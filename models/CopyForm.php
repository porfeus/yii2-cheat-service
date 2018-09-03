<?php

namespace app\models;

use Yii;
use app\components\ApiFunctions;
use app\models\Conf;
use app\models\Schedule;
use app\models\Users;
use app\models\Logs;

class CopyForm extends Jetid
{

  public static function prepareOutputData($jetid){

    $jetidModel = self::find()->where(['id' => $jetid])->one();

    if( empty($jetidModel) ){
      return;
    }
    //--------

    //Получаем параметры настройки на нашем сайте
    $rowH      = $jetidModel->toArray();
    $conf      = $rowH['conf'];
    $confq     = $rowH['conf'];
    $usernameq = $rowH['username'];
    $refq      = $rowH['ref'];
    $refstatq  = $rowH['refstat'];
    $md5q      = $rowH['md5'];
    $linkstatq = $rowH['linkstat'];
    parse_str($conf, $output);
    $output2   = array_map("\app\components\ApiFunctions::myicon2", $output);
    $outputist = explode(":", $output2['pst'] ?? '');
    $ptm2      = explode(":", $output2['ptm'] ?? '');
    $pac2      = explode(":", $output2['pac'] ?? '');
    $purl2     = explode("<!;#D>", $output2['purl'] ?? '');
    //--------

    //Разбираем полученные параметры
    $SiteID                 = array();
    //$SiteID[]               = $jetid;
    $Settings               = $output2;
    $Settings['site']       = $output2['url'] ?? '';
    $Settings['dayunickon'] = $output2['dayunick'] ?? '';
    $Settings['newfolder']  = '';
    $Settings['geo']      = $output2['geo'] ?? '0';
    $Settings['prs']      = $output2['prs'] ?? '0';
    $Settings['prstime']  = $outputist['0'] ?? '';
    $Settings['prstime1'] = $outputist[1] ?? '';
    $Settings['prsmin']   = $outputist[2] ?? '';
    $Settings['prsmax']   = $outputist[3] ?? '';
    $Settings['prsref']   = $outputist[4] ?? '';
    $Settings['prstime2'] = $outputist[5] ?? '';
    $Settings['prsrnd']   = $outputist[6] ?? '';
    foreach ($ptm2 as $k => $v) {
        $Settings['tms'][$k] = $v;
    }
    foreach ($ptm2 as $k => $v) {
        $Settings['cmds'][$k] = $pac2[$k];
    }
    foreach ($ptm2 as $k => $v) {
        $Settings['urls'][$k] = $purl2[$k];
    }
    $upidjet                = intval($jetid);
    $confq = (ApiFunctions::AjaxEditSiteSet($upidjet));
    parse_str($confq, $output);
    //--------

    //Добавляем настройку
    $newId = ApiFunctions::SiteAdd($Settings);
    if ( empty($newId) ) {
        //Ошибка сохранения настроек на jetswap;
        Logs::AddSettingLogs("Не удалось копирование настройки ID: $jetid пользователя ID: {$jetidModel->uz}", $jetidModel->uz);
        return;
    }
    //--------

    else {
      //Заполняем настройку
      $jetidModelData = $jetidModel->toArray();
      $jetidModel = new self();
      $jetidModel->id = $newId;

      foreach($jetidModelData as $key=>$val){
        if( $key == 'ai' || $key == 'id' ) continue;
        $jetidModel->{$key} = $val;
      }

      sleep(1);
      $upidjet   = intval($newId);
      $conf      = (ApiFunctions::AjaxEditSiteSet($upidjet));
      $refstatq  = (substr(trim($output2['crefstat'] ?? ''), 0, 1024));
      $linkstatq = (substr(trim($output2['clinkstat'] ?? ''), 0, 1024));
      $refqn     = ($output2['crefref'] ?? '');
      $md5qn     = md5($refqn);
      $idsc[]    = $upidjet;
      $scost0    = ApiFunctions::AjaxEditSiteCost($idsc);
      parse_str($scost0, $outputq1);
      $scost2 = array_map("\app\components\ApiFunctions::myicon2", $outputq1);
      $scost  = $scost2[$upidjet];
      //получили из парсинга цену настройки
      parse_str($conf, $outputq);
      $output2q = array_map("\app\components\ApiFunctions::myicon2", $outputq);
      $pktfc    = $output2q['pkt'] ?? '1';
      $pktfc2   = $output2q['pkt2'] ?? '0';
      if ($pktfc2 == '0') {
          $pktfc2 = $pktfc;
      }
      $costmax = round(($pktfc2 / $pktfc) * $scost, 2); //вычислили максималку

      $jetidModel->conf = $conf;
      $jetidModel->refstat = $refstatq;
      $jetidModel->ref = $refqn;
      $jetidModel->md5 = $md5qn;
      $jetidModel->linkstat = $linkstatq;
      $jetidModel->cost = $scost;
      $jetidModel->costmax = $costmax;
      $jetidModel->username = 'копия  id: '.$jetid;

      //поправляем записываемые данные
      $jetidModel->t = date("Y-m-d H:i:s");
      $jetidModel->set_pause = 0;
      $jetidModel->last = 0;
      $jetidModel->oll = 0;
      $jetidModel->d = 0;
      $jetidModel->ch = 0;
      $jetidModel->traf = 0;

      if ( !$jetidModel->save(false) ) {
          //ошибка jetupd8
          Logs::AddSettingLogs("Не удалось копирование настройки ID: $jetid пользователя ID: {$jetidModel->uz} - невозможно создать запись в БД", $jetidModel->uz);
          return;
      }

      Logs::AddSettingLogs("Настройка ID: $jetid пользователя ID: {$jetidModel->uz} успешно скопирована. Новый ID: $newId", $jetidModel->uz);
      //--------

      //Копируем файл
      $idDir = Yii::getAlias('@app/web/ID-S/ID/');
      if (file_exists( $idDir."$jetid.js" )){
        copy($idDir."$jetid.js", $idDir."$newId.js");
      }
      //--------

      //Копируем расписание
      $scheduleModel = Schedule::findOne(['site_id' => $jetid]);
      if( !empty($scheduleModel) ){
        $newScheduleModel = new Schedule();
        foreach( $scheduleModel->toArray() as $key=>$val ){
          if( $key == 'id' || $key == 'site_id' ) continue;
          $newScheduleModel->{$key} = $val;
        }
        $newScheduleModel->site_id = $newId;
        if( !$newScheduleModel->disabled ){
          $newScheduleModel->last_upd = date('Y-m-d H:i:s');
        }
        $newScheduleModel->save(false);
      }
      //--------
    }
  }
}
