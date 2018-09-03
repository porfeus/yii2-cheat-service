<?php

namespace app\components;

use Yii;
use app\models\ApiRequests;
use app\models\Conf;
use app\models\Logs;
use app\models\Jetid;
use app\models\Tikets;
use app\models\Users;
use app\models\Paymentsinfo;
use app\models\Antifraud;

class ApiFunctions {
  /**
   * Проверяем возможность послать запрос в апи (не чаще определенного времени)
   * @param string $type тип запроса
   * @return bool
   */
  public static function checkTime($type)
  {
      $ID = Yii::$app->user->id;

      $last_request = self::getLastRequest($ID, $type);

      if ($last_request) {
          $time_limit = self::getTimeLimit($ID, $type);

          if ($last_request + $time_limit <= time()) {
              ApiRequests::deleteAll(['type' => $type, 'user_id' => $ID]);
              return true;
          } else {
              return false;
          }
      }

      return true;
  }

  /**
   * Получаем дату последнего доступа к апи
   * @param $user_id
   * @param $type
   */
  public static function getLastRequest ($user_id, $type) {
      $model = ApiRequests::find()
        ->where(['user_id' => $user_id, 'type' => $type])
        ->orderBy('last_request DESC')
        ->one();

      if( empty($model) ) return '';
      return $model->last_request;
  }


  /**
   * get time limit
   */
  public static function getTimeLimit($user_id, $type)
  {
      $query = Yii::$app->db->createCommand("SELECT COALESCE(u.time, l.default) AS time_limit
          FROM time_limits l
          LEFT JOIN time_limits_users u ON u.limit_id = l.id AND u.user_id = :user_id
          WHERE l.`type` = :type")
      ->bindValue(':user_id', $user_id)
      ->bindValue(':type', $type)
      ->queryOne();

      return $query["time_limit"];
  }


  /**
   * convert win-1251 to utf-8
   */
  public static function myicon2($str) {
    if (is_array($str)) {
      $str3 = array_map("self::myicon2", $str);
      return $str3;
    }
    else {
      $str2 = iconv('WINDOWS-1251', 'UTF-8', $str);
      return $str2;
    }
  }


  /**
   * апи jettswap для подключения
   */
  public static function SiteApiRequest($action,$prm)
  {
      $api=Conf::getParams('apijet');
      $key=Conf::getParams('skeyjet');
      $api_config = Yii::$app->params['api_config'];
      $ch = curl_init($api_config["url"]);
      $t=time();
      $code=md5("$api::$action::$t::5::$key");
      $resa=array();
      $resa["PAY_INTERFACE"]=5;
      $resa["PAY_API"]=$api;
      $resa["PAY_TIME"]=$t;
      $resa["PAY_CODE"]=$code;
      $resa["PAY_ACTION"]=$action;
      $resa=array_merge($prm,$resa);
      curl_setopt($ch, CURLOPT_TIMEOUT, 10);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_POSTFIELDS, self::ArrayToUrl($resa));
      $Result=curl_exec($ch);
      $Result=substr($Result,1,strlen($Result)-1);
      curl_close($ch);
      parse_str($Result,$ar);
      return $ar;
  }


  /**
   * апи jettswap для подключения (v2)
   */
  public static function AjaxEditSiteApiRequest($action, $prm, & $edit = FALSE) {
      $apijet=Conf::getParams('apijet');
      $skeyjet=Conf::getParams('skeyjet');
      $api_config = Yii::$app->params['api_config'];
      $ch = curl_init($api_config["url"]);
      $t = time();
      $code = md5("$apijet::$action::$t::5::$skeyjet");
      $resa = array();
      $resa["PAY_INTERFACE"] = 5;
      $resa["PAY_API"] = $apijet;
      $resa["PAY_TIME"] = $t;
      $resa["PAY_CODE"] = $code;
      $resa["PAY_ACTION"] = $action;
      $resa = array_merge($prm, $resa);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_POSTFIELDS, self::ArrayToUrl($resa));
      $Result1 = trim(curl_exec($ch));
      $edit = $Result1 {0};
      $Result = substr($Result1, 1, strlen($Result1) - 1);
      curl_close($ch);
      return $Result;
  }


  /**
   * апи jettswap для подключения (v3)
   */
  public static function AjaxApiSiteApiRequest($action, $prm, $type = null) {
      $apijet=Conf::getParams('apijet');
      $skeyjet=Conf::getParams('skeyjet');
      $ID=Yii::$app->user->id;
      $api_config = Yii::$app->params['api_config'];
      $ch = curl_init($api_config["url"]);
      $t = time();
      $code = md5("$apijet::$action::$t::5::$skeyjet");
      $resa = array();
      $resa["PAY_INTERFACE"] = 5;
      $resa["PAY_API"] = $apijet;
      $resa["PAY_TIME"] = $t;
      $resa["PAY_CODE"] = $code;
      $resa["PAY_ACTION"] = $action;
      $resa = array_merge($prm, $resa);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_POSTFIELDS, self::ArrayToUrl($resa));
      $Result = trim(curl_exec($ch));
      curl_close($ch);
      if (is_numeric($Result)) {
          if (in_array((int) $Result, array_keys($api_config["errors"]))) {
              $error = $api_config["errors"][$Result];
          } else {
              $error = "внутренняя ошибка системы";
          }
          return array(
              "success" => false,
              "error_code" => $Result,
              "error" => $error
          );
      }
      if (empty($Result)) {
          return array(
              "success" => false,
              "error_code" => 0,
              "error" => "Сервер очень перегружен, попробуйте через пару секунд..."
          );
      }
      if (!empty($type)) {
          self::setLastRequest($ID, $type);
      }
      return array(
          "success" => true,
          "result" => substr($Result, 1, strlen($Result) - 1)
      );
  }

  /**
   * Обновляем дату последнего доступа к апи
   * @param $user_id
   * @param $type
   */
  public static function setLastRequest ($user_id, $type) {
      $addModel = new ApiRequests();
      $addModel->user_id = $user_id;
      $addModel->type = $type;
      $addModel->last_request = time();
      $addModel->save(false);
  }


  /**
   * array to url
   */
  public static function ArrayToUrl($arr, $name="")
  {
      $rt = '';
      $a=array_keys($arr);
      for($x=0;$x<count($arr);$x++)
      {
      if($x>0)$rt.="&";
      if(is_array($arr[$a[$x]]))$rt.=self::ArrayToUrl($arr[$a[$x]], (strlen($name) ? "{$name}[" : "") . urlencode($a[$x]) . (strlen($name) ? "]" : ""));
      else $rt.=(strlen($name) ? "{$name}[" : "") . urlencode($a[$x]) . (strlen($name) ? "]" : "") . "=" . urlencode($arr[$a[$x]]);
      }
      return $rt;
  }


  /**
   * запрос статистики у настройки
   */
  public static function SiteStat($SiteID)
  {
      $SiteID = (array)$SiteID;
      $Result = self::SiteApiRequest("stat",array("idst"=>join(":",$SiteID)));
      return $Result;
  }

  /**
   * запрос статистики у настройки (v2)
   */
  public static function AjaxEditSiteStat($SiteID) {
      return self::AjaxEditSiteApiRequest("stat", array("idst" => join(":", $SiteID)));
  }


  /**
   * запрос статистики у настройки (v3)
   */
  public static function AjaxApiSiteStat($SiteID) {
      return self::AjaxApiSiteApiRequest("stat", array("idst" => join(":", $SiteID)));
  }


  /**
   * check archive
   */
  public static function CheckArchive($SiteID, $state = "recovery")
  {
      $SiteID = (array)$SiteID;
      $Result = self::SiteApiRequest("archive",array("op" => ($state == "recovery"? 1: 3), "idst"=>join(":",$SiteID)));
      return $Result;
  }

  /**
   * Функция SiteIP - используется для получения списка IP-адресов посетителей сайта.
   * Параметры:
   * $SiteID - массив идентификаторов нужных сайтов.
   * Выдает массив с результатами операции для каждого ID сайта, переданного в массиве $SiteID, содержащими IP-адреса посетителей этого сайта. Подробнее о значениях выходного массива вы можете узнать в документации.
   */
  public static function SiteIP($SiteID)
  {
      $Result = self::SiteApiRequest("statip",array("idst"=>join(":",$SiteID)));
      return $Result;
  }


  /**
   * get time limit error
   */
  public static function getTimeLimitError($user_id, $type)
  {
      $limit_info = self::getTimeLimitInfo($user_id, $type);
      return "Вы не можете {$limit_info["description"]} чаще чем раз в {$limit_info["time_limit"]} сек. Повторите попытку позже.";
  }


  /**
   * get time limit info
   */
  public static function getTimeLimitInfo($user_id, $type)
  {

      $query = Yii::$app->db->createCommand("SELECT l.description, COALESCE(u.time, l.`default`) AS time_limit
              FROM time_limits l
              LEFT JOIN time_limits_users u ON u.limit_id = l.id AND u.user_id = :user_id
              WHERE type = :type")
      ->bindValue(':user_id', $user_id)
      ->bindValue(':type', $type)
      ->queryOne();

      return $query;
  }


  /**
   * save start
   */
  public static function save_start($avt, $ID, $counter = 1) {
      $json = array();
      try {
          //Logs::AddSettingLogs('Инициализация сохранения/изменения настройки # ' . $_REQUEST['id'] . ' попытка №' . $counter, $ID);

          if ($avt != "OK")
              throw new \Exception("Вы не авторизованы", 1);

          $user_id = $ID;
          if ($counter == 1 && !self::checkTime("change_settings"))
              throw new \Exception(self::getTimeLimitError($user_id, "change_settings"), 1);
          //сохраняем в базу
          self::save($user_id, $counter);
          $json['success'] = true;
          $json["result"] = "Настройки ID:  ". $_REQUEST['id'] ." успешно сохранены";
      } catch (\Exception $e) {
          //обработка ошибок ajax
          if ($e->getCode() != 0 || $counter > 5) {
              Logs::AddSettingLogs('Сохранение/изменение настройки # ' . $_REQUEST['id']  . ' закончилось ошибкой: "' . $e->getMessage() . '"', $ID);
              $json["error"] = $e->getMessage();
              $json["success"] = false;
          } else {
              $counter++;
              Logs::AddSettingLogs('Не удалось сохранить настройки # ' . $_REQUEST['id']  . ', ошибка: "' . $e->getMessage() . '"  попытка №' . $counter, $ID);
              $json = self::save_start($avt, $ID, $counter);
          }
      }
      return $json;
  }


  /**
   * Функция используется для получения состояния счета сайта, пополнения и снятия со счета сайта кредитов и VIP-показов, настройки автопополнения счета.
   * Параметры:
   * $SiteID - массив идентификаторов нужных сайтов
   * $OpID - номер операции: 0 - получение состояния счета, 1 - пополнение счета, 2 - снятие со счета, 3 - настройка автопополнения счета. При отсутствии значения этого параметра будет выполнен запрос состояния счетов сайтов.
   * $Cr - количество кредитов для добавления/снятия (обязательный параметр при $OpID>0)
   * $VIP - количество VIP-показов для добавления/снятия (обязательный параметр при $OpID>0)
   * $MinCr - минимальное количество кредитов на счету сайта для добавления $Cr кредитов на счет (обязательный параметр при $OpID=3)
   * $MinVIP - минимальное количество VIP-показов на счету сайта для добавления $VIP показов на счет (обязательный параметр при $OpID=3)
   * Выдает массив с результатами операции для каждого ID сайта, переданного в массиве $SiteID. Формат массива смотрите в документации.
   */
  public static function SitePoints($SiteID,$OpID=0,$Cr=0,$VIP=0,$MinCr=0,$MinVIP=0)
  {
      $tmp=array();
      if(count($SiteID)>0)$tmp["idst"]=join(":",$SiteID);
      $tmp["op"]=$OpID;
      $tmp["ac"]=$Cr;
      $tmp["av"]=$VIP;
      $tmp["mc"]=$MinCr;
      $tmp["mv"]=$MinVIP;
      $Result=self::SiteApiRequest("points",$tmp);
      return $Result;
  }


  /**
   * SitePoints (v2)
   */
  public static function AjaxApiSitePoints($SiteID, $OpID = 0, $Cr = 0, $VIP = 0, $MinCr = 0, $MinVIP = 0, $type = null) {
      $tmp = array();
      if (count($SiteID) > 0)
          $tmp["idst"] = join(":", $SiteID);
      $tmp["op"] = $OpID;
      $tmp["ac"] = $Cr;
      $tmp["av"] = $VIP;
      $tmp["mc"] = $MinCr;
      $tmp["mv"] = $MinVIP;
      return self::AjaxApiSiteApiRequest("points", $tmp, $type);
  }


  /**
   * SitePoints (v3)
   */
  public static function AjaxEditSitePoints($SiteID, $OpID = 0, $Cr = 0, $VIP = 0, $MinCr = 0, $MinVIP = 0) {
      $tmp = array();
      if (count($SiteID) > 0) {
          $tmp["idst"] = join(":", $SiteID);
      }
      $tmp["op"] = $OpID;
      $tmp["ac"] = $Cr;
      $tmp["av"] = $VIP;
      $tmp["mc"] = $MinCr;
      $tmp["mv"] = $MinVIP;
      $Result = self::AjaxEditSiteApiRequest("points", $tmp);
      return $Result;
  }

  /**
   * получение параметров настроек
   */
  public static function SiteSet($SiteID) {
      return self::SiteApiRequest("set", array("fill" => $SiteID));
  }


  /**
   * SiteSet (v2)
   */
  public static function AjaxApiSiteSet($SiteID) {
      return self::AjaxApiSiteApiRequest("set", array("fill" => $SiteID));
  }

  /**
   * SiteSet (v3)
   */
  public static function AjaxEditSiteSet($SiteID) {
      return self::AjaxEditSiteApiRequest("set", array("fill" => $SiteID));
  }

  /**
   * функция редактирования настройки
   */
  public static function SiteEdit($SiteID, $Settings) {
      if (count($SiteID) > 0)
        $Settings["idst"]=join(":",$SiteID);
      $Result = self::SiteApiRequest("edit", $Settings);
      return $Result;
  }


  /**
   * функция редактирования настройки (v2)
   */
  public static function AjaxApiSiteEdit($SiteID, $Settings) {
      $Settings['apisel'] = Conf::getParams('apijet');
      if (count($SiteID) > 0)
      $Settings["idst"] = join(":", $SiteID);
      $Result = self::AjaxApiSiteApiRequest("edit", $Settings, $edit);
      return $edit;
  }


  /**
   * функция редактирования настройки (v3)
   */
  public static function AjaxEditSiteEdit($SiteID, $Settings) {
      $Settings['apisel'] = Conf::getParams('apijet');
      if (count($SiteID) > 0)
      $Settings["idst"] = join(":", $SiteID);
      $Result = self::AjaxEditSiteApiRequest("edit", $Settings, $edit);
      return $edit;
  }


  /**
   * функция добавления настройки (v3)
   */
  public static function SiteAdd($Settings) {
      $Settings['apisel'] = Conf::getParams('apijet');
      $Result = self::AjaxEditSiteApiRequest("edit", $Settings, $edit);
      list(,$id) = explode('ids[0]=', $Result);
      return $id;
  }

  /**
   * получение стоимости настройки в кредитах
   */
  public static function SiteCost($SiteID) {
      if( is_array($SiteID) )
        $SiteID = join(":", $SiteID);
      return self::SiteApiRequest("cost", array("idst" => $SiteID));
  }


  /**
   * получение стоимости настройки в кредитах (v2)
   */
  public static function AjaxApiSiteCost($SiteID) {
      return self::AjaxApiSiteApiRequest("cost", array("idst" => join(":", $SiteID)));
  }


  /**
   * получение стоимости настройки в кредитах (v3)
   */
  public static function AjaxEditSiteCost($SiteID) {
      return self::AjaxEditSiteApiRequest("cost", array("idst" => join(":", $SiteID)));
  }

  /**
   * сохранение параметров настройки в базу
   */
  public static function save($user_id, $counter) {

      $jetidq = (int) $_REQUEST['id'];

      $rowHq = Jetid::find()
        ->where(['id' => $jetidq, 'uz' => $user_id])
        ->asArray()
        ->one();

      if ( !$rowHq ) {
          throw new \Exception("Некорректный id", 1);
      }

      $text = self::AjaxEditSitePoints(array($_REQUEST['id']));
      parse_str($text, $info);

      if (isset($info[$_REQUEST['id']]['notexists']) && $info[$_REQUEST['id']]['notexists'] == 1)
          throw new \Exception("Площадки с ID {$_REQUEST['id']} не существует. Обратитесь в тех поддержку", 1);
      //получаем основные параметры настройки
      $confq = $rowHq['conf'];
      parse_str($confq, $outputq);
      $urlSite = urldecode($outputq['url'] ?? '');
      $md5q = $rowHq['md5'];
      $pokaz = $rowHq['pokaz'];
      $poll = $rowHq['oll'];
      $refbase = $rowHq['ref'];
      $jetidqd[] = $jetidq;

      $otvetd = self::AjaxEditSiteStat($jetidqd);
      parse_str($otvetd, $outputq1d);
      $rezd = array_map("self::myicon2", $outputq1d);
      foreach ($rezd as $idmkd => $idmd) {
          if ( isset($idmd['notexists']) && !$idmd['notexists']) {
              $trafst = (substr($idmd['cr'], 0, 1024));
          } else {
              $trafst = $rowHq['traf'];
          }
      }

      //обновляем настройку id получаем параметры
      parse_str($confq, $outputq);
      $output2q = array_map("self::myicon2", $outputq);
      $outputistq = explode(":", $output2q['pst']);
      $ptm2q = explode(":", $output2q['ptm']);
      $pac2q = explode(":", $output2q['pac']);
      $purl2q = explode("<!;#D>", $output2q['purl']);
      $SiteID = array();
      $Settings = array();
      $ppkh = $_REQUEST['pkh'] ?? '';
      $SiteID[] = $_REQUEST['id'];
      $Settings['site'] = urldecode($output2q['url']);
      $Settings['pkm'] = $_REQUEST['pkm'] ?? '';
      $Settings['pkh'] = $_REQUEST['pkh'] ?? '';
      $Settings['pkt'] = $_REQUEST['pkt'] ?? '';
      $Settings['pkt2'] = $_REQUEST['pkt2'] ?? '';
      $Settings['tml1'] = $_REQUEST['tml1'] ?? '';
      $Settings['tml2'] = $_REQUEST['tml2'] ?? '';
      $Settings['tmlc1'] = $_REQUEST['tmlc1'] ?? '';
      $Settings['tmlc2'] = $_REQUEST['tmlc2'] ?? '';
      $Settings['ssf'] = $output2q['ssf'];
      $Settings['ipc'] = $_REQUEST['ipc'] ?? '';
      $Settings['second'] = $_REQUEST['second'] ?? '';
      $Settings['proxy'] = $_REQUEST['proxy'] ?? ''; //прокси вкл или выкл
      $Settings['exact'] = $_REQUEST['exact'] ?? '';
      $Settings['li'] = $_REQUEST['li'] ?? ''; //Li.ru
      $Settings['speed'] = $_REQUEST['speed'] ?? ''; //скорость ограничение
      $Settings['highspeed'] = $_REQUEST['highspeed'] ?? ''; //высокая скорость  ограничение
      $Settings['mouse'] = $_REQUEST['mouse'] ?? ''; //включили мышку
      $Settings['iphl'] = $_REQUEST['iphl'] ?? '';
      $Settings['iph'] = $_REQUEST['iph'] ?? '';
      $Settings['dayunickon'] = $_REQUEST['dayunickon'] ?? '';
      $Settings['dayunick'] = $_REQUEST['dayunick'] ?? '';
      $Settings['msf'] = $output2q['msf'];
      $Settings['ipex'] = $output2q['ipex'];
      $Settings['hideref'] = $output2q['hideref'];
      $Settings['hsf'] = $output2q['hsf'];
      $Settings['uh'] = $output2q['uh'];
      $Settings['dontstop'] = $_REQUEST['dontstop'] ?? '';
      $Settings['name'] = "UPDATE: ".date("d.m.y - H:i:s").", ID: $user_id";
      $Settings['fid'] = $_REQUEST['fid'] ?? '';
      $Settings['newfolder'] = '';
      $Settings['ggeo'] = $_REQUEST['ggeo'] ?? '';
      $Settings['rgeo'] = $_REQUEST['rgeo'] ?? '';
      if (isset($_REQUEST['geo']) && $_REQUEST['geo'] != '') {
        $Settings['geo'] = $_REQUEST['geo'];
      } else {
        $Settings['geo'] = '0';
      }
      $Settings['prs'] = $output2q['prs'];
      $Settings['prstime'] = $outputistq['0'];
      $Settings['prstime1'] = $outputistq[1];
      $Settings['prsmin'] = $outputistq[2];
      $Settings['prsmax'] = $outputistq[3];
      $Settings['prtab'] = $output2q['prtab'];
      $Settings['prsref'] = $outputistq[4];
      $Settings['prstime2'] = $outputistq[5];
      $Settings['prsrnd'] = $outputistq[6];

      foreach ($ptm2q as $key => $value) {
          $Settings['tms'][] = $ptm2q[$key];
          $Settings['cmds'][] = $pac2q[$key];
          $Settings['urls'][] = $purl2q[$key];
      }

      //первая команда
      //генерируем ссылку при каждом сохранении
      if(
        isset($Settings['urls']) &&
        isset($Settings['urls']['0']) &&
        strstr($Settings['urls']['0'], 'prskey')
      ){
          $Settings['urls']['0'] = 'var prskey="<get(key)>"; <dls('.\app\models\Conf::setIdUrl('<get(id)>', '<rndr(1:999999)>').')>';
      }

       //ограничения галочек, проверка для настройки
  	 //если сущесвтуют - оставляем, если нет - добавляем
      $set_id = self::AjaxEditSiteSet($jetidq);
      parse_str($set_id, $out_set_id);
      $v2 = $out_set_id['v2'];
      if ($v2 == '1'){$Settings['v2'] = '1';} else {$Settings['v2'] = '0';}
      $v3 = $out_set_id['v3'];
      if ($v3 == '1'){$Settings['v3'] = '1';} else {$Settings['v3'] = '0';}
      $v4 = $out_set_id['v4'];
      if ($v4 == '1'){$Settings['v4'] = '1';} else {$Settings['v4'] = '0';}
      $v5 = $out_set_id['v5'];
      if ($v5 == '1'){$Settings['v5'] = '1';} else {$Settings['v5'] = '0';}

      $Settings['tmlrefresh'] = $output2q['tmlrefresh'] ?? '';
      $upidjet = (intval(substr($_REQUEST['id'], 0, 1024)));

      //если тип настройки №4
      if ($rowHq['config_type'] == 'type-4'){

      $dir = Yii::getAlias('@app/web/ID-S/ID/')."$jetidq.js";
      if (file_exists($dir)) {
        //загружаем файл в буфер
        $filename = file_get_contents($dir);

        //парсим ссылочный реферер => $out_referer
        preg_match('~var refer1 \= \[(.*)\];~isU', $filename, $url_ref1);
        preg_match_all('~\'(.*)\'~', $url_ref1[1], $url_refer1);
        $out_referer = '';
        foreach($url_refer1[1] as $val){
          $out_referer .= $val."\n";
        }
        $out_referer = trim($out_referer);

        //парсим настройки
        preg_match('|@urls\((.*)\);|Uis', $filename, $out_urls);
        preg_match('|@time\((.*)\);|Uis', $filename, $out_time);
        preg_match('|@prosmotr\((.*)\);|Uis', $filename, $out_prosmotr);
        preg_match('|@linkmask\((.*)\);|Uis', $filename, $out_linkmask);
        preg_match('|@loadtime\((.*)\);|Uis', $filename, $out_loadtime);
      }

      //url главного сайта
      $Settings['sites'][] = $out_urls[1];

      //реферер по заказу
      $Settings['cref'] = $out_referer;

      //Время показа первого сайта презентации,1 сек для динамики, для других зависит от скорости сайта
      $Settings['prstime'] = $out_time[1];

      //кол-во просмотров сайта
      $prosmotr = $out_prosmotr[1];

      //Маска ссылки для переходов по сайту
      $linkmask = $out_linkmask[1];

      //Время на прогрузку страницы между переходами
      $loadtime = $out_loadtime[1];

      if ( empty($linkmask) ) {
          $linkmask = 'http';
      }

      if ((int) $loadtime < 1) {
          $loadtime = 1;
      }

       //добавлеяем команды просмотра сайта
      $Settings['tms']['0'] = '5';
      $Settings['urls']['0'] = 'a;link;'.$linkmask.';click;-1';
      $Settings['cmds']['0'] = '5';

      $Settings['tms']['1'] = $loadtime;
      $Settings['urls']['1'] = 'last;0';
      $Settings['cmds']['1'] = '1';

      if ((int) $prosmotr < 1 || (int) $prosmotr > 21) {
          $prosmotr = 5;
      }

      for($i=1; $i < (int) $prosmotr; $i++) {
           $index_0 = (string) ($i * 2);
           $index_1 = (string) ($i * 2 + 1);

          $Settings['tms'][$index_0] = $Settings['tms']['0'];
          $Settings['urls'][$index_0] = $Settings['urls']['0'];
          $Settings['cmds'][$index_0] = $Settings['cmds']['0'];

          $Settings['tms'][$index_1] = $Settings['tms']['1'];
          $Settings['urls'][$index_1] = $Settings['urls']['1'];
          $Settings['cmds'][$index_1] = $Settings['cmds']['1'];
        }
      }


      $edit = self::AjaxEditSiteEdit($SiteID, $Settings);
      $upidjet = intval($jetidq);

      if ($upidjet != '' && $upidjet != '0') {
          $confq = (self::AjaxEditSiteSet($upidjet));
          $idscst[] = $upidjet;
          $scost0st = self::AjaxEditSiteCost($idscst);
          parse_str($scost0st, $outputq1st);
          $scost2st = array_map("self::myicon2", $outputq1st);
          $scostst = ($scost2st[$upidjet]);

          //получили из парсинга цену настройки  для одного ID
          parse_str($confq, $outm);
          $outpm = array_map("self::myicon2", $outm);
          $pktfc = $outpm['pkt'] ?? '';
          $pktfc2 = $outpm['pkt2'] ?? '';
          if ($pktfc2 == '0') {
              $pktfc2 = $pktfc;
          }

          $costmaxst = round(($pktfc2 / $pktfc) * $scostst, 2); //вычислили максималку

          $updateModel = Jetid::find()
            ->where(['id' => $upidjet])
            ->one();

          if (!$updateModel) {
              throw new \Exception("Ошибка выполнения запроса, код ошибки QWER122", 0);
          }

          $updateModel->conf = $confq;
          $updateModel->cost = $scostst;
          $updateModel->costmax = $costmaxst;
          $updateModel->traf = $trafst;
          $updateModel->username = (substr($_REQUEST['name'], 0, 1024));
          $updateModel->ref = ($_REQUEST['cref'] ?? '');
          $updateModel->save(false);
      }

      if ($edit != '!') {
          throw new \Exception("Ошибка удаленного запроса, код ошибки QWER123", 0);
      } else {

          $addModel = new ApiRequests();
          $addModel->user_id = $user_id;
          $addModel->type = 'change_settings';
          $addModel->last_request = time();
          $addModel->save(false);

          $json["result"] = "Настройки успешно сохранены";
          Logs::AddSettingLogs('Настройки # ' . $_REQUEST['id']  . ' успешно сохранены с попытки № ' . $counter .'
          <br/>Параметры сохранения:<br/>' .$confq, $user_id);
      }
  }

  //api

  /**
   * пополнение и снятие баланса
   */
  public static function move($avt, $ID, $counter = 1) {

      static $in_default;

      $userModel = Yii::$app->user->identity;
      $mtrafbalans = $userModel->trafbalans;

      $act = trim($_REQUEST["act"]);
      $id = (int) $_REQUEST["id"];
      $count = (int) $_REQUEST["count"];


      if (!$in_default) {
          $row = Jetid::find()
            ->where(['id' => $id, 'uz' => $ID])
            ->asArray()
            ->one();

          $in_default = array(
            'balans' => number_format($mtrafbalans, 2, '.', ''),
            'traf' => number_format(floatval($row['traf']), 2, '.', '')
          );
      }

      $json = array();
      try {
          if ($avt != "OK")
              throw new \Exception("Вы не авторизованы", 1);

          $user_id = $ID;

          $type = '';
          switch ($act) {
              //пополнение определенного ID показами
              case "add-balance":
                  $type = 'Пополнение';

                  //Logs::AddBalansLogs('Инициализация пополнения для # ' . $id . ' попытка №' . $counter, $ID);
                  if ($counter == 1 && !self::checkTime("change_balance"))
                      throw new \Exception(self::getTimeLimitError($ID, "change_balance"), 1);

                  $json["result"] = self::addBalance($id, $count, $user_id);
                  $json["success"] = true;
                  //Logs::AddBalansLogs('Пополнение реалами ID:' . $id . ' на ' . $count . ' реалов' . ' попытка №' . $counter, $ID);
                  break;

              //списание показов с определенного ID
              case "dis-balance":
                  $type = 'Списание';

                  //Logs::AddBalansLogs('Инициализация списания для # ' . $id . ' попытка №' . $counter, $ID);
                  if ($counter == 1 && !self::checkTime("change_balance"))
                      throw new \Exception(self::getTimeLimitError($ID, "change_balance"), 1);

                  $json["result"] = self::disBalance($id, $count, $user_id);
                  $json["success"] = true;
                  //Logs::AddBalansLogs('Снятие реалов с #:' . $id . ' на ' . $count . ' реалов' . ' попытка №' . $counter, $ID);
                  break;

              default:
                  throw new \Exception("Неверное действие", 1);
          }
      } catch (\Exception $e) {

          //обработка ошибок ajax
          if ($e->getCode() != 0 || $counter > 5) {
              Logs::AddBalansLogs($type . ' # ' . $id . ' закончилось ошибкой: "' . $e->getMessage() . '"', $ID);
              $json["error"] = $e->getMessage();
              $json["success"] = false;
          } else {
              $counter++;
              Logs::AddBalansLogs($type . ' не удалось # ' . $id . ', ошибка: "' . $e->getMessage() . '"  попытка №' . $counter, $ID);
              $json = self::move($avt, $ID, $counter);
          }
      }
      return $json;
  }

  /**
   * функция добавления реалов на баланс
   */
  public static function addBalance($id, $count, $user_id) {
      $userModel = Yii::$app->user->identity;
      $mtrafbalans = $userModel->trafbalans;

      $rowModel = Jetid::find()
        ->where(['id' => $id, 'uz' => $user_id])
        ->one();

      if ( empty($rowModel) )
          throw new \Exception("Внутренняя ошибка. Настройка $id не найдена.", 0);

      $row = $rowModel->toArray();

      $costmax = $row['costmax'];
      $traf = $row['traf'];
      if ($count < 10)
        throw new \Exception("Введите число больше 10 реалов", 1);
      if ($count >= 1000000)
        throw new \Exception("Максимальное число пополнения реалами 999 999 шт.", 1);

      if ($mtrafbalans >= $count) {

          $result = self::AjaxApiSitePoints(array($id), 1, $count, 0, 0, 0, "change_balance");
          if (!$result["success"]) {
              if ($result["error_code"] == 0) {
                  throw new \Exception("Сервер очень перегружен, попробуйте через пару секунд...", 0);
              }
          }

           //получаем результаты
          parse_str($result['result'], $info);
          if (isset($info[$_REQUEST['id']]['notexists']) && $info[$_REQUEST['id']]['notexists'] == 1)
              throw new \Exception("Площадки с ID $id не существует. Обратитесь в тех поддержку", 1);

          $result = $result["result"];
          parse_str($result, $re1);
          $re2 = array_map("self::myicon2", $re1);
          $re3 = $re2[$id];

          //зачислиляем на баланс в случае успеха
          $otv = $re3['done'] ?? '';

          //если баланс успешно пополнен
          if ($otv == '1') {

              //используем транзакцию для гарантированного обновления двух таблиц
              $transaction = Yii::$app->db->beginTransaction();
              try{
                $rowModel->notify_send = 0;
                $rowModel->traf = $rowModel->traf + number_format($count, 2, '.', '');
                $rowModel->save(false);

                $balans_before = $userModel->balans;
                $trafbalans_before = $userModel->trafbalans;
                $userModel->trafbalans = $userModel->trafbalans - $count;
                $userModel->save(false);

                // записываем в историю транзакций
                Paymentsinfo::add([
                  'cost' => $count,
                  'note' => "Пополнение баланса настройки id $id. Было рублей: {$balans_before}, стало: {$userModel->balans}. Было реалов: {$trafbalans_before}, стало: {$userModel->trafbalans}",
                ], Paymentsinfo::TRAFBALANS_MINUS);
				
				//проверка на мошенничество
				Antifraud::add([
				  'type' => Antifraud::TRAFBALANS_MINUS,
				  'cost' => $count,
				  'note' => "Пополнение баланса настройки id $id. Было реалов: {$trafbalans_before}, стало: {$userModel->trafbalans}",
				]);

                //---
                $transaction->commit();
              }catch (\Exception $e){
                $transaction->rollback();

                throw new \Exception("Ошибка выполнения запроса код ошибки 012300", 0);
              }
              //---

              Logs::AddBalansLogs("Тип операции: 'пополнение', настройка #" . $id . ", баланс общего счета реалов до/после: (" . number_format($mtrafbalans, 2, '.', '') . "/" . number_format($mtrafbalans - $count, 2, '.', '') . ") шт, баланс настройки до/после операции (" . number_format($traf, 2, '.', '') . "/"
              . number_format($traf + $count, 2, '.', '') . ") шт. Пополнено " . $count . " реалов.", $user_id);
              return "Баланс $id пополнен на $count реалов";
          }
          else {
              //выводим ошибки  в случае чего по коду ответа сервера
              $nomer = $re3['error'];
              if ($nomer[0][0] == '1') {
                  $today = date("Y-m-d H:i:s");
                  //ошибка если реалы кончились на главном аккаунте

                  $tiketsModel = new Tikets();
                  $tiketsModel->title = 'ROBOT';
                  $tiketsModel->message = "Не хватает количество трафика. Реалы: {$count}, ID: $id, USERID: $user_id";
                  $tiketsModel->save(false);


                  //уведомляем админа об этом
                  Yii::$app->mailer->compose()
                    ->setFrom([
                      Yii::$app->params['sendFrom']['email'] => Yii::$app->params['sendFrom']['name']
                    ])
                    ->setReplyTo(Yii::$app->params['sendFrom']['email'])
                    ->setTo( Conf::getParams('adminmail') )
                    ->setSubject('Новый тикет')
                    ->setHtmlBody(
                      'Одному из пользователей не хватает трафика ID: '.$user_id.', настройка #'.$id.', запрашиваемое кол-во реалов: '.$count.' '
                    )
                    ->send();

                  throw new \Exception("Данного кол-ва трафика сейчас нет в системе, администрация уже курсе и
              получила Ваш запрос.<br>Требуемое кол-во, будет добавлено в ближайшее время! Можете попробовать пополнить на меньшее кол-во реалов..", 1);
              }
              elseif ($nomer[0][0] == '4')
                  throw new \Exception("Максимальное число пополнения реалами 999 999 шт.", 1);
              elseif ($nomer[0][0] == '5')
                  throw new \Exception("Сайт находится в черном списке и не может рекламироваться в системе", 1);
          }
      }
      else {
          if ($mtrafbalans < $count) {
              throw new \Exception("Кол-во реалов на балансе недостаточно для выполнения данной операции, обновите статистику и попробуйте еще раз!", 1);
          }
          if ($mtrafbalans >= $count) {
              throw new \Exception("Прошло менее 5 минут с момента предыдущего запуска интерфейса", 1);
          }
      }

      throw new \Exception("Сервер очень перегружен, попробуйте через пару секунд", 0);
  }


  /**
   * фукнция снятия реалов с баланса
   */
  public static function disBalance($id, $count, $user_id) {

      if ($count < 10)
        throw new \Exception("Введите число больше 10 реалов", 1);
      if ($count >= 1000000)
        throw new \Exception("Максимальное число снятия реалами 999 999 шт.", 1);

      $ids[] = $id;

      $userModel = Yii::$app->user->identity;
      $mtrafbalans = $userModel->trafbalans;

      $rowModel = Jetid::find()
        ->where(['id' => $id, 'uz' => $user_id])
        ->one();

      if ( empty($rowModel) )
          throw new \Exception("Внутренняя ошибка. Настройка $id не найдена.", 0);

      $row = $rowModel->toArray();

      $traf = $row['traf'];
      $costmax = $row['costmax'];
      $result = self::AjaxApiSiteStat($ids);
      if (!$result["success"]) {
          if ($result["error_code"] == 0) {
              throw new \Exception("Сервер очень перегружен, попробуйте через пару секунд...", 0);
          }
      }

      parse_str($result['result'], $info);

      if (isset($info[$_REQUEST['id']]['notexists']) && $info[$_REQUEST['id']]['notexists'] == 1)
          throw new \Exception("Площадки с ID $id не существует. Обратитесь в тех.поддержку", 1);

      $result = $result["result"];
      parse_str($result, $outputq1);
      $rez = array_map("self::myicon2", $outputq1);
      foreach ($rez as $idmk => $idm) {
          if (!($idm['notexists'] ?? '')) {
              $cr = (substr($idm['cr'], 0, 1024));
          }
      }

      if ($traf >= $count) {
          $result = self::AjaxApiSitePoints($ids, 2, $count, 0, 0, 0, "change_balance");

          if (!$result["success"])
              throw new \Exception("Возникла ошибка, обратитесь в тех поддержку", 0);

          $result = $result["result"];
          parse_str($result, $re1);
          $re2 = array_map("self::myicon2", $re1);
          $re3 = $re2[$id];

          //успешно снимаем реалы
          $cr2 = round($cr - $count, 2);
          $otv = $re3['done'] ?? '';

          if ($otv == '1') {

              //используем транзакцию для гарантированного обновления двух таблиц
              $transaction = Yii::$app->db->beginTransaction();
              try{
                $rowModel->notify_send = 0;
                $rowModel->traf = number_format($cr2, 2, '.', '');
                $rowModel->pokaz = $rowModel->pokaz - $count;
                $rowModel->save(false);

                $balans_before = $userModel->balans;
                $trafbalans_before = $userModel->trafbalans;
                $userModel->trafbalans = $userModel->trafbalans + $count;
                $userModel->save(false);

                // записываем в историю транзакций
                Paymentsinfo::add([
                  'cost' => $count,
                  'note' => "Списание с баланса настройки id $id. Было рублей: {$balans_before}, стало: {$userModel->balans}. Было реалов: {$trafbalans_before}, стало: {$userModel->trafbalans}",
                ], Paymentsinfo::TRAFBALANS_PLUS);
				
				//проверка на мошенничество
				Antifraud::add([
				  'type' => Antifraud::TRAFBALANS_PLUS,
				  'cost' => $count,
				  'note' => "Списание с баланса настройки id $id. Было реалов: {$trafbalans_before}, стало: {$userModel->trafbalans}",
				]);
				
                //---
                $transaction->commit();
              }catch (\Exception $e){
                $transaction->rollback();

                throw new \Exception("Ошибка выполнения запроса updbalapi2", 0);
              }
              //---

              Logs::AddBalansLogs("Тип операции: 'снятие', настройка #" . $id . ", баланс общего счета реалов до/после: ("
              . number_format($mtrafbalans, 2, '.', '') . "/" . number_format($mtrafbalans + $count, 2, '.', '') . ") шт, баланс настройки до/после операции (" . number_format($traf, 2, '.', '') . "/"
              . number_format($traf - $count, 2, '.', '') . ") шт. Снято " . $count . " реалов.", $user_id);
              return "С баланса $id списано $count реалов";
          } else {
              $nomer = $re3['error'];
              if ($nomer[0][0] == 1)
                  $err = "У вас недостаточно реалов на настройке для снятия, уменьшите число или обновите статистику";
              elseif ($nomer[0][0] == 4)
                  $err = "Не получилось снять реалы..наверное их уже нет, попробуйте еще раз!";
              elseif ($nomer[0][0] == 5)
                  $err = "Сайт в черном списке и не может рекламироваться в системе";
              else
                  $err = "Текущий баланс реалов на настройке возможно еще не обновился, попробуйте остановить настройку или снимите меньшее кол-во.";
              throw new \Exception($err, 1);
          }
      }
      else {
          throw new \Exception("Возникла ошибка: либо нет реалов на счету, либо статистика давно не обновлялась - обновите и попробуйте еще раз!", 1);
      }
  }

  /**
   * Функция получения базовой информации о пользователе
   */
  public static function InfoUser()
	{
		$Result = "<br/>IP адрес: " . getenv("REMOTE_ADDR") . "<br/>
    Юзер агент: " . getenv("HTTP_USER_AGENT") . "<br/>";
		return $Result;
	}

  /**
   * Проверка баланса кредитов у 1 настройки
   * Вместо $SiteID передаем id нужной настройки
   */
  public static function SiteStatForDelete($SiteID)
	{
		$Result = self::SiteApiRequest("stat", array(
			"idst" => $SiteID
		));
		return $Result;
	}

  /**
   * Функция удаления настройки на Jetswap, возвращает "1" в случае успеха в массиве
   * Вместо $SiteID передаем id нужной настройки
   */
  public static function SiteDelete($SiteID)
	{
		$Result = self::SiteApiRequest("delete", array(
			"idst" => $SiteID
		));
		return $Result;
	}

  /**
   * Функция SiteTaskDel - используется для удаления расписания сайтов.
   * Параметры:
   * $SiteID - массив идентификаторов нужных сайтов.
   * Выдает массив с результатами операции для каждого ID сайта, переданного в массиве $SiteID. Подробнее о значениях выходного массива вы можете узнать в документации.
   */
  public static function SiteTaskDel($SiteID)
  {
  	$Result = self::SiteApiRequest("taskdel", array(
  		"idst" => join(":", $SiteID)
  	));
  	return $Result;
  }

  /**
   * Функция расписания
   */
   public static function SiteTaskSet($SiteID)
   {
       $Result=self::SiteApiRequest("taskset",array("fill"=>$SiteID));
       return $Result;
   }

   /**
    * Функция SiteTask - используется для добавления или изменения расписания показов сайтов.
    * Параметры:
    * $SiteID - массив идентификаторов нужных сайтов.
    * $Settings - массив настроек (подробное содержание этого массива см. в документации к интерфейсу)
    * Выдает массив с результатами операции для каждого ID сайта, переданного в массиве $SiteID. Подробнее о значениях выходного массива вы можете узнать в документации.
    */
    public static function SiteTask($SiteID, $Settings)
    {
    	$Settings["idst"] = join(":", $SiteID);
    	$Result = self::SiteApiRequest("task", $Settings);
    	return $Result;
    }

    /**
     * функция обновления настройки
     */
    public static function UpdateStats($attempt)
    {
    	$onlineday = Conf::getParams('onlineday');
    	$last_online = $onlineday * 60 * 60 * 24;

    	// Выбираем пользователей которые были онлайн назад $last_online сек
      $data = Users::find()
        ->where(['>=', 'lastdate', (time() - $last_online)])
        ->asArray()
        ->all();

      if( empty($data) ) return;

  		$dataSettings = array();

  		// Перебираем подходящих пользователей
  		foreach ($data as $row) {

  			// Список ID-ов настроек текущего пользователя
  			$jet_ids = Jetid::find()
          ->select('id')
          ->where(['uz'=> $row['id']])
          ->asArray()
          ->column();

  			if (!empty($jet_ids)) {
  				foreach($jet_ids as $v) {
  					$dataSettings[] = $v;
  				}
  			}
  		}

  		// end while
  		$dataSettings = array_map("trim", $dataSettings); // весь массыв настроек
  		$preparedArray = array_chunk($dataSettings, 48); //разбитый массыв по 48 значений
  		if (!empty($preparedArray[0])) {
  			$cout = 0;
  			foreach($preparedArray as $pp) {

  				// посылаем запрос на API
  				$siteStat = self::SiteStat($pp);
  				$cout++;
  				foreach($siteStat as $id => $site) {
  					if (isset($site['notexists'])) continue;
  					$update_stata[] = $id;

  					// обновляем статистику юзеров
            $updateModel = Jetid::find()
              ->where(['id' => $id])
              ->one();
            $updateModel->traf = $site['cr'];
            $updateModel->pokaz = $site['cr'];
            $updateModel->ch = $site['pkh'];
            $updateModel->d = $site['pkd'];
            $updateModel->oll = $site['pk'];
            $updateModel->last = $site['lp'];
            $updateModel->save(false);
  				}
  			}
  		}

    	if (empty($update_stata)) {
        Logs::AddCronLogs("Автоматическое обновление статистики
          пользователей кроном произошло с ошибкой" . implode(", ", $update_stata) .
          "(попытка " . $attempt . ")"
        );
    		return false;
    	}

    	if (isset($update_stata)) {
        Logs::AddCronLogs("Автоматическое обновление статистики
          (онлайн = $last_online сек, ".ceil($last_online/86400)." дн.) (кол-во запросов к API = $cout)
          для ID: " . implode(", ", $update_stata) . " (попытка " . $attempt . ")"
        );
    		return true;
    	}

    	return false;
    }
}
