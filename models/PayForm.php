<?php

namespace app\models;

use Yii;
use yii\base\Model;
use app\models\Logs;
use app\models\Users;
use app\models\Paymentsinfo;

class PayForm extends Model
{

    public $ammount;
    public $type = 'webmoney'; // Способ пополнения по умолчанию

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
          [['ammount', 'type'], 'required'],
          [['ammount'], 'integer', 'min'=> Conf::getParams('min_pay_summ'), 'tooSmall' => 'Минимум к пополнению 100р.'],
          [['type'], 'in', 'range' => ['robokassa', 'webmoney', 'interkassa', 'sprypay'], 'message' => 'Выбранный способ пополнения не найден.'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'ammount' => 'Сумма пополнения',
            'type' => 'Способ пополнения',
        ];
    }

    /**
     * Add errors in alert.
     *
     */
    public function addError($attribute, $error = '')
    {
        parent::addError($attribute, $error);

        $flashes = Yii::$app->session->getAllFlashes();
        if( empty($flashes['error']) || !in_array($error, $flashes['error']) ){
          Yii::$app->session->addFlash('error', $error);
        }
    }


    /**
     * Submit pay form
     *
     */
    public function printForm(){
      switch( $this->type ){
        case "robokassa":
          $form = $this->robokassaForm();
        break;

        case "webmoney":
          $form = $this->webmoneyForm();
        break;

        case "interkassa":
          $form = $this->interkassaForm();
        break;

        case "sprypay":
          $form = $this->sprypayForm();
        break;
      }
      exit($form);
    }


    /**
     * Check payment
     *
     */
    static public function checkPayment($type){
      if( !count($_REQUEST) ){
        throw new \yii\web\BadRequestHttpException(Yii::t('app', 'Bad Reques.'));
      }

      switch( $type ){
        case "robokassa":
          self::checkRobokassaPayment();
        break;

        case "webmoney":
          self::checkWebmoneyPayment();
        break;

        case "interkassa":
          self::checkInterkassaPayment();
        break;

        case "sprypay":
          self::checkSprypayPayment();
        break;
      }
    }


    /**
     * Show result
     *
     */
    static public function showResult(){

      $idt3 = '';

      //платежи от робокассы
      if (!empty($_REQUEST['InvId'])) {
          $idt4       = '';
          $idt3       = substr(trim($_REQUEST['InvId']), 0, 1024);
      }

      //платежи от интеркассы
      if (!empty($_REQUEST['ik_pm_no'])) {
          $idt4       = '';
          $idt3       = substr(trim($_REQUEST['ik_pm_no']), 0, 1024);
      }

      //платежи от SpryPay
      if (!empty($_REQUEST['spShopPaymentId'])) {
          $idt4       = '';
          $idt3       = substr(trim($_REQUEST['spShopPaymentId']), 0, 1024);
      }

      //платежи от WebMoney
      if (!empty($_REQUEST['WMInvId'])) {
          $idt4       = '';
          $idt3       = substr(trim($_REQUEST['WMInvId']), 0, 1024);
      }

      //получаем детали платежа
      $paymentModel = Paymentsinfo::findOne(['payment' => $idt3]);

      if (empty($idt3) || !$paymentModel) {
        Yii::$app->session->addFlash('error', 'Платеж не найден');
        return false;
      }

      if( $paymentModel['status'] != 'OK-PAY' ){
        return Yii::$app->session->addFlash('info', 'Платеж еще не зачислен, пожалуйста обновите баланс через 3-5 минут.');
      }

      return Yii::$app->session->addFlash('success', 'Баланс пополнен на ' . $paymentModel['cost'] . 'р.');
    }


    /**
     * Event on success payment.
     *
     */
    public static function onSuccessPay($out_summ, $shp_ZAKAZ, $inv_id, $paymentType){

      //отправляем письмо
      Yii::$app->mailer->compose()
        ->setFrom([
          Yii::$app->params['sendFrom']['email'] => 'Информация'
        ])
        ->setReplyTo(Yii::$app->params['sendFrom']['email'])
        ->setTo( Conf::getParams('adminmail') )
        ->setSubject('Система Go-Ip.ru')
        ->setHtmlBody(
          "Баланс в системе Go-Ip.ru был пополнен через $paymentType одним из пользователей на " .
          round($out_summ, 2) . " руб. ID пользователя: $shp_ZAKAZ  Номер оплаты: $inv_id"
        )
        ->send();

      //пишем лог
      Logs::AddTransactionLogs("ID пользователя: $shp_ZAKAZ пополнил основной баланс рублей на " .
        round($out_summ, 2) . " руб. с помощью $paymentType");
    }


    /**
     * Подготавливаем форму robokassa
     *
     */
    public function robokassaForm(){

      // присвоить айди юзера
      $shp_ZAKAZ = Yii::$app->user->id;

      //обнуляем номер платежа
      $inv_id = rand(111111111, 999999999);

      //сумма платежа
      $out_summ = $this->ammount;

      //идентификатор магазина
      $mrh_login = Conf::getParams('robokassa_login');

      //первый секретный пароль
      $mrh_pass1 = Conf::getParams('robokassa_pass1');

      //формируем подпись
      $md5  = md5("$mrh_login:$out_summ:$inv_id:$mrh_pass1:Shp_ZAKAZ=$shp_ZAKAZ");

      //сохраняем детали платежа
      $paymentModel = new Paymentsinfo();
      $paymentModel->payment = $inv_id;
      $paymentModel->cost = $out_summ;
      $paymentModel->metod = "robokassa";
      $paymentModel->type = Paymentsinfo::BALANS_BUY;
      $paymentModel->save(false);


      return "
      <div style='display:none'>
      <form name=\"forma\" action='https://auth.robokassa.ru/Merchant/Index.aspx' method=POST>
      <input type=\"hidden\" name=\"OutSum\"  id=\"sum\" class=\"up\" value='".$out_summ."'/>
      <input type=\"hidden\"  id=\"nom\" class=\"up\" name=\"Shp_ZAKAZ\" value='".$shp_ZAKAZ."'/>
      <input type=hidden name=MrchLogin value='".$mrh_login."'>
      <input type=hidden name=InvId value='".$inv_id."'>
      <input type=hidden name=Desc value='Оплата заказа'>
      <input type=hidden name=SignatureValue id=\"sign\" value='".$md5."'>
      <input type=hidden name=Culture value='ru'>
        <!--<input type=hidden name=IsTest value='1'>-->
      <input type=button value='Оплатить' onClick='pr()'>
      </form>
      </div>
      <script>
      document.forma.submit();
      </script>
      ";
    }


    /**
     * Проверяем платеж robokassa
     *
     */
    public static function checkRobokassaPayment(){

      //передаем данные
      $out_summ  = $_REQUEST["OutSum"];
      $inv_id    = $_REQUEST["InvId"];
      $crc       = $_REQUEST["SignatureValue"];
      $shp_ZAKAZ = $_REQUEST['Shp_ZAKAZ'];

      //второй секретный пароль
      $mrh_pass2 = Conf::getParams('robokassa_pass2');

      //сравниваем подпись
      $my_crc = strtoupper(md5("$out_summ:$inv_id:$mrh_pass2:Shp_ZAKAZ=$shp_ZAKAZ"));
      if (strtoupper($my_crc) != strtoupper($crc)) {
          $ok = FALSE;
          //Ключи подписи не совпали. Мы отклонили платеж. Обратитесь в тех. поддержку
          exit("error");
      }

      //проверка на минимальный платеж
      if ($out_summ < Conf::getParams('min_pay_summ')) {
          //Ошибка платежа
          exit("error");
      }

      //номер оплаты
      $inv_id    = substr($inv_id, 0, 1024);

      //сумма платежа
      $out_summ  = substr($out_summ, 0, 1024);

      //ID юзера для зачисления суммы
      $shp_ZAKAZ = substr($shp_ZAKAZ, 0, 1024);

      //получаем детали платежа
      $paymentModel = Paymentsinfo::findOne(['payment' => $inv_id]);

      if (!$paymentModel) {
          //платеж уже зачислялся
          exit("error");
      }

      if ($paymentModel['status'] == 'OK-PAY') {
          //Повторный платеж
          exit("error");
      }

      //используем транзакцию для гарантированного обновления двух таблиц
      $transaction = Yii::$app->db->beginTransaction();
      try{
        //пополняем баланс юзера
        $userModel = Users::findOne($paymentModel->userid);
        $balans_before = $userModel->balans;
        $trafbalans_before = $userModel->trafbalans;
        $userModel->balans += $out_summ;
        $userModel->pay = 1;
        $userModel->save(false);

        //помечаем платеж зачисленным
        $paymentModel->status = 'OK-PAY';
        $paymentModel->note = "Пополнение личного счета в системе. Было рублей: {$balans_before}, стало: {$userModel->balans}. Было реалов: {$trafbalans_before}, стало: {$userModel->trafbalans}";
        $paymentModel->save(false);
        //---
        $transaction->commit();
      }catch (Exception $e){
        $transaction->rollback();

        //Ошибка зачисления средств или пометки платежа зачисленным
        exit("error");
      }

      // вывод платежной системе успешности зачисления
      echo 'OK' . $inv_id;

  	  //отправляем письмо и пишем лог
      self::onSuccessPay($out_summ, $shp_ZAKAZ, $inv_id, 'RoboKassa');
    }


    /**
     * Подготавливаем форму webmoney
     *
     */
    public function webmoneyForm(){

      // присвоить айди юзера
      $shp_ZAKAZ = Yii::$app->user->id;

      //случайный номер оплаты
      $rand_order = rand(111111111, 999999999);

      //итоговая сумма оплаты
      $out_summ = $this->ammount;

      //wmr-кошелек
      $wmr = Conf::getParams('webmoney_r');

      //сохраняем детали платежа
      $paymentModel = new Paymentsinfo();
      $paymentModel->payment = $rand_order;
      $paymentModel->cost = $out_summ;
      $paymentModel->metod = "webmoney";
      $paymentModel->type = Paymentsinfo::BALANS_BUY;
      $paymentModel->save(false);

      //отправляем на оплату
      return "
      <div>
      <form method='post' name='forma' action='https://merchant.webmoney.ru/lmi/payment.asp'>
      <input type='hidden' name='LMI_PAYMENT_DESC_BASE64'
      value='".base64_encode('Пополнение баланса в системе site.ru, ID пользователя: '.$shp_ZAKAZ)."' />
      <input type='hidden' name='LMI_PAYEE_PURSE' id='wm' value='".$wmr."' />
      <input type=\"hidden\"  id=\"nom\" class=\"up\" name=\"Shp_ZAKAZ\" value='".$shp_ZAKAZ."'/>
      <input type=hidden name=WMInvId value='".$rand_order."'>
      <input type='hidden' name='LMI_PAYMENT_AMOUNT' value='".$out_summ."' />
      </form>
      </div>
      <script>
      document.forma.submit();
      </script>
      ";
    }


    /**
     * Проверяем платеж webmoney
     *
     */
    public static function checkWebmoneyPayment(){

      //сумма платежа
      $out_summ = $_REQUEST['LMI_PAYMENT_AMOUNT'] ?? '';

      //номер заказа
      $inv_id = $_REQUEST["WMInvId"] ?? '';

      //ID юзера для зачисления платежа
      $shp_ZAKAZ = $_REQUEST['Shp_ZAKAZ'] ?? '';


      //проверяем кошелек получателя
      if ($_REQUEST['LMI_PREREQUEST'] ?? '' == 1) {
        if (trim($_REQUEST['LMI_PAYEE_PURSE']) != Conf::getParams('webmoney_r') ) {
          echo "ERR: НЕВЕРНЫЙ КОШЕЛЕК ПОЛУЧАТЕЛЯ " . $_REQUEST['LMI_PAYEE_PURSE'];
          exit("error");
        }
    	//разрешаем платеж
        echo "YES";
      }

      //проверка на минимальный платеж в магазине
      if ($out_summ < Conf::getParams('min_pay_summ') ) {

          //Ошибка платежа
          exit("error");
      }

    	//секретные ключи
      $secret_key    = Conf::getParams('webmoney_key');

      //получаем данные

    	//номер платежа
      $inv_id    = substr($inv_id, 0, 1024);

    	//сумма платежа
      $out_summ  = substr($out_summ, 0, 1024);

    	//ID юзера для зачисления платежа
      $shp_ZAKAZ = substr($shp_ZAKAZ, 0, 1024);

      //получаем детали платежа
      $paymentModel = Paymentsinfo::findOne(['payment' => $inv_id]);

      if (!$paymentModel) {
          //платеж уже зачислялся
          exit("error");
      }

      if ($paymentModel['status'] == 'OK-PAY') {
          //Повторный платеж
          exit("error");

      }

      //используем транзакцию для гарантированного обновления двух таблиц
      $transaction = Yii::$app->db->beginTransaction();
      try{
        //пополняем баланс юзера
        $userModel = Users::findOne($paymentModel->userid);
        $balans_before = $userModel->balans;
        $trafbalans_before = $userModel->trafbalans;
        $userModel->balans += $out_summ;
        $userModel->pay = 1;
        $userModel->save(false);

        //помечаем платеж зачисленным
        $paymentModel->status = 'OK-PAY';
        $paymentModel->note = "Пополнение личного счета в системе. Было рублей: {$balans_before}, стало: {$userModel->balans}. Было реалов: {$trafbalans_before}, стало: {$userModel->trafbalans}";
        $paymentModel->save(false);
        //---
        $transaction->commit();
      }catch (Exception $e){
        $transaction->rollback();

        //Ошибка зачисления средств или пометки платежа зачисленным
        exit("error");
      }

      // вывод платежной системе успешности зачисления
      echo 'OK' . $inv_id;

  	  //отправляем письмо и пишем лог
      self::onSuccessPay($out_summ, $shp_ZAKAZ, $inv_id, 'WebMoney');
    }


    /**
     * Подготавливаем форму interkassa
     *
     */
    public function interkassaForm(){

      // присвоить логин или айди юзера
      $shp_ZAKAZ = Yii::$app->user->id;

      //случайный номер оплаты
      $rand_order = rand(111111111, 999999999);

      //сумма оплаты
      $out_summ = $this->ammount;

      //задаем кодировку текста
      $txt1251 = "Пополнение баланса";

      //id в системе
      $ik_co_id = Conf::getParams('interkassa_id');

      //сохраняем детали платежа
      $paymentModel = new Paymentsinfo();
      $paymentModel->payment = $rand_order;
      $paymentModel->cost = $out_summ;
      $paymentModel->metod = "interkassa";
      $paymentModel->type = Paymentsinfo::BALANS_BUY;
      $paymentModel->save(false);

      return '
      <div style="display:none">
      <form name="payment" method="post" action="https://sci.interkassa.com/" accept-charset="UTF-8">
      	<input type="hidden" name="ik_x_Shp_ZAKAZ" value="'.$shp_ZAKAZ.'"/>
      	<input type="hidden" name="ik_am" value="'.$out_summ.'" />
      	<input type="hidden" name="ik_co_id" value="'.$ik_co_id.'" />
      	<input type="hidden" name="ik_pm_no" value="'.$rand_order.'" />
      	<input type="hidden" name="ik_desc" value="'.$txt1251.'" />
      	<input type="submit" value="Pay" onClick="pr()">
      </form>
      </div>
      <script>
      document.payment.submit();
      </script>
      ';
    }


    /**
     * Проверяем платеж interkassa
     *
     */
    public static function checkInterkassaPayment(){

      // HTTP параметры
      $out_summ  = $_REQUEST["ik_am"];
      $inv_id    = $_REQUEST["ik_pm_no"];
      $crc       = $_REQUEST["ik_sign"];
      $shp_ZAKAZ = $_REQUEST['ik_x_Shp_ZAKAZ'];

      // формирование подписи
      $shop_id    = Conf::getParams('interkassa_id');
      $test_key   = Conf::getParams('interkassa_test_key');
      $secret_key = Conf::getParams('interkassa_secret_key');

      if ($_REQUEST['ik_co_id'] != $shop_id) {
          //Не корректный номер магазина. Мы отклонили платеж. Обратитесь в тех. поддержку
          exit("error");
      }

      //проверка на тестовый платеж
      $ik_key = ($_REQUEST['ik_pw_via'] == 'test_interkassa_test_xts') ? $test_key : $secret_key;

      //разбираем массив
      $data = array();
      foreach ($_REQUEST as $key => $value) {
        if (!preg_match('/ik_/', $key))
          continue;
        $data[$key] = $value;
      }

      $ik_sign = $data['ik_sign'];
      unset($data['ik_sign']);

      ksort($data, SORT_STRING);
      array_push($data, $ik_key);

      $signString = implode(':', $data);

      //формируем подпись
      $sign = base64_encode(md5($signString, true));

      // проверка подписи
      if ($sign === $ik_sign && $data['ik_inv_st'] == 'success') {
      } else {
        //Ключи подписи не совпали. Мы отклонили платеж. Обратитесь в тех. поддержку
        exit("error");
      }

      //проверка на минимальный платеж
      if ($out_summ < Conf::getParams('min_pay_summ')) {
          //Ошибка платежа
          exit("error");
      }

      //номер платежа
      $inv_id = substr($inv_id, 0, 1024);

      //сумма зачисления
      $out_summ  = substr($out_summ, 0, 1024);

      //ID юзера кому зачислять платеж
      $shp_ZAKAZ = substr($shp_ZAKAZ, 0, 1024);

      //получаем детали платежа
      $paymentModel = Paymentsinfo::findOne(['payment' => $inv_id]);

      if (!$paymentModel) {
          //платеж уже зачислялся
          exit("error");
      }

      if ($paymentModel['status'] == 'OK-PAY') {
          //Повторный платеж
          exit("error");
      }

      //используем транзакцию для гарантированного обновления двух таблиц
      $transaction = Yii::$app->db->beginTransaction();
      try{
        //пополняем баланс юзера
        $userModel = Users::findOne($paymentModel->userid);
        $balans_before = $userModel->balans;
        $trafbalans_before = $userModel->trafbalans;
        $userModel->balans += $out_summ;
        $userModel->pay = 1;
        $userModel->save(false);

        //помечаем платеж зачисленным
        $paymentModel->status = 'OK-PAY';
        $paymentModel->note = "Пополнение личного счета в системе. Было рублей: {$balans_before}, стало: {$userModel->balans}. Было реалов: {$trafbalans_before}, стало: {$userModel->trafbalans}";
        $paymentModel->save(false);
        //---
        $transaction->commit();
      }catch (Exception $e){
        $transaction->rollback();

        //Ошибка зачисления средств или пометки платежа зачисленным
        exit("error");
      }

      //платеж зачислен
      echo 'OK' . $inv_id;

  	  //отправляем письмо и пишем лог
      self::onSuccessPay($out_summ, $shp_ZAKAZ, $inv_id, 'InterKassa');
    }


    /**
     * Подготавливаем форму sprypay
     *
     */
    public function sprypayForm(){

      // присвоить логин или айди юзера
      $shp_ZAKAZ = Yii::$app->user->id;

      //сумма платежа
      $out_summ = $this->ammount;

      //текст сообщения
      $txt1251 = "Пополнение баланса";

      //случайный номер заказа
      $rand_order = rand(111111111, 999999999);

      //id магазина
      $sprypay_id = Conf::getParams('sprypay_id');

      //сохраняем детали платежа
      $paymentModel = new Paymentsinfo();
      $paymentModel->payment = $rand_order;
      $paymentModel->cost = $out_summ;
      $paymentModel->metod = "sprypay";
      $paymentModel->type = Paymentsinfo::BALANS_BUY;
      $paymentModel->save(false);

      return '
      <div style="display:none">
      <form name="payment" action="http://sprypay.ru/sppi/" method="POST" accept-charset="utf-8">
        <input type="hidden" name="spShopId" value="'.$sprypay_id.'">
        <input type="hidden" name="spUserData_Shp_ZAKAZ" value="'.$shp_ZAKAZ.'">
        <input type="hidden" name="spShopPaymentId" value="'.$rand_order.'">
        <input type="hidden" name="spCurrency" value="rur">
        <input type="hidden" name="spPurpose" value="'.$txt1251.'">
        <input type="hidden" name="spAmount" value="'.$out_summ.'">
        <input type="submit" id="p" value="оплатить">
      </form>
      </div>
      <script>
      document.payment.submit();
      </script>';
    }


    /**
     * Проверяем платеж sprypay
     *
     */
    public static function checkSprypayPayment(){

      // HTTP parameters:
      $out_summ  = $_REQUEST["spAmount"];
      $inv_id    = $_REQUEST["spShopPaymentId"];
      $shp_ZAKAZ = $_REQUEST['spUserData_Shp_ZAKAZ'];

      // формирование подписи
      $spQueryFields = array(
          'spPaymentId',
          'spShopId',
          'spShopPaymentId',
          'spBalanceAmount',
          'spAmount',
          'spCurrency',
          'spCustomerEmail',
          'spPurpose',
          'spPaymentSystemId',
          'spPaymentSystemAmount',
          'spPaymentSystemPaymentId',
          'spEnrollDateTime',
          'spBalanceCurrency',
          'spHashString',
          'spTakeFeeFrom',
          'spTakeFeeFromPercent',
          'spCustomerPhone'
      );

      // проверим, что все они присутутвуют в запросе
      foreach ($spQueryFields as $spFieldName)
          if (!isset($_REQUEST[$spFieldName])) {
              //error в запросе с данными платежа отсутствует параметр `$spFieldName`
              exit("error 1");
          }

      // ваш секретный ключ, задается в настройках магазина
      $yourSecretKeyString = Conf::getParams('sprypay_secret_key');

      // получим контрольную подпись
      $localHashString = md5($_REQUEST['spPaymentId'] . $_REQUEST['spShopId'] . $_REQUEST['spShopPaymentId'] . $_REQUEST['spBalanceAmount'] . $_REQUEST['spAmount'] . $_REQUEST['spCurrency'] . $_REQUEST['spCustomerEmail'] . $_REQUEST['spPurpose'] . $_REQUEST['spPaymentSystemId'] .
      $_REQUEST['spPaymentSystemAmount'] .
      $_REQUEST['spPaymentSystemPaymentId'] .
      $_REQUEST['spEnrollDateTime'] .
      $yourSecretKeyString);

      if ($localHashString != $_REQUEST['spHashString']) {
          //Не сошлась подпись
          exit("error 2");
      }


      //проверка на минимальный платеж
      if ($out_summ < Conf::getParams('min_pay_summ')) {
          //Ошибка платежа
          exit("error 3");
      }

      //номер платежа
      $inv_id    = substr($inv_id, 0, 1024);

      //сумма платежа
      $out_summ  = substr($out_summ, 0, 1024);

      //ID юзера кому зачислять
      $shp_ZAKAZ = substr($shp_ZAKAZ, 0, 1024);

      //получаем детали платежа
      $paymentModel = Paymentsinfo::findOne(['payment' => $inv_id]);

      if (!$paymentModel) {
          //платеж уже зачислялся
          exit("error 4");
      }

      if ($paymentModel['status'] == 'OK-PAY') {
          //Повторный платеж
          exit("error 5");
      }

      //используем транзакцию для гарантированного обновления двух таблиц
      $transaction = Yii::$app->db->beginTransaction();
      try{
        //пополняем баланс юзера
        $userModel = Users::findOne($paymentModel->userid);
        $balans_before = $userModel->balans;
        $trafbalans_before = $userModel->trafbalans;
        $userModel->balans += $out_summ;
        $userModel->pay = 1;
        $userModel->save(false);

        //помечаем платеж зачисленным
        $paymentModel->status = 'OK-PAY';
        $paymentModel->note = "Пополнение личного счета в системе. Было рублей: {$balans_before}, стало: {$userModel->balans}. Было реалов: {$trafbalans_before}, стало: {$userModel->trafbalans}";
        $paymentModel->save(false);
        //---
        $transaction->commit();
      }catch (Exception $e){
        $transaction->rollback();

        //Ошибка зачисления средств или пометки платежа зачисленным
        exit("error 6");
      }


      // зачисляем платеж
      echo 'OK';

  	  //отправляем письмо и пишем лог
      self::onSuccessPay($out_summ, $shp_ZAKAZ, $inv_id, 'SpryPay');
    }
}
