<?php

namespace app\components;

use Yii;
use app\components\ApiFunctions;
use app\models\Conf;

trait LimitsAndConfigTrait
{
    public static function loadLimits( $definedVars ){
      extract( $definedVars );

      $limitHtml = '';

      //текущее время с эпохи линукс
      $time_online = time();

      //кол-во секунд в 24ч = 24*60*60 = 86400сек
      $time_sytki = (24*60*60);

      //Берем данные по лимитам пользователя

        //берем лимит кол-ва настроек в день
        $limit_users_id = $userModel->limitsByType['add_id']['trueTime'];

        //берем лимит таймаута между настройками
        $limit_users_time = $userModel->limitsByType['limit_time_add']['trueTime'];

        //лимит на общее кол-во настроек в панели
        $limit_value_configs = $userModel->limitsByType['limit_add_id']['trueTime'];

      //Конец. Берем данные по лимитам пользователя

      //берем дату последнего запроса
      $limit_last_request = $userModel->lastRequest('add_id'); //последняя дата сохранения

      //берем созданное кол-во настроек у юзера за сутки
      $limit_n_id = $userModel->lastRequest('limit_n_id'); //кол-во настроек

      //берем общее кол-во настроек в панели
      $limit_value_id = $userModel->countSettings();//всего настроек

      //считаем разницу времени между последним запросом и текущим временем (сек)
      $result_time = ($time_online - $limit_last_request);

      //минут прошло с последнего запроса
      $result_time_minyt =  round(($result_time/60),0);

      //$limitHtml.= "<div id='inform_user'>";

       // Условия проверки
       //если запросы в базе есть
      if ($limit_last_request != '') {
      //  $limitHtml.= "Последний запрос от Вас был $result_time_minyt минут(-ы) назад<br>";
      }

      //запросов в базе нет
      if ($limit_last_request == '') {
      //  $limitHtml.= "У вас не было запросов создания, Вам разрешено создание настройки!<br>";
      }

      // если запрос был, но давно - разрешаем создание
      if ($result_time >= $limit_users_time) {
        //ложим статус создания как разрешено true
        $status = "true";
      }



      // если превышено общее кол-во настроек
      if ($limit_value_id >= $limit_value_configs) {
          //ложим статус создания как разрешено false
        Yii::$app->session->addFlash('error', "Вы превысили общий лимит настроек ($limit_value_configs шт всего), удалите некоторые шаблоны или воспользуйтесь старыми!
        Увеличение лимита возможно через запрос в администрацию");
        $status2 = "false";
        $status = "false";
      }


      //если запрос был и недавно
      if ($result_time < $limit_users_time) {
        //считаем сколько подождать!
        $ostatok_time = round((($limit_users_time - $result_time)/60),0);

        $secOrMin = 'мин.';
        if( $ostatok_time < 60 ){
          $ostatok_time = round($limit_users_time - $result_time,0);
          $secOrMin = 'сек.';
        }

        Yii::$app->session->addFlash('error', "Подождите пожалуйста $ostatok_time $secOrMin<br>Таймаут ожидания между созданием: $limit_users_time сек.");

        //показываем юзеру его лимиты
        //$limitHtml.= "Лимит кол-ва настроек в сутки для Вас: $limit_users_id шт<br>";
        //ложим статус создания как запрещено false
        $status = "false";
      }

      //лимиты кол-ва настроек у юзеров
      //три проверки на ==, на > и на <
      //$status2 - разрешение по кол-ву создания

      // если запрос был, но давно - разрешаем создание
      if ($limit_n_id =='') {
      //  $limitHtml.= "Лимит настроек не сохранялся!<br>";
        //добавляем нолик юзеру!
        $userModel->nullLastRequest('limit_n_id');
      }


      //лимит настроек есть
      if ($limit_n_id !='') {

        //созданное кол-во == лимиту юзера
        if ($limit_n_id == $limit_users_id) {
          Yii::$app->session->addFlash('error', "Вам нельзя создавать за сутки больше $limit_users_id настроек!<br>Попробуйте обновить страницу или зайдите позже!");
          $status2 = "false";
        }

        //созданное кол-во > лимита юзера
        if ($limit_n_id > $limit_users_id) {
          Yii::$app->session->addFlash('error', "Ошибка, превышен Ваш лимит в сутки по созданию настроек!<br>Попробуйте обновить страницу или зайдите позже!");
          $status2 = "false";
        }


        //созданное кол-во < лимита юзера
        if ($limit_n_id < $limit_users_id) {
          $razr = ($limit_users_id-$limit_n_id);
        //  $limitHtml.= "Можно создать сегодня: $razr шт шаблонов!<br>";
          $status2 = "true";
        }


        if ($result_time >= $time_sytki){
          //обнуляем лимит
          //$userModel->nullLastRequest('limit_n_id');
          $status2 = "true";
        }

      }

      //$limitHtml.= "</div>";

      return get_defined_vars();
    }

    public static function loadConfigApi( $definedVars ){
      extract( $definedVars );

      //Дефолтные параметры настройки
      eval( Conf::getParams('api_jetswap_config') );

      $Settings = array();
      $Settings['pkm'] = $param['pkm'] ?? '';
      $Settings['pkh'] = $param['pkh'] ?? '';
      $Settings['pkt'] = $param['pkt'] ?? '';
      $Settings['pkt2'] = $param['pkt2'] ?? '';
      $Settings['tml1'] = $param['tml1'] ?? '';
      $Settings['tml2'] = $param['tml2'] ?? '';
      $Settings['tmlc1'] = $param['tmlc1'] ?? '';
      $Settings['tmlc2'] = $param['tmlc2'] ?? '';
      $Settings['ssf'] = $param['ssf'] ?? '';
      $Settings['ipc'] = $param['ipc'] ?? '';
      $Settings['second'] = $param['second'] ?? '';
      $Settings['iphl'] = $param['iphl'] ?? '';
      $Settings['iph'] = $param['iph'] ?? '';
      $Settings['dayunick'] = $param['dayunick'] ?? '';
      $Settings['ipex'] = $param['ipex'] ?? '';
      $Settings['hideref'] = $param['hideref'] ?? '';
      $Settings['hsf'] = $param['hsf'] ?? '';
      $Settings['uh'] = $param['uh'] ?? '';
      $Settings['name'] = $param['name'] ?? '';
      $Settings['fid'] = $param['fid'] ?? '';
      $Settings['newfolder'] = $param['newfolder'] ?? '';
      $Settings['ggeo'] = $param['ggeo'] ?? '';
      $Settings['rgeo'] = $param['rgeo'] ?? '';
      $Settings['cref'] = $param['cref'] ?? '';
      $Settings['prs'] = $param['prs'] ?? '';
      $Settings['prstime'] = $param['prstime'] ?? '';
      $Settings['prstime1'] = $param['prstime1'] ?? '';
      $Settings['prsmin'] = $param['prsmin'] ?? '';
      $Settings['prsmax'] = $param['prsmax'] ?? '';
      $Settings['prtab'] = $param['prtab'] ?? '';
      $Settings['prsref'] = $param['prsref'] ?? '';
      $Settings['sitetitle'] = $param['sitetitle'] ?? '';
      $Settings['sitedesk'] = $param['sitedesk'] ?? '';
      $Settings['catid'] = $param['catid'] ?? '';
      $Settings['apisel'] = $param['apisel'] ?? '';
      $Settings['sites'] = $param['site'] ?? '';
      $Settings['tms']['0'] = $param['tms']['0'] ?? '';
      $Settings['tms']['1'] = '';
      $Settings['geo'] = $param['geo'] ?? '';
      $Settings['cmds']['0'] = $param['cmds']['0'] ?? '';
      $Settings['cmds']['1'] = $param['cmds']['1'] ?? '';
      $Settings['urls']['0'] = $param['urls']['0'] ?? '';
      $Settings['tmlrefresh'] = '1';

      //проверяем id  на корректность в цикле
      for($schet_u = 1; $schet_u <=4; $schet_u++){
        $Result = ApiFunctions::SiteEdit(array($param['id'] ?? ''), $Settings);
        $new_id = $Result['ids'][0];
        if (is_numeric($new_id)) {
          break;
        }
        else{
          sleep(2);
        }
      }

      //получаем настройки
      $conf = ApiFunctions::SiteSet($new_id);

      //время мин.показа    $pkt
      $pkt = $conf['pkt'] ?? '';

      //время макс.показа  $pkt2
      $pkt2 = $conf['pkt2'] ?? '';

      //если второе время = 0, то первое время = второму времени
      if ($pkt2=='0'){
        $pkt2=$pkt;
      }
      //получаем стоимость показа
      $scost = ApiFunctions::SiteCost($new_id);
      $scost = round($scost[$new_id], 2);
      //вычислили максималку
      $costmax = round(($pkt2 / $pkt) * $scost, 2);

      //кодируем переменные в urlencode
      $zacoder_pkm = urlencode($param['pkm'] ?? '');
      $zacoder_pkh = urlencode($param['pkh'] ?? '');
      $zacoder_tml1 = urlencode($param['tml1'] ?? '');
      $zacoder_tml2 = urlencode($param['tml2'] ?? '');
      $zacoder_site = urlencode($param['site'] ?? '');
      $zacoder_urls = urlencode($param['urls']['0'] ?? '');
      $zacoder_name = urlencode($param['name'] ?? '');
      $zacoder_tms = urlencode($param['tms']['0'] ?? '');
      $zacoder_second = urlencode($param['second'] ?? '');
      $zacoder_iphl = $param['iphl'] ?? '';
      $zacoder_iph = $param['iph'] ?? '';
      $zacoder_dayunick = $param['dayunick'] ?? '';
      $zacoder_ipex = $param['ipex'] ?? '';
      $zacoder_hideref = $param['hideref'] ?? '';
      $zacoder_hsf = $param['hsf'] ?? '';
      $zacoder_uh = $param['uh'] ?? '';
      $zacoder_fid = $param['fid'] ?? '';
      $zacoder_newfolder = $param['newfolder'] ?? '';
      $zacoder_prs = $param['prs'] ?? '';
      $zacoder_prtab =  $param['prtab'] ?? '';
      $zacoder_ipc = $param['ipc'] ?? '';
      $zacoder_tmlc1 = $param['tmlc1'] ?? '';
      $zacoder_tmlc2 = $param['tmlc2'] ?? '';
      $zacoder_ssf = $param['ssf'] ?? '';


      $conf = "url=$zacoder_site&pkt=$pkt&pkt2=$pkt2&free=0&nofree=0&hideref=$zacoder_hideref&crmin=0&vipmin=0&cradd=0&vipadd=0&cref=&lc=0&chk=0&geonac=0.00&uh=$zacoder_uh&iph=$zacoder_iph&iphl=$zacoder_iphl&fid=$zacoder_fid&name=$zacoder_name&tml1=$zacoder_tml1&tml2=$zacoder_tml2&tmlc1=$zacoder_tmlc1&tmlc2=$zacoder_tmlc2&dontstop=0&dayunick=$zacoder_dayunick&prtab=$zacoder_prtab&ipc=$zacoder_ipc&pkm=$zacoder_pkm&pkh=$zacoder_pkh&ssf=$zacoder_ssf&v2=0&v3=0&v4=0&v5=0&tml=0&prs=$zacoder_prs&hsf=$zacoder_hsf&geo=0&second=$zacoder_second&msf=0&ipex=$zacoder_ipex&proxy=0&exact=0&mouse=0&speed=0&highspeed=0&li=0&pst=1%3A0%3A0%3A0%3A1%3A0%3A0&ptm=20&pac=7&purl=$zacoder_urls&site=$zacoder_site&sites[0]=$zacoder_site";

      return get_defined_vars();
    }
}
