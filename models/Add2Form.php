<?php

namespace app\models;

use Yii;
use yii\base\Model;
use app\components\ApiFunctions;
use app\components\Coderton;
use app\models\Logs;
use app\models\Jetid;
use app\models\Conf;

class Add2Form extends Model
{
  use \app\components\LimitsAndConfigTrait;

  //начало кода вставки
  public static $code = <<<SERVISCODE

function myRandom (from, to)  {return Math.floor((Math.random() * (to - from + 1)) + from);}

/*вводим точки входа сайта*/
var url_vhoda = [
{urlvhoda}
];

var rnd1 = Math.floor(Math.random() * ((url_vhoda.length - 1) + 1));
var url_vhoda = url_vhoda[rnd1];

/*определяем ссылочный реферер*/
var refer1 = [
{refer1}
];

var rnd2 = Math.floor(Math.random() * ((refer1.length - 1) + 1));
var refer1 = refer1[rnd2];

/*Картинки, JS, Cookies
img=1 - загрузка изображений включена
img=0 - загрузка изображений выключена
js=1 - JavaScript включен
js=0 - JavaScript отключен
mouse=1 - включить перемещение мыши
mouse=0 - выключить перемещение мыши
csp=1 - включить управление Content Security Policy (CSP)
csp=0 - выключить управление Content Security Policy (CSP)*/

var cmdname="script";
var cmdtime=1;
var cmdparam="<set(img={img})><set(js={js})><set(mouse={mouse})><set(csp={csp})>";
alert("prs::" + prskey + "::add::" + cmdname + "::" + cmdtime + "::" + cmdparam);

/*вводим запросы для поискового реферера*/
var words = [
{zaprospoisk}
];

/*начало настроек поисковика
@param0({useragent-0}); = "Закладки about:blank"
@param1({useragent-1}); 1 = yandex.ru
@param2({useragent-2}); 2 = google.ru
@param3({useragent-3}); 3 = nigma .ru
@param4({useragent-4}); 4 = search.qip.ru
@param5({useragent-5}); 5 = go.mail.ru
@param6({useragent-6}); 6 = nova.rambler.ru
@param7({useragent-7}); 7 = google.com.ua
@param8({useragent-8}); 8 = search.meta.ua
@param9({useragent-9}); 9 = yandex.ua
@param10({useragent-10}); 10 = search.bigmir.net
@param11({useragent-11}); 11 = bing.com
@param12({useragent-12}); 12 = yandex.com
@param13({useragent-13}); 13 = google.com
конец настроек поисковика */

function generateString(num){
  var letters = [
    "ABCDEFGHIGKLMNOPQRSTUVWXYZ",
    "abcdefghigklmnopqrstuvwxyz",
    "1234567890",
  ].join("");
  var str = "";
  for(var i=0; i < num; i++){
    str+= letters.charAt(parseInt(Math.random()*letters.length));
  }
  return str;
}

    /*составляем массив поисковых машин*/
   var arr = new Array({arrpoiskoviki});

  /*выбираем случайный поисковик (номер из заданных, например яндекс или гугл)*/
  var rand = arr[Math.floor(Math.random()*(arr.length))];

   /*выбираем рандомный номер строки  списка ключевиков (случайное слово из заданных)*/
  var rnd3 = Math.floor(Math.random()*((words.length - 1) + 1));

   /*получаем ключевую фразу*/
  var zapros = words[rnd3];

/*начинаем выбирать поисковик, скрипт генерации поискового реферера из ключевых слов*/

/*закладки*/
if (0 == rand){
var refer2 = 'about:blank';
      }

/*yandex.ru*/
else if (1 == rand){
var a = 'http://yandex.ru/yandsearch?date=&text=';
var b = '&site=&rstr=&zone=all&wordforms=all&lang=ru&within=0&from_day=&from_month=&from_year=&to_day=&to_month=&to_year=&mime=all&numdoc=10&lr=11476';
var predvar_zapros = encodeURI(zapros);
var refer2 = a + predvar_zapros + b;
      }

/*google.ru*/
else if (2 == rand){
var a = 'http://www.google.ru/search?hl=ru&newwindow=1&q=';
var b = '&aq=f&aqi=&aql=&oq=';
var predvar_zapros = encodeURI(zapros);
var refer2 = a + predvar_zapros + b;
      }

/*nigma.ru*/
else if (3 == rand){
var a = 'http://nigma.ru/?s=';
var b = '&t=web&gl=1&yh=1&ms=1&yn=1&rm=1&av=1&ap=1&nm=1&lang=all&from=1';
var predvar_zapros = encodeURI(zapros);
var refer2 = a + predvar_zapros + b;
      }

/*search.qip.ru*/
else if (4 == rand){
var a = 'http://search.qip.ru/search/?query=';
var predvar_zapros = encodeURI(zapros);
var refer2 = a + predvar_zapros;
      }

/*go.mail.ru*/
else if (5 == rand){
var a = 'http://go.mail.ru/search?mailru=1&drch=e&mg=1&q=';
var b = '&rch=e';
var predvar_zapros = encodeURI(zapros);
var refer2 = a + predvar_zapros + b;
      }

/*nova.rambler.ru*/
else if (6 == rand){
var a = 'http://nova.rambler.ru/srch?query=';
var b = '123&and=1&dlang=2&mimex=0&st_date=&end_date=&news=0&limitcontext=0&exclude=&filter=&sort=3&pagelen=10&adult=soft&gopic=%D0%9D%D0%B0%D0%B9%D1%82%D0%B8';
var predvar_zapros = encodeURI(zapros);
var refer2 = a + predvar_zapros + b;
      }

/*google.com.ua*/
else if (7 == rand){
var a = 'http://www.google.com.ua/search?hl=ru&source=hp&q=';
var b = '&aq=f&aqi=&aql=&oq=';
var predvar_zapros = encodeURI(zapros);
var refer2 = a + predvar_zapros + b;
      }

/*search.meta.ua*/
else if (8 == rand){
var a = 'http://search.meta.ua/search.asp?q=';
var b = '&m=';
var predvar_zapros = encodeURI(zapros);
var refer2 = a + predvar_zapros + b;
      }

/*yandex.ua*/
else if (9 == rand){
var a = 'http://yandex.ua/yandsearch?date=&text=';
var b = '&site=&rstr=&zone=all&wordforms=all&lang=ru&within=0&from_day=&from_month=&from_year=&to_day=&to_month=&to_year=&mime=all&numdoc=10&lr=143';
var predvar_zapros = encodeURI(zapros);
var refer2 = a + predvar_zapros + b;
      }

/*search.bigmir.net*/
else if (10 == rand){
var a = 'http://search.bigmir.net/?cref=http%3A%2F%2Fi.bigmir.net%2Fcse%2F2_ru.xml&cof=FORID%3A11&channel=5988463740&z=';
var predvar_zapros = encodeURI(zapros);
var refer2 = a + predvar_zapros;
      }

/*bing.com*/
else if (11 == rand){
var a = 'http://www.bing.com/search?q=';
var b = '&setmkt=ru-RU&go=&form=QBRE&filt=all&qs=n&sk=';
var predvar_zapros = encodeURI(zapros);
var refer2 = a + predvar_zapros + b;
      }

/*yandex.com*/
else if (12 == rand){
var a = 'http://yandex.com/search?date=&text=';
var b = '&site=&zone=all&wordforms=all&lang=ru&within=0&from_day=&from_month=&from_year=&to_day=&to_month=&to_year=&mime=all&numdoc=10&lr=11476';
var predvar_zapros = encodeURI(zapros);
var refer2 = a + predvar_zapros + b;
      }

/*google.com*/
else if (13 == rand){
var a = 'https://www.google.com/search?source=hp&ei='+ generateString(22) +'&q=';
var b = '&oq=';
var predvar_zapros = encodeURI(zapros);
var refer2 = a + predvar_zapros + b + predvar_zapros;
      }

else {
var refer2 = 'about:blank';
}
 /*тип хождения по сайту
 @param если m = 5 (5 коротких)
 @param если m = 4 (4 полу-длинных)
 @param если m = 3 (3 средних)
 @param если m = 2 (2 длинных)
 @param если m = 1 (1 очень длинный)
 конец настроек */

  /*вариант захода на сайт*/
  var variantzahoda = myRandom(1,100);

  /*вставлять или нет собственный код после перехода*/
  var coderton = {coderton};

  /*процент поискового реферера*/
  var procent1 = {procent1};

   /*составляем массив для браузеров*/
   var arr2 = new Array({brouser});

  /*выбираем случайный поисковик (номер из заданных, например яндекс или гугл)*/
  var rand2 = arr2[Math.floor(Math.random()*(arr2.length))];

  /*чистим куки (да = 1, нет =0)*/
  var cookies = {cookies};

  /*собственный код после перехода*/
  /*{uniks_code}({comment_code}){/uniks_code}*/
  /*var time-1<({time1})>
  var time-2<({time2})>*/


 if (cookies == 1){
var cmdname="cookies";
var cmdtime=1;
var cmdparam="ALL";
alert("prs::" + prskey + "::add::" + cmdname + "::" + cmdtime + "::" + cmdparam);
}

/*выбираем тип браузера
IE<({br-0})>; - браузер Microsoft Internet Explorer
FF<({br-1})>; - браузер Mozilla Firefox
Chrome<({br-2})>; - браузер Google Chrome
Opera<({br-3})>; - браузер Opera
Safari<({br-4})>; - браузер Safari
Mobile<({br-5})>; - браузеры мобильных устройств
Other<({br-6})>; - другие браузеры */

if (rand2 == 0){
alert("prs::" + prskey + "::agent::group=IE");
}

else if (rand2 == 1){
alert("prs::" + prskey + "::agent::group=FF");
}

else if (rand2 == 2){
alert("prs::" + prskey + "::agent::group=Chrome");
}

else if (rand2 == 3){
alert("prs::" + prskey + "::agent::group=Opera");
}

else if (rand2 == 4){
alert("prs::" + prskey + "::agent::group=Safari");
}

else if (rand2 == 5){
alert("prs::" + prskey + "::agent::group=Mobile");
}

else{
alert("prs::" + prskey + "::agent::group=Other");
}

/*получаем переменные для показа на вкладке "журнал", при тестинге скрипта*/
var cmdname="script";
var cmdtime=1;
var cmdparam="alert('prs::<get(key)>::setvar::refer1='+refer1);alert('prs::<get(key)>::setvar::refer2='+refer2);alert('prs::<get(key)>::setvar::zapros='+zapros);alert('prs::<get(key)>::setvar::rand='+rand);alert('prs::<get(key)>::setvar::url_vhoda='+url_vhoda);alert('prs::<get(key)>::setvar::procent1='+procent1);alert('prs::<get(key)>::setvar::variantzahoda='+variantzahoda);alert('prs::<get(key)>::setvar::rand2='+rand2);";
alert("prs::" + prskey + "::add::" + cmdname + "::" + cmdtime + "::" + cmdparam);

if (variantzahoda >= {procent1}){
/*поисковой реферер refer2*/
var cmdname="nav";
var cmdtime=myRandom({time1},{time2});
var cmdparam="<getvar(url_vhoda)><referer(<getvar(refer2)>)>";
alert("prs::" + prskey + "::add::" + cmdname + "::" + cmdtime + "::" + cmdparam);
}

else{
/*ссылочный реферер refer1*/
var cmdname="nav";
var cmdtime=myRandom({time1},{time2});
var cmdparam="<getvar(url_vhoda)><referer(<getvar(refer1)>)>";
alert("prs::" + prskey + "::add::" + cmdname + "::" + cmdtime + "::" + cmdparam);
}

/*вставляем уникальный код после перехода*/
if (coderton == 1){
{codert_end}
}

alert("prs::" + prskey + "::set::cmdindex=1;cmdtime=0;");

SERVISCODE;
//конец вставки кода

  /**
   * проверка формы
   */
  public static function loadForm($definedVars){
    extract( $definedVars );

    $types = 'type-2';

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
      $time1 = (int) $_POST['time1'];
      $time2 = (int) $_POST['time2'];


      //берем значения процентов поисковиков
      $array = $_POST['check'];
      $new_array = array();
      foreach ($array AS $key=>$value){
      	if (!empty($value) and in_array($key, range(0, 13) ))
      		for($i=0; $i < $value; $i++)
      			$new_array[] = $key;
      }

      if (count($new_array) > 0){
      	$arrpoiskoviki = implode(', ', $new_array);

      }


      // URL входа
      // Должен обязательно начинаться с https или http либо IP адреса
      // далее должен не содержать запрещающих символов в регулярном выражении
      // если содержит - то не вклюается в $urlvhoda
      // регулярные выражения разные, их тоже просьба проверить

      $urlvhoda = $_POST['urlvhoda'];
      $urlvhoda = explode("\r\n", $urlvhoda);

      foreach($urlvhoda as $k => $v) {
      	if (empty($v)) {
              unset($urlvhoda[$k]);
      	} else {
      		$v = trim($v);
      		if (preg_match('/^(https?)|([0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3})/ui', $v) && !preg_match('/[^a-zа-я0-9\!\?\-_,\.\s\=;&\(\)@\$\:\/%#+]+/ui', $v)) {
      			$urlvhoda[$k] = $v;
              } else {
                  unset($urlvhoda[$k]);
      		}
      	}
      }

      $urlvhoda = array_chunk($urlvhoda, 500);
      $urlvhoda = "'" . implode("'," . PHP_EOL . "'", $urlvhoda[0]??[]) . "'";
      $zap = ",";
      $urlvhoda = $urlvhoda . $zap;

      // ссылочный реферер
      // Должен обязательно начинаться только с http (+ обновление 19.06.2017 также и https)
      // далее должен не содержать запрещающих символов в регулярном выражении
      // если содержит - то не вклюается в $refer1
      // регулярные выражения разные, их тоже просьба проверить
      //preg_match('/^(https?)|([0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3})/ui', $v) новый вариант с https
      //preg_match('/^http\:/', $v) старый вариант без https

      $refer1 = $_POST['refer1'];
      $refer1 = explode("\r\n", $refer1);

      foreach($refer1 as $k => $v) {
      	if (empty($v)) {
              unset($refer1[$k]);
      	} else {
      		$v = trim($v);
      		if ($v == 'about:blank' || (preg_match('/^(https?)|([0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3})/ui', $v) && !preg_match('/[^a-zа-я0-9\!\?\-_,\.\:\s\=;&\(\)@\$\/%#+]+/ui', $v))) {
      			$refer1[$k] = $v;
      		} else {
                  unset($refer1[$k]);
      		}
      	}
      }

      $refer1 = array_chunk($refer1, 500);
      $refer1 = "'" . implode("'," . PHP_EOL . "'", $refer1[0]??[]) . "'";
      $zap = ",";
      $refer1 = $refer1 . $zap;

      // поисковые запросы
      // любые слова которые не содержат запрещенных символов
      // если содержат не включаются в $zaprospoisk

      $zaprospoisk = $_POST['zaprospoisk'];
      $zaprospoisk = explode("\r\n", $zaprospoisk);

      foreach($zaprospoisk as $k => $v) {
      	if (empty($v)) {
              unset($zaprospoisk[$k]);
      	} else {
      		$v = trim($v);
      		if (!preg_match('/[^a-zа-я0-9\!\?\-_,\.\:\s\=;&\(\)@\$\/]+/ui', $v)) {
      			$zaprospoisk[$k] = $v;
      		} else {
      			unset($zaprospoisk[$k]);
      		}
      	}
      }

      $zaprospoisk = array_chunk($zaprospoisk, 500);
      $zaprospoisk = "'" . implode("'," . PHP_EOL . "'", $zaprospoisk[0]??[]) . "'";
      $zap = ",";
      $zaprospoisk = $zaprospoisk . $zap;

      //виды браузеров
      $array2 = $_POST['brouser'];
      $new_array2 = array();
      foreach ($array2 AS $key=>$value){
      	if (!empty($value) and in_array($key, range(0, 6) ))
      		for($i=0; $i < $value; $i++)
      			$new_array2[] = $key;
      }

      if (count($new_array2) > 0){
      	$brouser = implode(', ', $new_array2);
      }

      //IMG
      $img = (int) $_POST['img'];

      //JS
      $js = (int) $_POST['js'];

      //Mouse
      $mouse = (int) $_POST['mouse'];

    	// csp
    	$csp = (int) $_POST['csp'];

      //Куки
      $cookies = (int) $_POST['cookies'];

      //Вариант захода на сайт
      $procent1 = (int) $_POST['procent-1'];
      $procent2 = (int) $_POST['procent-2'];

      //использование собственного кода после перехода
      $coderton = (int) $_POST['coderton']; //да или нет
      $codert = $_POST['codert']; //сам код

      $codert = Coderton::commentsEncode($codert);

      $date_generate = date("создана: дата d.m.y время H:i:s");
      //новый ID получать через api если разрешено юзеру!
      if($status == "true" AND $status2 == "true"){
          extract( self::loadConfigApi( get_defined_vars() ) );
          //$new_id = 123457; получен
          $namefile = $new_id;

          if ($coderton=='1'){
            $id = $new_id;

            extract( Coderton::run( get_defined_vars() ) );
            //берем переменную $codert_end с вставкой кода
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
            $idModel->config_type = 'type-2';
            $idModel->save(false);
          }

          if($new_id=='0' OR $new_id==''){
              Yii::$app->session->addFlash('error', "Произошла ошибка создания API настройки!");
              Logs::AddSettingLogs("ID пользователя: $ID, тип настройки = $types  Произошла ошибка создания API настройки Код операции: Add2-001", $ID);
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
        Logs::AddSettingLogs("ID пользователя: $ID, тип настройки = $types  Произошла ошибка создания настройки  (шаблон пустой) Код операции: Add2-002", $ID);
      }

      elseif(!$urlvhoda){
        Yii::$app->session->addFlash('error', "Не правильный формат URL адреса");
        Logs::AddSettingLogs("ID пользователя: $ID, тип настройки = $types  Произошла ошибка создания настройки  Код операции: Add2-003", $ID);
      }

      elseif($status == "false"){
        // Yii::$app->session->addFlash('error', "Подождите указанное время и попробуйте снова!");
        Logs::AddSettingLogs("ID пользователя: $ID, тип настройки = $types  Произошла ошибка создания настройки (сработал лимит таймаута) Код операции: Add2-004", $ID);
      }

      elseif($status2 == "false"){
        // Yii::$app->session->addFlash('error', "Превышен лимит создания настроек в сутки, попробуйте позже!");
        Logs::AddSettingLogs("ID пользователя: $ID, тип настройки = $types  Произошла ошибка создания настройки (сработал лимит в сутки) Код операции: Add2-005", $ID);
      }

      elseif($new_id=='0'){
        Yii::$app->session->addFlash('error', "Ошибка при создании настройки через API, сообщите администратору!");
        Logs::AddSettingLogs("ID пользователя: $ID, тип настройки = $types  Произошла ошибка создания настройки (API) Код операции: Add2-006", $ID);
      }
      else{
        //заменяем переменные в шаблоне
         $code = strtr(self::$code,array(
        '{urlvhoda}'            =>    $urlvhoda?? '',
        '{time1}'               =>    $time1?? '',
        '{time2}'               =>    $time2?? '',
        '{zaprospoisk}'         =>    $zaprospoisk?? '',
        '{refer1}'              =>    $refer1?? '',
        '{arrpoiskoviki}'       =>    $arrpoiskoviki?? '',
        '{brouser}'             =>    $brouser?? '',
        '{img}'                 =>    $img?? '',
        '{js}'                  =>    $js?? '',
        '{mouse}'               =>    $mouse?? '',
        '{csp}'                 =>    $csp?? '',
        '{variantzahoda}'       =>    $variantzahoda?? '',
        '{codert}'              =>    $codert?? '',
        '{coderton}'            =>    $coderton?? '',
        '{procent2}'            =>    $procent2?? '',
        '{codert_end}'          =>    htmlspecialchars_decode($codert_end?? ''),
        '{cookies}'             =>    $cookies?? '',
        '{comment_code}'        =>    $comment_code?? '',
        '{br-0}'                =>    $array2[0]?? '',
        '{br-1}'                =>    $array2[1]?? '',
        '{br-2}'                =>    $array2[2]?? '',
        '{br-3}'                =>    $array2[3]?? '',
        '{br-4}'                =>    $array2[4]?? '',
        '{br-5}'                =>    $array2[5]?? '',
        '{br-6}'                =>    $array2[6]?? '',
        '{useragent-0}'         =>    $array[0]?? '',
        '{useragent-1}'         =>    $array[1]?? '',
        '{useragent-2}'         =>    $array[2]?? '',
        '{useragent-3}'         =>    $array[3]?? '',
        '{useragent-4}'         =>    $array[4]?? '',
        '{useragent-5}'         =>    $array[5]?? '',
        '{useragent-6}'         =>    $array[6]?? '',
        '{useragent-7}'         =>    $array[7]?? '',
        '{useragent-8}'         =>    $array[8]?? '',
        '{useragent-9}'         =>    $array[9]?? '',
        '{useragent-10}'        =>    $array[10]?? '',
        '{useragent-11}'        =>    $array[11]?? '',
        '{useragent-12}'        =>    $array[12]?? '',
        '{useragent-13}'        =>    $array[13]?? '',
        '{procent1}'            =>    $procent1?? ''
      ));



        if(!file_put_contents($dir.$namefile.'.js', $code)){

            Yii::$app->session->addFlash('warning', "Не создали файл настроек");
            Logs::AddSettingLogs("ID пользователя: $ID, тип настройки = $types  Произошла ошибка создания настройки (файл не создан) Код операции: Add2-007", $ID);
        }
        else if($namefile!=""){
             Yii::$app->session->addFlash('success', "Успешно cоздана настройка с ID: <a href='/robot/edit/$namefile'>$namefile</a>
             <br/>Настройка уже добавлена на управление в Ваш аккаунт!<br/>
             По умолчанию в настройке прописано ограничение на 100 уников/час");
             Logs::AddSettingLogs("ID пользователя: $ID, тип настройки = $types  Успешно cоздана настройка с ID: $namefile Код операции: Add2-008", $ID);
        }
        else {
            Yii::$app->session->addFlash('error', "Возникла ошибка создания настройки через API, сообщите администратору!");
            Logs::AddSettingLogs("ID пользователя: $ID, тип настройки = $types  шибка создания настройки через API Код операции: Add2-009", $ID);
        }
      }
    }

    return get_defined_vars();
  }
}
