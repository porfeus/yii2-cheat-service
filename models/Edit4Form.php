<?php

namespace app\models;

use Yii;
use yii\base\Model;
use app\components\ApiFunctions;
use app\components\Coderton;
use app\models\Logs;
use app\models\Jetid;
use app\models\Conf;

class Edit4Form extends Model
{
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

  public static function loader($definedVars){
    extract( $definedVars );

    if (file_exists($dir)) {

      //загружаем файл в буфер
      $filename = file_get_contents($dir);

      //парсим ссылочный реферер => $out_referer
      preg_match('~var refer1 \= \[(.*)\];~isU', $filename, $url_ref1);
      preg_match_all('~\'(.*)\'~', $url_ref1[1]??'', $url_refer1);
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

    return get_defined_vars();
  }

  /**
   * проверка формы
   */
  public static function loadForm($definedVars){
    extract( $definedVars );

    //задаем путь хранения настроек
    $dir = Yii::getAlias('@app/web/ID-S/ID/')."$id.js";

    $formHtml = '';

    //берем параметры настроек
    $conf = $userModel->settings[$id]->conf;

    //парсим настройки
    parse_str($conf, $output);
    $out_url = urldecode($output['url']??'');
    $out_pkt = $output['pkt']??'';
    $out_pkt2 = $output['pkt2']??'';
    $out_free = $output['free']??'';
    $out_nofree = $output['nofree']??'';
    $out_hideref = $output['hideref']??'';
    $out_crmin = $output['crmin']??'';
    $out_vipmin = $output['vipmin']??'';
    $out_cradd = $output['cradd']??'';
    $out_vipadd = $output['vipadd']??'';
    $out_cref = $output['cref']??'';
    $out_lc = $output['lc']??'';
    $out_chk = $output['chk']??'';
    $out_geonac = $output['geonac']??'';
    $out_uh = $output['uh']??'';
    $out_iph = $output['iph']??'';
    $out_iphl = $output['iphl']??'';
    $out_fid = $output['fid']??'';
    $out_name = urldecode($output['name']??'');
    $out_tml1 = $output['tml1']??'';
    $out_tml2 = $output['tml2']??'';
    $out_tmlc1 = $output['tmlc1']??'';
    $out_tmlc2 = $output['tmlc2']??'';
    $out_dontstop = $output['dontstop']??'';
    $out_dayunick = $output['dayunick']??'';
    $out_prtab = $output['prtab']??'';
    $out_ipc = $output['ipc']??'';
    $out_pkm = $output['pkm']??'';
    $out_pkh = $output['pkh']??'';
    $out_ssf = $output['ssf']??'';
    $out_v2 = $output['v2']??'';
    $out_v3 = $output['v3']??'';
    $out_v4 = $output['v4']??'';
    $out_v5 = $output['v5']??'';
    $out_tml = $output['tml']??'';
    $out_prs = $output['prs']??'';
    $out_hsf = $output['hsf']??'';
    $out_geo = $output['geo']??'';
    $out_rgeo = $output['rgeo']??'';
    $out_ggeo = $output['ggeo']??'';
    $out_second = $output['second']??'';
    $out_msf = $output['msf']??'';
    $out_ipex = $output['ipex']??'';
    $out_proxy = $output['proxy']??'';
    $out_exact = $output['exact']??'';
    $out_mouse = $output['mouse']??'';
    $out_speed = $output['speed']??'';
    $out_highspeed = $output['highspeed']??'';
    $out_li = $output['li']??'';
    $out_pst = urldecode($output['pst']??'');
    $out_ptm = $output['ptm']??'';
    $out_prstime1 = $output['prstime1']??'';
    $out_pac = $output['pac']??'';
    $out_purl = $output['purl']??'';
    $out_site = urldecode($output['site']??'');
    $out_sites = urldecode($output['sites'][0]??'');

    //совпал ли переданный Id в GET запросе с полученным из базы
    if ($id == $id_user) {

      $status_edit = "true";
      extract( self::loader( get_defined_vars() ) );
    }

    //настройка не была найдена в базе

    if ($id_user != $id) {
      // Yii::$app->session->addFlash('error', "Данная настройка не найдена в Вашем управлении или ее не существует!");
      Logs::AddSettingLogs("ID пользователя: $ID,
      Редактирование шаблона: тип настройки = $types  (не смог отредактировать, настройка не найдена в управлении) Код операции #Edit4-0001", $ID);
    }


    if (!file_exists($dir)) {
      Yii::$app->session->addFlash('warning', "Файл настройки $id не найден!");
      Logs::AddSettingLogs("ID пользователя: $ID,
      Редактирование шаблона: тип настройки = $types  (файл настройки не найден) Код операции #Edit4-0002", $ID);
    }

    if(isset($_POST['generate'])){

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

      //$date_generate = date("создана: дата d.m.y время H:i:s");

      //новый ID получать через api если разрешено юзеру!
      if($status_edit == "true"){
        $namefile = $id_user;

        if ( empty($linkmask) ) {
            $linkmask = 'http';
        }

        if ((int) $loadtime < 1) {
            $loadtime = 1;
        }

        //передаем данные в переменные API
        $SiteID=array();
        $Settings = array();
        $SiteID[]=$id;//id настройки
        $Settings['pkm'] = $out_pkm;//показов в день
        $Settings['pkh'] = $out_pkh;//показов в час
        $Settings['pkt'] = $out_pkt;//минимальное время показа
        $Settings['pkt2'] = $out_pkt2;//максимальное время показа
        $Settings['tml1'] = $out_tml1;//минимальный интервал
        $Settings['tml2'] = $out_tml2;//максимальный интервал
        $Settings['tmlc1'] = $out_tmlc1;//изменение интервала 1
        $Settings['tmlc2'] = $out_tmlc2;//изменение интервала 2
        $Settings['ssf'] = $out_ssf;//режим SSF
        $Settings['proxy'] = $out_proxy;//разрешить показ с прокси
        $Settings['ipc'] = $out_ipc;//Контроль уникальности посетителей ,0 - опция отключена (по умолчанию) ,1 - опция включена
        $Settings['second'] = $out_second;//Не показывать сайт посетителям с близкими IP-адресами. ,0 - опция отключена (по умолчанию) ,1 - опция включена
        $Settings['iphl'] = $out_iphl;//время уникальности посетителей 24ч(по умолчанию)-минимум
        $Settings['iph'] = $out_iph;//Время уникальности IP-адресов, макс. значение.(пр умолчанию)
        $Settings['dayunick'] = $out_dayunick;//Посуточная уникальность IP-адресов, отклонение от времени сервера(московского времени)
        $Settings['ipex'] = $out_ipex;//Эксклюзивные IP-адреса.
        $Settings['hideref'] = $out_hideref;//Скрытие HTTP_REFERER ,0 - опция отключена (по умолчанию) ,1 - опция включена
        $Settings['hsf'] = $out_hsf;//Скрытый серфинг ,0 - не показывать сайт в скрытом серфинге ,1 - разрешить показ сайта в скрытом серфинге ,2 - показывать сайт только в скрытом серфинге
        $Settings['uh'] = $out_uh;//Скрывать URL сайта ,0 - опция откл. ,1 - опция вкл.
        $Settings['name'] = $out_name;//имя настройки
        $Settings['fid'] = $out_fid;//Id папки сайта на jetswap
        $Settings['newfolder'] = '';//Имя новой папки сайта (применяется только при создании новой папки)
        $Settings['ggeo'] = $out_ggeo;//гео вкл выкл
        $Settings['rgeo'] = $out_rgeo;//выбранное гео
        $Settings['cref'] = trim($ref_sucsess);//реферер по заказу
        $Settings['prs'] = $out_prs;//Режим презентации ,0 - опция откл. (по умолчанию) ,1 - опция вкл.,2 для динамики
        $Settings['prstime'] = $time;//Время показа первого сайта презентации,1 сек для динамики, для других зависит от скорости сайта
        $Settings['prstime1'] = $out_prstime1;//Случайное отклонение от заданного времени, 0 по умолчанию
        $Settings['prsmin'] = '0';//Минимум страниц для просмотра презентации
        $Settings['prsmax'] = '0';//Максимум страниц для просмотра презентации
        $Settings['prtab'] = $out_prtab;//Количество дополнительных вкладок браузера (от 0 до 3)
        $Settings['prsref'] = '1';//Режим передачи реферера 0 - Передавать реферер по заказу на каждую страницу (по умолчанию)
        //1 - Передавать реферер по заказу на первую страницу, на остальные - текущую страницу в браузере как реферер
        //2 - Передавать реферер по заказу на первую страницу, на остальные - предыдущую заданную вами страницу как реферер
        $Settings['sitetitle'] = '';//Название сайта для каталога сайтов
        $Settings['sitedesk'] = '';//Описание сайта для каталога сайтов
        $Settings['catid'] = '0';//Тематика сайта для каталога
        $Settings['apisel'] = '200';//APISEL
        $Settings['sites'][] = $urls;//url главного сайта
        $Settings['geo'] = $out_geo;//включенное гео
        $Settings['exact'] = $out_exact;//Точное соблюдение лимитов показов
        $Settings['mouse'] = $out_mouse;//включение реальной мышки
        $Settings['speed'] = $out_speed;//скорость трафика минимальная
        $Settings['highspeed'] = $out_highspeed;//скорость трафика для больших файлов
        $Settings['li'] = $out_li;//включить показ на ли.ру
        $Settings['dontstop'] = $out_dontstop;//не останавливать показы по нагрузки на сайт
        $Settings['tmlrefresh'] = '1'; //галочка обновить сейчас

        //добавлеяем команды просмотра сайта
        $Settings['tms']['0'] = '5';
        $Settings['urls']['0'] = 'a;link;'.$linkmask.';click;-1';
        $Settings['cmds']['0'] = '5';

        $Settings['tms']['1'] = $loadtime;
        $Settings['urls']['1'] = 'last;0';
        $Settings['cmds']['1'] = '1';

        if ((int) $prosmotr < 1 || (int) $prosmotr > 21) {
            $prosmotr = 20;
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
        //сохраняем
        $Save = ApiFunctions::SiteEdit($SiteID, $Settings);
      }

      //вывод ошибок
      if(empty(self::$code)){
        Yii::$app->session->addFlash('error', "Шаблон кода пустой");
        Logs::AddSettingLogs("ID пользователя: $ID,
        Редактирование шаблона: тип настройки = $types  (шаблон кода постой) Код операции #Edit4-0003", $ID);
      }

      else if(strpos($urls, 'goo.gl') != false){
        Yii::$app->session->addFlash('warning', "Сокращатели Goo.gl и часть других запрещены для использования!");
        Logs::AddSettingLogs("ID пользователя: $ID, тип настройки = $types
        Ошибка создания настройки через API Код операции: Add4-004", $ID);
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

        if(!file_put_contents($dir, $code, LOCK_EX)){

          Yii::$app->session->addFlash('warning', "Не создали файл настроек");
          Logs::AddSettingLogs("ID пользователя: $ID, Редактирование шаблона: тип настройки = $types
          (не создали файл настроек) Код операции #Edit4-0005", $ID);

        }

        else if($namefile!=""){
          extract( self::loader( get_defined_vars() ) );
          Yii::$app->session->addFlash('success', "Настройка ID: $id успешно сохранена");
          Logs::AddSettingLogs("ID пользователя: $ID, Редактирование шаблона: тип настройки = $types
          (успешно изменен файл шаблона id: $id) Код операции #Edit4-0006", $ID);
        }

        else if($namefile==""){
          Yii::$app->session->addFlash('error', "Возникла ошибка создания настройки через API, сообщите администратору!");
          Logs::AddSettingLogs("ID пользователя: $ID, тип настройки = $types
          Ошибка создания настройки через API Код операции: Add4-007", $ID);
        }
        else {
          Yii::$app->session->addFlash('error', "Возникла ошибка создания настройки через API, сообщите администратору!");
          Logs::AddSettingLogs("ID пользователя: $ID, Редактирование шаблона: тип настройки = $types
          (ошибка создания настройки через API) Код операции #Edit4-0008", $ID);
        }
      }
    }

    return get_defined_vars();
  }
}
