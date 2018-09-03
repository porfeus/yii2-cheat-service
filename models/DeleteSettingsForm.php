<?php

namespace app\models;

use Yii;
use yii\base\Model;
use app\components\ApiFunctions;

class DeleteSettingsForm extends Model
{

    static $logs = [];
    static $forPaymentsInfo = [];
    static $totalSum = 0;

    static function logsAdd($log){
      array_push(self::$logs, $log);
    }

    static function logsClear(){
      self::$logs = [];
    }

    static public function delete($id, $ID){
      self::logsClear();

      $userModel = Users::findOne($ID);
      $balans_before = $userModel->balans;
      $trafbalans_before = $userModel->trafbalans;

      self::$forPaymentsInfo = [];
      self::$totalSum = 0;

      $ids = explode(',', $id);
      foreach( $ids as $id ){
        self::deleteOne($id, $ID).'<br />';
      }

      $userModel = Users::findOne($ID);

      // записываем в историю транзакций
      if( !empty(self::$forPaymentsInfo) ){
        Paymentsinfo::add([
          'cost' => self::$totalSum,
          'note' => "Удаление настроек, настройки id ".implode(", ", self::$forPaymentsInfo)." реалов. Было рублей: {$balans_before}, стало: {$userModel->balans}. Было реалов: {$trafbalans_before}, стало: {$userModel->trafbalans}",
        ], Paymentsinfo::TRAFBALANS_DEL_PLUS);
		
		//проверка на мошенничество
		Antifraud::add([
		  'type' => Antifraud::TRAFBALANS_DEL_PLUS,
		  'cost' => self::$totalSum,
		  'note' => "Удаление настроек, настройки id ".implode(", ", self::$forPaymentsInfo)." реалов. Было реалов: {$trafbalans_before}, стало: {$userModel->trafbalans}",
		]);
      }

      Logs::AddSettingLogs(implode('<br />', self::$logs)."<br />Инфо: <br />ID пользователя: $ID");
    }

    static function deleteOne($id, $ID){

      $userModel = Users::find()->where(['id' => $ID])->one();

  		// $ID = ID юзера в системе
  		// переданный id Настройки $id

      $jetid = Jetid::find()
        ->where(['id' => $id, 'uz' => $ID])
        ->one();


      if( empty($jetid) || empty($userModel) ){
        // настройка не была найдена в базе
        self::logsAdd("ID пользователя: $ID,  попытался войти в чужую настройку, ошибка..Код операции: Delete-012");

        return "<font color='red' size='3'>Настройка с id: $id не найдена в Вашем управлении или ее не существует!</font><br/>";
      }

      $id_user = $jetid->id; //есть ли настройка у юзера, ложим если нашли в $id_user

      $credite_id = ApiFunctions::SiteStatForDelete($id_user);
      $credite_id_result = $credite_id[$id_user]['cr'] ?? 0; //if set is not exists
      $credite_id_result = round($credite_id_result, 2);

      //считаем правильно отнимаемую сумму для истории транзакций
      if( $userModel->trafbalans + $credite_id_result >= 0 ){
        self::$totalSum += round($credite_id_result);
      }else{
        self::$totalSum += $userModel->trafbalans;
      }

      $userModel->trafbalans = $userModel->trafbalans + $credite_id_result;
      if( $userModel->trafbalans < 0 ) $userModel->trafbalans = 0;
      $result_update = $userModel->save(false);

      self::$forPaymentsInfo[] = "$id_user:$credite_id_result";

      // проверяем в цикле корректность удаления
      for ($schet_s = 1; $schet_s <= 5; $schet_s++) {

        // передаем настройку на удаление
        $delete_id = ApiFunctions::SiteDelete($id_user);

        // получаем ответ
        $delete_result = $delete_id[$id_user]['done'] ?? 1; //if set is not exists
        if ($delete_result == '1') {
          break;
        }
        else {
          sleep(1);
        }
      }

      // локальная проверка файла настроек
      $messages = '';
      $dir = Yii::getAlias('@app/web/ID-S/ID/')."$id_user.js";
      if ($result_update) {
        $messages.= "<font color='green' size='3'>На баланс возвращено " . htmlspecialchars($credite_id_result) . " реалов.</font><br/>";

        self::logsAdd("файл настройки id: $id найден, вернули после удаления кредитов $credite_id_result Код операции: Delete-00158");
      }

      if (file_exists($dir)) {

        // echo "<font color='green' size='3'>Файл настройки: $id_user найден!</font><br/>";

        $del = unlink($dir);

        self::logsAdd("файл настройки id: $id найден, запрос на удаление! Код операции: Delete-001");

        if ($del) {

          // echo "<font color='green' size='3'>Файл настройки $id_user был успешно удален!</font><br/>";

          self::logsAdd("файл настройки id: $id удален! Код операции: Delete-002");
        }
      }
      else {

        // echo "<font color='red' size='3'>Ошибка файл настройки: $id_user не найден! </font><br/>";

        self::logsAdd("файл настройки id: $id не существовал  Код операции: Delete-003");
      }

      // ответ api
      // успешно

      if ($delete_result == '1') {
        $messages.= "<font color='green' size='3'>Настройка с id: $id успешно удалена из API</font><br/>";

        self::logsAdd("Настройка успешно удалена из API id: $id Код операции: Delete-004");
      }

      // не успешно (например в архиве, или не существует)

      if ($delete_result != '1') {
        $messages.= "<font color='red' size='3'>Ошибка удаления настройки с id: $id из API (возможно она в архиве или уже удалена!)</font><br/>";

        self::logsAdd("Удалить настройку из API id: $id  не получилось  Код операции: Delete-005");
      }

      // удаляем всю строку

      Yii::$app->session->addFlash('success', $messages);



      if (  $jetid->delete()) {

        // 5 Конфигурация настройки была очищена из базы
        // echo "<font color='green' size='3'>Конфигурация настройки удалена</font><br/>";

        self::logsAdd(" удалил настройку id: $id Код операции: Delete-010");
      }
      else {

        // 6 ошибка
        // echo "<font color='red' size='3'>Ошибка очистки конфигурации настройки</font><br/>";

        self::logsAdd("НЕ удалил настройку id: $id Ошибка Код операции: Delete-011");
      }
    }

    static function noConfirm(){
      Yii::$app->session->addFlash('error', "Вы не согласились с условиями удаления (поставьте галочку)!");

      $ID = Yii::$app->user->id;

      Logs::AddSettingLogs("ID пользователя: $ID,  не согласился с правилами при удалении настройки Код операции: Delete-013");
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
}
