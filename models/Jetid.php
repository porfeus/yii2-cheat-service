<?php

namespace app\models;

use Yii;
use app\components\ApiFunctions;
use app\models\Conf;
use app\models\Schedule;
use app\models\Users;

/**
 * This is the model class for table "jetid".
 *
 * @property int $ai № записи
 * @property int $id id настройки
 * @property int $uz ID юзера
 * @property string $conf Параметры шаблона в массиве
 * @property string $username Название настройки
 * @property string $refstat не нужно
 * @property string $ref не нужно
 * @property string $md5 не нужно
 * @property string $linkstat не нужно
 * @property double $cost Стоимость показа (мин)
 * @property double $costmax Стоимость показа (макс)
 * @property double $traf Бланс реалов на настройке
 * @property string $config_type Вид настройки
 * @property int $ch Статистика (час)
 * @property int $d Статистика (день)
 * @property int $oll Статистика (всего)
 * @property string $pokaz
 * @property int $last
 * @property int $set_pause Последний заказ помощи
 * @property int $notify_send
 * @property int $view В архиве (1 - нет, 0 - да)
 * @property string $t Последнее время сохранения
 */
class Jetid extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'jetid';
    }

    public function getUserInfo()
    {
        return $this->hasOne(Users::className(), ['id' => 'uz']);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'uz', 'conf', 'username', 'refstat', 'ref', 'md5', 'linkstat', 'cost', 'costmax', 'traf', 'config_type', 'ch', 'd', 'oll', 'pokaz', 'last', 'set_pause', 'notify_send'], 'required'],
            [['id', 'uz', 'ch', 'd', 'oll', 'pokaz', 'last', 'set_pause', 'notify_send'], 'integer'],
            [['conf', 'refstat', 'ref', 'config_type'], 'string'],
            [['cost', 'costmax', 'traf'], 'number'],
            [['t'], 'safe'],
            [['username', 'linkstat'], 'string', 'max' => 1024],
            [['md5'], 'string', 'max' => 64],
            [['view'], 'string', 'max' => 1],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'ai' => '№ записи',
            'id' => 'id настройки',
            'uz' => 'ID юзера',
            'conf' => 'Параметры шаблона в массиве',
            'username' => 'Название настройки',
            'refstat' => 'не нужно',
            'ref' => 'не нужно',
            'md5' => 'не нужно',
            'linkstat' => 'не нужно',
            'cost' => 'Стоимость показа (мин)',
            'costmax' => 'Стоимость показа (макс)',
            'traf' => 'Бланс реалов на настройке',
            'config_type' => 'Вид настройки',
            'ch' => 'Статистика (час)',
            'd' => 'Статистика (день)',
            'oll' => 'Статистика (всего)',
            'pokaz' => 'Pokaz',
            'last' => 'Last',
            'set_pause' => 'Последний заказ помощи',
            'notify_send' => 'Notify Send',
            'view' => 'В архиве (1 - нет, 0 - да)',
            't' => 'Последнее время сохранения',
        ];
    }

    public function afterDelete()
    {
       parent::afterDelete();
       $model = Schedule::findOne(['site_id' => $this->id]);
       if( $model ){
         $model->delete();
       }
    }

    public static function prepareUserOutputData($jetid){

      $userModel = Yii::$app->user->identity;

      $errort = '';
      $block_field = false;

      $rowH = $userModel->settings[$jetid] ?? null;

      $jetidModel = self::find()
        ->where(['id' => $jetid, 'uz' => Yii::$app->user->id ])
        ->one();

      if( empty($rowH) || empty($jetidModel) ){
        //Некорректный  id, данная настройка отсутствует в базе или удалена!
        return [
          'redirect' => ['api'],
        ];
      }


      $conf = $rowH['conf'];

      $schedule = Schedule::find()->where(['site_id' => $jetid, 'disabled' => 0])->one();

      $confq     = $rowH['conf'];
      $usernameq = $rowH['username'];
      $refq      = $rowH['ref'];
      $refstatq  = $rowH['refstat'];
      $md5q      = $rowH['md5'];
      $linkstatq = $rowH['linkstat'];
      $Dluee     = 1;

      parse_str($conf, $output);
      $output2   = array_map("\app\components\ApiFunctions::myicon2", $output);

      // conf = notexists=1&sites=
      if( isset($output2['notexists']) && $output2['notexists'] == '1' ){
        $jetid = (substr($jetid, 0, 1024));

        $rowH      = $jetidModel->toArray();
        $conf      = $rowH['conf'];
        $usernameq = $rowH['username'];
        $refq      = $rowH['ref'];
        $refstatq  = $rowH['refstat'];
        $md5q      = $rowH['md5'];
        $linkstatq = $rowH['linkstat'];
        $upidjet = intval($jetid);
        if ($upidjet != '' && $upidjet != '0') {
            $conf = (ApiFunctions::AjaxEditSiteSet($upidjet));

            $jetidModel->conf = $conf;

            if ( !$jetidModel->save(false) ) {
                //ошибка jetupd2d
                return [
                  'redirect' => ['api'],
                ];
            }
        }
        parse_str($conf, $output);
        $output2   = array_map("\app\components\ApiFunctions::myicon2", $output);
      }

      $outputist = explode(":", $output2['pst'] ?? '');
      $ptm2      = explode(":", $output2['ptm'] ?? '');
      $pac2      = explode(":", $output2['pac'] ?? '');
      $purl2     = explode("<!;#D>", $output2['purl'] ?? '');
      $otvet     = ApiFunctions::SiteStat($jetid);

      if (isset($otvet[$jetid]['notexists'])) {
          $otvet = ApiFunctions::CheckArchive($jetid, "check");
          if (isset($otvet[$jetid]['notexists']) || $off_recovery = true) {
              $Dluee = 0;
          } else {
              $otvet = ApiFunctions::CheckArchive($jetid, "recovery");
              if (isset($otvet[$jetid]['done']) && $otvet[$jetid]['done'] == 1) {
                  // обновляем старый ID на новый
                  $jetidModel->id = $otvet[$jetid]['id'];
                  $jetidModel->view = 1;
                  $jetidModel->save(false);

				          $dir = Yii::getAlias('@app/web/ID-S/ID/');
                  if (file_exists($dir . $jetid . ".js"))
                      rename($dir . $jetid . ".js", $dir . $otvet[$jetid]['id'] . ".js");


                  return [
                    'redirect' => ['edit', 'id' => $otvet[$jetid]['id'] ],
                  ];
              } else{
                  $Dluee = 0;
              }
          }
      }

      if ($Dluee == 0) {
          Yii::$app->session->setFlash('error', 'Ваша настройка находится в архиве, восстановите ее или удалите.<br/><a href="/delete/' . $jetid . '">Удалить</a>');
      } else {

          if ( !empty($schedule) ) {
              $block_field = true;
              $curr_hour   = unserialize($schedule->curr_hour);
              if ($curr_hour['day'] ?? '' != date("w")) {
                  $curr_hour = array(
                      "day" => date("w")
                  );
              }
              if ($curr_hour['hour'] ?? '' != date("G")) {
                  $curr_hour = array(
                      "hour" => date("G"),
                      "day" => $curr_hour['day']
                  );
                  $_pkhr     = unserialize($schedule->pkhr);
                  $_pktm     = unserialize($schedule->pktm);
                  $_pktml    = unserialize($schedule->pktml);


                  //"Разбираем" параметры
                  foreach ($_pkhr as $k => &$v) {
                      $v = explode(":", $v);
                      foreach ($v as $v1) {
                          if ($v1 > 0) {
                              if( empty($all_pokaz[$k]) ) $all_pokaz[$k] = 0;
                              $all_pokaz[$k] += $v1;
                          }
                      }
                  }
                  unset($v);
                  foreach ($_pkhr[1] as $vv) {
                      if ($vv == 0) {
                          $vv = 0.1;
                      }
                  }
                  //Показы в день
                  $curr_hour['pkm'] = $_pkhr[date("w")][24];
                  $curr_hour['pkh'] = $_pkhr[date("w")][date("G")];
                  //Показы в час
                  if (!$curr_hour['pkh']) {
                      for ($i = date("G"); $i >= 0; $i--) {
                          if ($_pkhr[date("w")][$i]) {
                              $curr_hour['pkh'] = $_pkhr[date("w")][$i];
                              break;
                          }
                      }
                  }
                  foreach ($_pktm as $k => &$v) {
                      $v = explode(":", $v);
                      foreach ($v as &$v1) {
                          $v1 = explode("-", $v1);
                      }
                  }

                  unset($v, $v1);


                  $tmlmin = array();
                  $tmlmax = array();

                  foreach ($_pktml as $k => $v) {
                      $v = explode(":", $v);
                      foreach ($v as $k1 => &$v1) {
                          $v1                 = explode(";", $v1);

                          if( !isset($tmlmin[$k]) )
                            $tmlmin[$k] = array();
                          if( !isset($tmlmin[$k][$k1]) )
                            $tmlmin[$k][$k1] = array();

                          if( !isset($tmlmax[$k]) )
                            $tmlmax[$k] = array();
                          if( !isset($tmlmax[$k][$k1]) )
                            $tmlmax[$k][$k1] = array();

                          $tmlmin[$k][$k1][0] = $v1[0];
                          $tmlmin[$k][$k1][1] = $v1[1];
                          $tmlmax[$k][$k1][0] = $v1[2];
                          $tmlmax[$k][$k1][1] = $v1[3];
                      }
                  }


                  if (!$tmlmin[date("w")][date("G")][0]) {
                      for ($i = date("G"); $i >= 0; $i--) {
                          if ($tmlmin[date("w")][$i][0]) {
                              $tmlmin[date("w")][date("G")][0] = $tmlmin[date("w")][$i][0];
                              break;
                          }
                      }
                  }


                  if (!$tmlmin[date("w")][date("G")][1]) {
                      for ($i = date("G"); $i >= 0; $i--) {
                          if ($tmlmin[date("w")][$i][1]) {
                              $tmlmin[date("w")][date("G")][1] = $tmlmin[date("w")][$i][1];
                              break;
                          }
                      }
                  }


                  if (!$tmlmax[date("w")][date("G")][0]) {
                      for ($i = date("G"); $i >= 0; $i--) {
                          if ($tmlmax[date("w")][$i][0]) {
                              $tmlmax[date("w")][date("G")][0] = $tmlmax[date("w")][$i][0];
                              break;
                          }
                      }
                  }


                  if (!$tmlmax[date("w")][date("G")][1]) {
                      for ($i = date("G"); $i >= 0; $i--) {
                          if ($tmlmax[date("w")][$i][1]) {
                              $tmlmax[date("w")][date("G")][1] = $tmlmax[date("w")][$i][1];
                              break;
                          }
                      }
                  }


                  $curr_hour['tml1'] = rand(intval($tmlmin[date("w")][date("G")][0]), intval($tmlmin[date("w")][date("G")][1]));
                  $curr_hour['tml2'] = rand(intval($tmlmax[date("w")][date("G")][0]), intval($tmlmax[date("w")][date("G")][1]));

                  $schedule->curr_hour = serialize($curr_hour);
                  $schedule->save(false);
              }
              $output2['pkm']  = $curr_hour['pkm'];
              $output2['pkh']  = $curr_hour['pkh'];
              $output2['tml1'] = $curr_hour['tml1'];
              $output2['tml2'] = $curr_hour['tml2'];


              //берем интервалы в часе и межинтервальные значения
              $_pktml = unserialize($schedule->pktml);
              $tmlmin = $tmlmax = array();
              foreach ($_pktml as $k => $v) {
                 $v = explode(":", $v);
                 foreach ($v as $k1 => $v1) {
                     $v1                 = explode(";", $v1);
                     $tmlmin[$k][$k1][0] = $v1[5];
                     $tmlmin[$k][$k1][1] = $v1[6];
                     $tmlmax[$k][$k1][0] = $v1[7];
                     $tmlmax[$k][$k1][1] = $v1[8];
                 }
              }

              if (!empty($tmlmin[date('w')][date('G')])) {
                $output2['tmlc1'] = (int) (($tmlmin[date('w')][date('G')][0] + $tmlmin[date('w')][date('G')][1]) / 2);
              }

              if (!empty($tmlmax[date('w')][date('G')])) {
                $output2['tmlc2'] = (int) (($tmlmax[date('w')][date('G')][0] + $tmlmax[date('w')][date('G')][1]) / 2);
              }
          } else {
              $block_field = false;
          }
      }

      return [
          'userModel' => $userModel,
          'errort' => $errort,
          'block_field' => $block_field,
          'jetidModel' => $jetidModel,
          'output2' => $output2,
          'jetid' => $jetid,
      ];
    }



    public static function prepareAdminOutputData($jetid){

      $jetidModel = self::find()->where(['id' => $jetid])->one();

      if( empty($jetidModel) ){
        return [
          'redirect' => ['index'],
        ];
      }

      $dir = Yii::getAlias('@app/web/ID-S/ID/')."$jetid.js";
      $rnd = rand();

      $IDuser = $jetidModel->uz;

      //Разбираем полученные параметры
      if ( !empty($_REQUEST['site']) ) {
          $SiteID                 = array();
          $SiteID[]               = $jetid;
          $Settings               = array();
          $Settings['site']       = $_REQUEST['site'] ?? '';
          $Settings['pkm']        = $_REQUEST['pkm'] ?? '';
          $Settings['pkh']        = $_REQUEST['pkh'] ?? '';
          $Settings['pkt']        = $_REQUEST['pkt'] ?? '';
          $Settings['pkt2']       = $_REQUEST['pkt2'] ?? '';
          $Settings['tml1']       = $_REQUEST['tml1'] ?? '';
          $Settings['tml2']       = $_REQUEST['tml2'] ?? '';
          $Settings['tmlc1']      = $_REQUEST['tmlc1'] ?? '';
          $Settings['tmlc2']      = $_REQUEST['tmlc2'] ?? '';
          $Settings['ssf']        = $_REQUEST['ssf'] ?? '';
          $Settings['ipc']        = $_REQUEST['ipc'] ?? '';
          $Settings['second']     = $_REQUEST['second'] ?? '';
          $Settings['proxy']      = $_REQUEST['proxy'] ?? '';
          $Settings['exact']      = $_REQUEST['exact'] ?? '';
          $Settings['li']         = $_REQUEST['li'] ?? '';
          $Settings['speed']      = $_REQUEST['speed'] ?? '';
          $Settings['highspeed']  = $_REQUEST['highspeed'] ?? '';
          $Settings['iphl']       = $_REQUEST['iphl'] ?? '';
          $Settings['iph']        = $_REQUEST['iph'] ?? '';
          $Settings['dayunickon'] = $_REQUEST['dayunickon'] ?? '';
          $Settings['dayunick']   = $_REQUEST['dayunick'] ?? '';
          $Settings['msf']        = $_REQUEST['msf'] ?? '';
          $Settings['ipex']       = $_REQUEST['ipex'] ?? '';
          $Settings['hideref']    = $_REQUEST['hideref'] ?? '';
          $Settings['hsf']        = $_REQUEST['hsf'] ?? '';
          $Settings['uh']         = $_REQUEST['uh'] ?? '';
          $Settings['dontstop']   = $_REQUEST['dontstop'] ?? '';
          $Settings['name']       = $_REQUEST['name'] ?? '';
          $Settings['fid']        = $_REQUEST['fid'] ?? '';
          $Settings['newfolder']  = '';
          $Settings['ggeo']       = $_REQUEST['ggeo'] ?? '';
          $Settings['rgeo']       = $_REQUEST['rgeo'] ?? '';
          if ( !empty($_REQUEST['geo']) ) {
            $Settings['geo'] = $_REQUEST['geo'];
          } else {
            $Settings['geo'] = '0';
          }
          $Settings['cref']     = $_REQUEST['cref'] ?? '';
          $Settings['prs']      = $_REQUEST['prs'] ?? '';
          $Settings['prstime']  = $_REQUEST['prstime'] ?? '';
          $Settings['prstime1'] = $_REQUEST['prstime1'] ?? '';
          $Settings['prsmin']   = $_REQUEST['prsmin'] ?? '';
          $Settings['prsmax']   = $_REQUEST['prsmax'] ?? '';
          $Settings['prtab']    = $_REQUEST['prtab'] ?? '';
          $Settings['prsref']   = $_REQUEST['prsref'] ?? '';
          $Settings['prstime2'] = $_REQUEST['prstime2'] ?? '';
          $Settings['prsrnd']   = $_REQUEST['prsrnd'] ?? '';
          $Settings['mouse']    = $_REQUEST['mouse'] ?? '';
          foreach ($_REQUEST['tms'] ?? [] as $k => $v) {
              $Settings['tms'][$k] = $v;
          }
          foreach ($_REQUEST['cmds'] ?? [] as $k => $v) {
              $Settings['cmds'][$k] = $v;
          }
          foreach ($_REQUEST['urls'] ?? [] as $k => $v) {
              $Settings['urls'][$k] = $v;
          }
          $Settings['sitetitle']  = $_REQUEST['sitetitle'] ?? '';
          $Settings['sitedesk']   = $_REQUEST['sitedesk'] ?? '';
          $Settings['catid']      = $_REQUEST['catid'] ?? '';
          $Settings['v2']         = $_REQUEST['v2'] ?? '';
          $Settings['v3']         = $_REQUEST['v3'] ?? '';
          $Settings['v4']         = $_REQUEST['v4'] ?? '';
          $Settings['v5']         = $_REQUEST['v5'] ?? '';
          $Settings['tmlrefresh'] = $_REQUEST['tmlrefresh'] ?? '';
          $upidjet                = intval($jetid);
          if ($upidjet != '' && $upidjet != '0') {
              $confq = (ApiFunctions::AjaxEditSiteSet($upidjet));
              parse_str($confq, $output);

      		//проверка галочек 1-5 в Jetswap
              $output2 = array_map("\app\components\ApiFunctions::myicon2", $output);
              if ($output2['v2'] ?? '' == '1') {
                  $Settings['v2'] = '1';
              }
              if ($output2['v3'] ?? '' == '1') {
                  $Settings['v3'] = '1';
              }
              if ($output2['v4'] ?? '' == '1') {
                  $Settings['v4'] = '1';
              }
              if ($output2['v5'] ?? '' == '1') {
                  $Settings['v5'] = '1';
              }
          }
          $edit = ApiFunctions::AjaxEditSiteEdit($SiteID, $Settings);

          if ($edit != '!') {
              //Ошибка сохранения настроек на jetswap;
              return [
                'redirect' => ['index'],
              ];
          }
      	//Обновляем полученные данные
      	else {
              sleep(1);
              $upidjet   = (intval(substr($jetid, 0, 1024)));
              $conf      = (ApiFunctions::AjaxEditSiteSet($upidjet));
              $refstatq  = (substr(trim($_REQUEST['crefstat'] ?? ''), 0, 1024));
              $linkstatq = (substr(trim($_REQUEST['clinkstat'] ?? ''), 0, 1024));
              $refqn     = ($_REQUEST['crefref'] ?? '');
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
              $jetidModel->username = $_POST['username_site'];

              if ( !$jetidModel->save(false) ) {
                  //ошибка jetupd8
                  return [
                    'redirect' => ['index'],
                  ];
              }

              return [
                'redirect' => ['updateset', 'edit' => 'ok', 'id' => $jetid ],
              ];
          }
      }
      if ( !empty($_REQUEST['upd']) ) {
          $upidjet = (substr($jetid, 0, 1024));

          $conf   = (ApiFunctions::AjaxEditSiteSet($upidjet));
          $idsc[] = $upidjet;
          $scost0 = ApiFunctions::AjaxEditSiteCost($idsc);
          parse_str($scost0, $outputq1);
          $scost2 = array_map("\app\components\ApiFunctions::myicon2", $outputq1);
          $scost  = ($scost2[$upidjet]);

          //получили из парсинга цену настройки
          parse_str($conf, $outputq);

          if( isset($outputq['notexists']) ){
            return [
              'redirect' => ['updateset', 'id' => $jetid ],
            ];
          }

          $output2q = array_map("\app\components\ApiFunctions::myicon2", $outputq);
          $pktfc    = $output2q['pkt'] ?? 0;
          $pktfc2   = $output2q['pkt2'] ?? 0;
          if ($pktfc2 == '0') {
              $pktfc2 = $pktfc;
          }

      	  //вычислили максималку
          $costmax = round(($pktfc2 / $pktfc) * $scost, 2);

          $jetidModel = self::find()->where(['id' => $upidjet])->one();
          $jetidModel->conf = $conf;
          $jetidModel->cost = $scost;
          $jetidModel->costmax = $costmax;

          if ( !$jetidModel->save(false) ) {
              //ошибка jetupd1
              return [
                'redirect' => ['index'],
              ];
          }

          return [
            'redirect' => ['updateset', 'vup' => 'ok', 'id' => $jetid ],
          ];
      }


      //Получаем параметры настройки на нашем сайте
      if ( !empty($jetid) ) {
          $jetid = (substr($jetid, 0, 1024));

          $rowH      = $jetidModel->toArray();
          $conf      = $rowH['conf'];
          $confq     = $rowH['conf'];
          $usernameq = $rowH['username'];
          $refq      = $rowH['ref'];
          $refstatq  = $rowH['refstat'];
          $md5q      = $rowH['md5'];
          $linkstatq = $rowH['linkstat'];
          $upidjet = intval($jetid);
          if ($upidjet != '' && $upidjet != '0') {
              $conf = (ApiFunctions::AjaxEditSiteSet($upidjet));

              $jetidModel->conf = $conf;

              if ( !$jetidModel->save(false) ) {
                  //ошибка jetupd2d
                  return [
                    'redirect' => ['index'],
                  ];
              }
          }
          parse_str($conf, $output);
          $output2   = array_map("\app\components\ApiFunctions::myicon2", $output);
          $outputist = explode(":", $output2['pst'] ?? '');
          $ptm2      = explode(":", $output2['ptm'] ?? '');
          $pac2      = explode(":", $output2['pac'] ?? '');
          $purl2     = explode("<!;#D>", $output2['purl'] ?? '');
      }

      return [
          'jetidModel' => $jetidModel,
          'output2' => $output2,
          'outputist' => $outputist,
          'ptm2' => $ptm2,
          'pac2' => $pac2,
          'purl2' => $purl2,
          'jetid' => $jetid,
          'usernameq' => $usernameq,
          'IDuser' => $IDuser,
          'dir' => $dir,
          'rnd' => $rnd,
      ];
    }
}
