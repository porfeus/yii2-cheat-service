<?php

namespace app\models;

use Yii;
use yii\base\Model;
use app\components\ApiFunctions;
use app\components\Coderton;
use app\models\Logs;
use app\models\Jetid;
use app\models\Conf;

class Add4Form extends Model
{
  use \app\components\LimitsAndConfigTrait;

  //начало кода вставки
  public static $code = <<<SERVISCODE

/*начало настроек
@urls({urls});
@time({time});
@prosmotr({prosmotr});
@linkmask({linkmask});
@loadtime({loadtime});
var refer1 = [
{referer}
];
конец настроек*/

SERVISCODE;
//конец вставки кода

  /**
   * проверка формы
   */
  public static function loadForm($definedVars){
    extract( $definedVars );

    $types = 'type-4';

    //подключаем ограничение лимитов
    extract( self::loadLimits( get_defined_vars() ) );

    //задаем путь хранения настроек
    $dir = Yii::getAlias('@app/web/ID-S/ID/');

    if(isset($_POST['generate'])){

      //если админская панель то активируем забор ID
      if ( \Yii::$app->controller->module->id == 'admin' ){
        $ID = (int) $_POST['ID'];
      }


      //время захода от и до
      $time = (int) $_POST['time'];
      $urls =  $_POST['urls'];

      //ссылочный реферер
      $referer = $_POST['referer'];
      $referer = explode("\r\n", $referer);
      $ref_sucsess = '';
      foreach($referer as $k=>$v){
          if (empty($v)){
              unset($referer[$k]);
          }else{
              $v = trim($v);
              if((strripos($v, "'") === false) and strripos($v, 'http') !== false or strripos($v, 'about:blank') !== false or strripos($v, 'https') !== false){
                  $referer[$k] = $v;
                  $ref_sucsess .= $v."\r\n";
              }else{
                  unset($referer[$k]);
              }
          }
      }
      $referer = array_chunk($referer,500);
      $referer = "'".implode("',".PHP_EOL."'", $referer[0]??[])."'";
      $zap= ",";
      $referer = $referer.$zap;


      //Кол-во просмотров сайта
      $prosmotr = (int) $_POST['prosmotr'];

      //Маска ссылки для переходов по сайту
      $linkmask = $_POST['linkmask'];

      //Время на прогрузку страницы между переходами
      $loadtime = (int) $_POST['loadtime'];

      $date_generate = date("создана: дата d.m.y время H:i:s");

      //новый ID получать через api если разрешено юзеру!
      if($status == "true" AND $status2 == "true"){
        //include('add-api-4.php');


        /**начало шаблона 4*/


        //Дефолтные параметры настройки
        eval( Conf::getParams('api_jetswap_config_4') );

        if ( empty($linkmask) ) {
            $linkmask = 'http';
        }

        if ((int) $loadtime < 1) {
            $loadtime = 1;
        }

        $Settings = array();
        $Settings['pkm'] = $param['pkm']??'';
        $Settings['pkh'] = $param['pkh']??'';
        $Settings['pkt'] = $param['pkt']??'';
        $Settings['pkt2'] = $param['pkt2']??'';
        $Settings['tml1'] = $param['tml1']??'';
        $Settings['tml2'] = $param['tml2']??'';
        $Settings['tmlc1'] = $param['tmlc1']??'';
        $Settings['tmlc2'] = $param['tmlc2']??'';
        $Settings['ssf'] = $param['ssf']??'';
        $Settings['ipc'] = $param['ipc']??'';
        $Settings['second'] = $param['second']??'';
        $Settings['iphl'] = $param['iphl']??'';
        $Settings['iph'] = $param['iph']??'';
        $Settings['dayunick'] = $param['dayunick']??'';
        $Settings['ipex'] = $param['ipex']??'';
        $Settings['hideref'] = $param['hideref']??'';
        $Settings['hsf'] = $param['hsf']??'';
        $Settings['uh'] = $param['uh']??'';
        $Settings['name'] = $param['name']??'';
        $Settings['fid'] = $param['fid']??'';
        $Settings['newfolder'] = $param['newfolder']??'';
        $Settings['ggeo'] = $param['ggeo']??'';
        $Settings['rgeo'] = $param['rgeo']??'';
        $Settings['cref'] = $param['cref']??'';
        $Settings['prs'] = $param['prs']??'';
        $Settings['prstime'] = $param['prstime']??'';
        $Settings['prstime1'] = $param['prstime1']??'';
        $Settings['prsmin'] = $param['prsmin']??'';
        $Settings['prsmax'] = $param['prsmax']??'';
        $Settings['prtab'] = $param['prtab']??'';
        $Settings['prsref'] = $param['prsref']??'';
        $Settings['sitetitle'] = $param['sitetitle']??'';
        $Settings['sitedesk'] = $param['sitedesk']??'';
        $Settings['catid'] = $param['catid']??'';
        $Settings['apisel'] = $param['apisel']??'';
        $Settings['sites'] = $param['site']??'';

        $Settings['tms']['0'] = '5';
        $Settings['urls']['0'] = 'a;link;'.$linkmask.';click;-1';
        $Settings['cmds']['0'] = '5';

        $Settings['tms']['1'] = $loadtime;
        $Settings['urls']['1'] = 'last;0';
        $Settings['cmds']['1'] = '1';

        if ((int) $prosmotr < 1 || (int) $prosmotr > 9) {
            $prosmotr = 10;
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

        $Settings['geo'] = $param['geo']??'';
        $Settings['tmlrefresh'] = '1';

        //проверяем id  на корректность в цикле
        for($schet_u = 1; $schet_u <=4; $schet_u++){
          $Result = ApiFunctions::SiteEdit(array($param['id']??''), $Settings);
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
        $pkt = $conf['pkt'];

        //время макс.показа  $pkt2
        $pkt2 = $conf['pkt2'];

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
        $zacoder_pkm = urlencode($param['pkm']??'');
        $zacoder_pkh = urlencode($param['pkh']??'');
        $zacoder_tml1 = urlencode($param['tml1']??'');
        $zacoder_tml2 = urlencode($param['tml2']??'');
        $zacoder_site = urlencode($param['site']??'');
        $zacoder_urls = urlencode($param['urls']['0']??'');
        $zacoder_name = urlencode($param['name']??'');
        $zacoder_tms = urlencode($param['tms']['0']??'');
        $zacoder_second = urlencode($param['second']??'');
        $zacoder_iphl = $param['iphl']??'';
        $zacoder_iph = $param['iph']??'';
        $zacoder_dayunick = $param['dayunick']??'';
        $zacoder_ipex = $param['ipex']??'';
        $zacoder_hideref = $param['hideref']??'';
        $zacoder_hsf = $param['hsf']??'';
        $zacoder_uh = $param['uh']??'';
        $zacoder_fid = $param['fid']??'';
        $zacoder_newfolder = $param['newfolder']??'';
        $zacoder_prs = $param['prs']??'';
        $zacoder_prtab =  $param['prtab']??'';
        $zacoder_ipc = $param['ipc']??'';
        $zacoder_tmlc1 = $param['tmlc1']??'';
        $zacoder_tmlc2 = $param['tmlc2']??'';
        $zacoder_ssf = $param['ssf']??'';


        $conf = "url=$zacoder_site&pkt=$pkt&pkt2=$pkt2&free=0&nofree=0&hideref=$zacoder_hideref&crmin=0&vipmin=0&cradd=0&vipadd=0&cref=&lc=0&chk=0&geonac=0.00&uh=$zacoder_uh&iph=$zacoder_iph&iphl=$zacoder_iphl&fid=$zacoder_fid&name=$zacoder_name&tml1=$zacoder_tml1&tml2=$zacoder_tml2&tmlc1=$zacoder_tmlc1&tmlc2=$zacoder_tmlc2&dontstop=0&dayunick=$zacoder_dayunick&prtab=$zacoder_prtab&ipc=$zacoder_ipc&pkm=$zacoder_pkm&pkh=$zacoder_pkh&ssf=$zacoder_ssf&v2=0&v3=0&v4=0&v5=0&tml=0&prs=$zacoder_prs&hsf=$zacoder_hsf&geo=0&second=$zacoder_second&msf=0&ipex=$zacoder_ipex&proxy=0&exact=0&mouse=0&speed=0&highspeed=0&li=0&pst=1%3A0%3A0%3A0%3A1%3A0%3A0&ptm=20&pac=7&purl=$zacoder_urls&site=$zacoder_site&sites[0]=$zacoder_site";


        /**конец шаблона 4*/


        //$new_id = 123457; получен
        $namefile = $new_id;


        if(strpos($urls, 'goo.gl') != false){
          Yii::$app->session->addFlash('warning', "Сокращатели Goo.gl и часть других запрещены для использования!");
          Logs::AddSettingLogs("ID пользователя: $ID, тип настройки = $types
          Ошибка создания настройки через API Код операции: Add4-009", $ID);
        }


        if ($new_id!='0' AND $new_id!=''){
          //активируем настройку
          $idModel = new Jetid();
          $idModel->id = $new_id;
          $idModel->uz = $ID;
          $idModel->conf = $conf;
          $idModel->md5 = md5('');
          $idModel->cost = $scost;
          $idModel->costmax = $costmax;
          $idModel->username = $date_generate;
          $idModel->config_type = 'type-4';
          $idModel->save(false);
        }

        if($new_id=='0' OR $new_id==''){
          Yii::$app->session->addFlash('error', "Произошла ошибка создания API настройки!");
          Logs::AddSettingLogs("ID пользователя: $ID, тип настройки = $types
          Произошла ошибка создания API настройки Код операции: Add4-001", $ID);
        }


        //проверка на лимиты
        //если первый раз сохраняет, записываем лимит
        if ($limit_last_request == '' AND $new_id!='0' AND $new_id!='') {
          $userModel->updateLastRequest('add_id');
        }

        //если уже сохранял обновляем лимиты
        if ($limit_last_request != '' AND $new_id!='0' AND $new_id!='') {
          $userModel->updateLastRequest('add_id');
        }


        if ($new_id!='0' AND $new_id!='') {
          //конец проверки лимитов
          //после создания настройки записываем +1 успешное создание
          $limit_n_id = ($limit_n_id +1);
          $userModel->updateLastRequest('limit_n_id', $limit_n_id);
        }

      }


      //вывод ошибок
      if(empty(self::$code)){
        Yii::$app->session->addFlash('error', "Шаблон кода пустой");
        Logs::AddSettingLogs("ID пользователя: $ID, тип настройки = $types
        Произошла ошибка создания настройки (шаблон пустой) Код операции: Add4-002", $ID);
      }

      else if($status == "false"){
        // Yii::$app->session->addFlash('error', "Подождите указанное время и попробуйте снова!");
        Logs::AddSettingLogs("ID пользователя: $ID, тип настройки = $types
        Произошла ошибка создания настройки (сработал лимит таймаута) Код операции: Add4-003", $ID);
      }

      else if($status2 == "false"){
        // Yii::$app->session->addFlash('error', "Превышен лимит создания настроек в сутки, попробуйте позже!");
        Logs::AddSettingLogs("ID пользователя: $ID, тип настройки = $types
        Произошла ошибка создания настройки (сработал лимит в сутки) Код операции: Add4-004", $ID);
      }

      else if($new_id=='0'){
        Yii::$app->session->addFlash('error', "Ошибка при создании настройки через API сервера, сообщите администратору!");
        Logs::AddSettingLogs("ID пользователя: $ID, тип настройки = $types
        Произошла ошибка создания настройки (API) Код операции: Add4-005", $ID);
      }

      else{
        //заменяем переменные в шаблоне
        $code = strtr(self::$code,array(
          '{urls}'            =>    $urls?? '',
          '{time}'            =>    $time?? '',
          '{referer}'         =>    $referer?? '',
          '{prosmotr}'        =>    $prosmotr?? '',
          '{linkmask}'        =>    $linkmask?? '',
          '{loadtime}'        =>    $loadtime?? '',
        ));


        if(!file_put_contents($dir.$namefile.'.js', $code)){

          Yii::$app->session->addFlash('warning', "Не создали файл настроек");
          Logs::AddSettingLogs("ID пользователя: $ID, тип настройки = $types
          Произошла ошибка создания настройки (файл не создан) Код операции: Add4-006", $ID);
        }

        else if($namefile==""){
          Yii::$app->session->addFlash('error', "Возникла ошибка создания настройки через API, сообщите администратору!");
          Logs::AddSettingLogs("ID пользователя: $ID, тип настройки = $types
          Ошибка создания настройки через API Код операции: Add4-008", $ID);
        }

        else if($namefile!=""){
          Yii::$app->session->addFlash('success', "Успешно cоздана настройка с ID: <a href='/robot/edit/$namefile'>$namefile</a>
          <br/>Настройка уже добавлена на управление в Ваш аккаунт!<br/>
          По умолчанию в настройке прописано ограничение на 100 уников/час");
          Logs::AddSettingLogs("ID пользователя: $ID, тип настройки = $types
          Успешно cоздана настройка с ID: $namefile Код операции: Add4-007", $ID);
        }

        else {
          Yii::$app->session->addFlash('error', "Возникла ошибка создания настройки через API, сообщите администратору!");
          Logs::AddSettingLogs("ID пользователя: $ID, тип настройки = $types
          Ошибка создания настройки через API Код операции: Add4-010", $ID);
        }
      }
    }

    return get_defined_vars();
  }
}
