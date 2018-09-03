<?php

namespace app\models;

use Yii;
use yii\base\Model;
use app\components\ApiFunctions;
use app\models\Jetid;

class ArchiveForm extends Model
{
  /**
   * Удаление настройки
   */
  public static function del($jetid){
    $ID = Yii::$app->user->id;

    $idModel = Jetid::find()
      ->where(['id' => $jetid, 'uz' => $ID])
      ->one();

    if ( empty($idModel) ) {
  		Yii::$app->session->addFlash('error', "Настройка не найдена");
  	}
  	else {
  		Yii::$app->session->addFlash('success','Настройка успешно удалена из системы');

      Logs::AddSettingLogs("Пользователь удалил настройку #{$jetid}");

      $idModel->delete();
  	}
  }

  /**
   * Восстановление настройки
   */
  public static function recovery($jetid, $user_id){
    $ID = $user_id;

    $idModel = Jetid::find()
      ->where(['id' => $jetid, 'uz' => $ID])
      ->one();

    $rowH = $idModel->toArray();
    $conf = $rowH['conf'];
    $confq = $rowH['conf'];
    $usernameq = $rowH['username'];
    $refq = $rowH['ref'];
    $refstatq = $rowH['refstat'];
    $md5q = $rowH['md5'];
    $linkstatq = $rowH['linkstat'];
    parse_str($conf, $output);
    $output2 = array_map("\app\components\ApiFunctions::myicon2", $output);
    $outputist = explode(":", $output2['pst'] ?? '');
    $ptm2 = explode(":", $output2['ptm'] ?? '');
    $pac2 = explode(":", $output2['pac'] ?? '');
    $purl2 = explode("<!;#D>", $output2['purl'] ?? '');
    $otvet = ApiFunctions::SiteStat($jetid);
    if (isset($otvet[$jetid]['notexists'])) {
      $otvet = ApiFunctions::CheckArchive($jetid, "check");
      if (isset($otvet[$jetid]['notexists'])) {
        Yii::$app->session->addFlash('error', 'К сожалению восстановить из архива данный шаблон не получится, нажмите на ссылку ниже для удаления шаблона из аккаунта.<br/>
        '.((\Yii::$app->controller->module->id == 'admin')? '<a href="/admin/users/deleteset?id=' . $jetid . '&user_id=' . $user_id . '">Удалить</a>':'<a href="/delete/' . $jetid . '">Удалить</a>').'');
      }
      else {
        $otvet = ApiFunctions::CheckArchive($jetid, "recovery");
        if (isset($otvet[$jetid]['done']) && $otvet[$jetid]['done'] == 1) {

          // обновляем старый ID на новый
          $idModel->id = $otvet[$jetid]['id'];
          $idModel->view = 1;
          $idModel->save(false);

          $idDir = Yii::getAlias('@app/web/ID-S/ID/');
          if (file_exists( $idDir."$jetid.js" )){
            rename($idDir."$jetid.js", $idDir.$otvet[$jetid]['id'].".js");
          }
          Yii::$app->session->addFlash('success',"Настройка была восстановлена. Новый ID настройки: {$otvet[$jetid]['id']}");

          Logs::AddSettingLogs("Пользователь восстановил настройку #{$jetid}. Новый ID настройки: {$otvet[$jetid]['id']}");
        }
        else{
          Yii::$app->session->addFlash('error', 'К сожалению восстановить из архива данный шаблон не получится, нажмите на ссылку ниже для удаления шаблона из аккаунта.<br/>
          '.((\Yii::$app->controller->module->id == 'admin')? '<a href="/admin/users/deleteset?id=' . $jetid . '&user_id=' . $user_id . '">Удалить</a>':'<a href="/delete/' . $jetid . '">Удалить</a>').'');

          Logs::AddSettingLogs("Пользователь попытался восстановить настройку #{$jetid}");
        }
      }
    }
  }
}
