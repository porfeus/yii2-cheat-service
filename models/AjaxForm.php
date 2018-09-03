<?php

namespace app\models;

use Yii;
use yii\base\Model;
use app\components\ApiFunctions;
use app\components\Curl;
use app\models\Logs;
use app\models\Jetid;
use app\models\Conf;
use app\models\Tikets;

class AjaxForm extends Model
{
  /**
   * Получаем статистику IP
   */
  public static function getips(){
    $ID = Yii::$app->user->id;

    $id = intval(Yii::$app->request->post('site'));
    $result = ApiFunctions::SiteIP(array(
      $id
    ));
    if (!isset($result[$id]) || isset($result[$id]['notexists']) ) {
      $result[$id] = array();
    }

    $ips = @implode("<br />", $result[$id]);
    if( empty($ips) ){
      $ips = "Записи отсутствуют";
    }

    die(json_encode(array(
      "title" => "IP-адреса посетителей Вашего сайта за 24 часа",
      "text" => "<center>Всего посетителей сегодня: " . intval(count($result[$id])) . " <br /><br />
                  <div style=\"max-height: 300px; overflow-y: scroll\">" . $ips . "</div><br/><small>Статистика отражает все IP адреса, даже те что не были учтены системой при работе (не оплаченные Вами показы)</small></center>"
    )));
  }

  /**
   * Очистка статистики
   */
  public static function clearStat(){
    $ID = Yii::$app->user->id;

    if (!ApiFunctions::checkTime("clear_stat")) die(ApiFunctions::getTimeLimitError($ID, "clear_stat"));
    $id = trim(Yii::$app->request->post('ids') , ",");
    if ($id) {
      $id = explode(",", $id);
      $id = array_map("intval", $id);
    }

    if (count($id) && $id[0]) {
      $totalt = "";
      $errort = "";
      foreach($id as $i) {
        $otvet = ApiFunctions::SiteStat($i);
        if (isset($otvet[$i]['notexists'])) {
          $t = 'Возникла ошибка по площадке ' . $i . ', площадка не доступна, обратитесь в тех поддержку' . "\n";
          $errort.= $t;
          $totalt.= $t;
          continue;
        }
        $totalt.= 'Статистика по площадке ' . $i . ' успешно сброшена!' . "\n";
      }
    }

    if( !empty($errort) ){
      $errort = $totalt;
    }

    $minus = 0;
    if (count($id) && $id[0]) {
      $idsall = array();
      $rowsModels = Jetid::find()->where(['IN', 'id', $id])->andWhere(['uz' => $ID])->all();
      foreach($rowsModels as $row) {
        if ($row->oll < 0) $row->oll = 0;
        $row->pokaz = $row->pokaz - $row->oll;
        $row->ch = 0;
        $row->d = 0;
        $row->oll = 0;
        $row->save(false);
        $minus+= $row->oll;
        $idsall[] = $row->id;
      }

      $curl = new Curl();
      $curl->setCookieFile("cookie.txt");
      $curl->setCookieJar("cookie.txt");
      $curl->setUserAgent("Mozilla/5.0 Windows NT 6.2 WOW64 rv:34.0 Gecko/20100101 Firefox/34.0");
      $curl->post('http://go.jetswap.com/account?mode=auth', array(
        "user" => Conf::getParams('jetlogin') ,
        "pss" => Conf::getParams('jetpass') ,
        "ipskip" => 1
      ));
      $txt = $curl->post("http://go.jetswap.com/account?mode=auth", array(
        "evc" => Conf::getParams('evc') ,
        "fnp" => Conf::getParams('fnp')
      ));
      $curl->get("http://go.jetswap.com/account?mode=url&conf=1&cmd=killstat&idst=" . implode(":", $idsall));
      Logs::AddSettingLogs("Заказана очистка статистики юзером ID: $ID Настройки: " . implode(":", $idsall), $ID);
      ApiFunctions::setLastRequest($ID, "clear_stat");
      if (isset($errort) && $errort != "") die($errort);
    }
    else {
      die("0");
    }
  }

  /**
   * Запрос изменения настроек
   */
  public static function setStat($context){
    $ID = Yii::$app->user->id;

    $id = intval(Yii::$app->request->post('id'));
    $data = Jetid::find()->where(['id' => $id, 'uz' => $ID])->asArray()->one();
    if (!$data['id']) {
      die("Нет доступа!");
    }
    elseif ($data['set_pause'] + (3600 * Conf::getParams('set_pause')) > time()) {

      if( Conf::getParams('set_pause') >= 1 ){
        $timeLeft = ceil(($data['set_pause'] + (3600 * Conf::getParams('set_pause')) - time()) / 3600). " ч.";
      }else{
        $timeLeft = ceil(($data['set_pause'] + (3600 * Conf::getParams('set_pause')) - time()) / 60). " мин.";
      }

      die("Заказ изменения настроек возможен через ~" . $timeLeft );
    }
    else {
      return $context->renderPartial('change_settings', ['id' => $id]);
    }
  }

  /**
   * Операции с реалами
   */
  public static function credOp(){
    $ID = Yii::$app->user->id;
    $userModel = Yii::$app->user->identity;
    $balans_before = $userModel->balans;
    $trafbalans_before = $userModel->trafbalans;

    if (!ApiFunctions::checkTime("change_balance"))
      die(ApiFunctions::getTimeLimitError($ID, "change_balance"));

    $views = intval(Yii::$app->request->post('views'));
    $traf = Yii::$app->user->identity->trafbalans;

    $ids = trim(Yii::$app->request->post('ids'), ",");
    $ids = explode(",", $ids);
    $ids = array_map("intval", $ids);

    $myids = Jetid::find()
      ->where(['uz' => $ID])
      ->andWhere(['IN', 'id', $ids])
      ->asArray()
      ->all();

    $logs = array();
    $alerts = array();

    switch (Yii::$app->request->post('op')) {

        // Пополнение кредитов
      case "views_up":
        $errort = "";
        if (!$views) {
          die("Введите количество реалов!");
        }

        if (count($ids) * $views > $traf) {
          die("У вас недостаточно реалов!");
        }

        $totalSum = 0;
        $forPaymentsInfo = [];

        $_REQUEST['psum1'] = $views;
        foreach ($myids as $row) {
          $_REQUEST['pid1'] = $row['id'];
          $pid10 = array();
          $pid10[] = (substr(intval($_REQUEST['pid1'] ?? '') , 0, 1024));
          $pid1 = (substr(intval($_REQUEST['pid1'] ?? '') , 0, 1024));
          $psum1 = (substr(intval($_REQUEST['psum1'] ?? '') , 0, 1024));
          $rowModel = Jetid::find()
            ->where(['id' => $pid1, 'uz' => $ID])
            ->one();
          $rowHq = $rowModel->toArray();
          $uz = $rowHq['uz'];
          $traf = $rowHq['traf'];
          $cost = $rowHq['cost'];
          $costmax = $rowHq['costmax'];
          $psum = ceil($psum1 * $costmax);
          $re = ApiFunctions::SitePoints($pid10, 1, $views);
          $otv = $re[$pid1]['done'] ?? '';

          // если баланс успешно пополнен
          if ($otv == 1) {
            if ($psum < 0) $psum = 0;
            if ($psum1 < 0) $psum1 = 0;

            // обновляем статистику настроек
            $rowModel->traf += $views;
            $rowModel->pokaz += $views;
            $rowModel->save(false);

            // обновляем общий баланс юзера
            $userModel->trafbalans -= $views;
            $userModel->save(false);

            $totalSum += $views;
            $forPaymentsInfo[] = "$pid1:$views";

            // пишем в ЛОГ
            $logs[] = "Массовая операция по реалам у юзера ID: $ID (пополнение) настройка $pid1:$views реалов";

            $errort.= $pid1.' - Успешно!' . "\n";
          }

          // иначе проверяем ошибки
          else {
            $nomer = $re[$pid1]['error'] ?? '';
            if ($nomer[0][0] ?? '' == '1') {
              $errort.= $pid1.' - Данного кол-ва трафика в данный момент нет в системе, через некоторое время оно появится, администрация вкурсе.' . "\n" . 'Попробуйте пополнить настройку на меньшее кол-во трафика.' . "\n";
              $today = date("Y-m-d H:i:s");

              $tiketsModel = new Tikets();
              $tiketsModel->title = 'ROBOT';
              $tiketsModel->message = "Не хватает количество трафика. Реалы: {$views}, ID: $pid1, USERID: $ID";
              $tiketsModel->save(false);

              Yii::$app->mailer->compose()
                ->setFrom([
                  Yii::$app->params['sendFrom']['email'] => 'Новый тикет в системе'
                ])
                ->setReplyTo(Yii::$app->params['sendFrom']['email'])
                ->setTo( Conf::getParams('adminmail') )
                ->setSubject('New ticket site.ru')
                ->setHtmlBody(
                  "New ticket site.ru"
                )
                ->send();
            }
            elseif (isset($re[$pid1]['notexists'])){
              $errort.= 'Возникла ошибка по площадке ' . $pid1 .
                        ', площадка не доступна, обратитесь в тех поддержку' . "\n";
            }
            else {
              $errort.= $pid1.' - Возникла неизвестная ошибка, обратитесь в тех поддержку' . "\n";
            }
          }

          $alerts[] = $errort;
          $errort = "";
        }

        // записываем в историю транзакций
        if( !empty($forPaymentsInfo) ){
          Paymentsinfo::add([
            'cost' => $totalSum,
            'note' => "Массовая операция пополнения реалов, настройки id ".implode(", ", $forPaymentsInfo)." реалов. Было рублей: {$balans_before}, стало: {$userModel->balans}. Было реалов: {$trafbalans_before}, стало: {$userModel->trafbalans}",
          ], Paymentsinfo::TRAFBALANS_MULTIMANUAL_MINUS);
		  
		  //проверка на мошенничество
			Antifraud::add([
			  'type' => Antifraud::TRAFBALANS_MULTIMANUAL_MINUS,
			  'cost' => $totalSum,
			  'note' => "Массовая операция пополнения реалов, настройки id ".implode(", ", $forPaymentsInfo)." реалов. Было реалов: {$trafbalans_before}, стало: {$userModel->trafbalans}",
			]);
        }

        Logs::AddBalansLogs(implode('<br>', $logs));
        if( !empty($alerts) ){
          die(implode("", $alerts));
        }
        echo "1";
        break;

        // Снятие реалов
      case "views_dn":
        $errort = "";
        if ( Yii::$app->request->post('all') ) {
          $views = 100000000000;
        }

        if (!$views) {
          die("Введите количество реалов!");
        }

        $totalSum = 0;
        $forPaymentsInfo = [];

        // снять все
        $_REQUEST['psum2'] = $views;
        foreach ($myids as $row) {
          $_REQUEST['pid2'] = $row['id'];
          $pid10 = array();

          // параметры настроек

          $pid10[] = (substr(intval($_REQUEST['pid2'] ?? '') , 0, 1024));
          $pid1 = (substr(intval($_REQUEST['pid2'] ?? '') , 0, 1024));
          $psum1 = (substr(intval($_REQUEST['psum2'] ?? '') , 0, 1024));
          $rowModel = Jetid::find()
            ->where(['id' => $pid1, 'uz' => $ID])
            ->one();
          $rowHq = $rowModel->toArray();
          $uz = $rowHq['uz'];
          $traf = $rowHq['traf'];
          $cost = $rowHq['cost'];
          $pokaz = $rowHq['pokaz'] - $rowHq['oll'];
          $costmax = $rowHq['costmax'];
          $otvet = ApiFunctions::SiteStat($pid10);

          // настройки не существует
          if (isset($otvet[$pid1]['notexists'])) {
            $errort.= 'Возникла ошибка по площадке ' . $row['id'] . ', площадка не доступна, обратитесь в тех поддержку' . "\n";

            $alerts[] = $errort;
            $errort = "";
            continue;
          }

          $cr = $otvet[$pid1]['cr'] ?? '';
          if ($psum1 > $pokaz) {
            $psum1 = $pokaz;
          }

          if ( Yii::$app->request->post('all') ) $views = $cr;

          // заглушка - если реалов <0, но больше 1 ставим принудительно их в ноль
          // обнаруженный баг
          if ($views < 1) {
            $views = 0;
          }

          $psum = floor($psum1 * $costmax);
          if ($cr >= $views && $traf >= $views) {
            $re = ApiFunctions::SitePoints($pid10, 2, $views);

            // ответ успешный, сняли

            $otv = $re[$pid1]['done'] ?? '';
            $cr2 = round($cr - $views, 2);
            if ($otv == '1') {
              if ($cr2 < 0) $cr2 = 0;
              if ($psum1 < 0) $psum1 = 0;

              // обновляет трафик
              $rowModel->traf = $cr2;
              $rowModel->pokaz -= $views;
              $rowModel->save(false);

              // обновляет общий баланс юзера
              $userModel->trafbalans += $views;
              $userModel->save(false);

              $totalSum += $views;
              $forPaymentsInfo[] = "$pid1:$views";

              // пишем в ЛОГ
              $logs[] = "Массовая операция по реалам у юзера ID: $ID (снятие) настройка $pid1:$views реалов";

              $errort.= $pid1.' - Успешно!' . "\n";
            }
            else {
              $errort.= $pid1." - Не делайте запросы слишком часто!\n";
            }
          }
          else {
            $errort.= "Площадка {$row['id']}: Возможно часть реалов уже открутилась, попробуйте изменить число реалов или попробуйте позже\n";
          }

          $alerts[] = $errort;
          $errort = "";
        }

        // записываем в историю транзакций
        if( !empty($forPaymentsInfo) ){
          $reportType = Paymentsinfo::TRAFBALANS_MULTIMANUAL_PLUS;
          if( Yii::$app->request->post('all') ){
            $reportType = Paymentsinfo::TRAFBALANS_MULTIAUTO_PLUS;
          }

          Paymentsinfo::add([
            'cost' => $totalSum,
            'note' => "Массовая операция снятия реалов, настройки id ".implode(", ", $forPaymentsInfo)." реалов. Было рублей: {$balans_before}, стало: {$userModel->balans}. Было реалов: {$trafbalans_before}, стало: {$userModel->trafbalans}",
          ], $reportType);
		  
		  //проверка на мошенничество
			Antifraud::add([
			  'type' => $reportType,
			  'cost' => $totalSum,
			  'note' => "Массовая операция снятия реалов, настройки id ".implode(", ", $forPaymentsInfo)." реалов. Было реалов: {$trafbalans_before}, стало: {$userModel->trafbalans}",
			]);
        }

        Logs::AddBalansLogs(implode('<br>', $logs));
        if( !empty($alerts) ){
          die(implode("", $alerts));
        }
        echo "1";
        break;
      }

    ApiFunctions::setLastRequest($ID, "change_balance");
  }
}
