<?php

namespace app\models;

use Yii;
use yii\base\Model;
use app\components\ApiFunctions;
use app\components\Coderton;
use app\models\Logs;
use app\models\Jetid;
use app\models\Conf;

class Add3Form extends Model
{
  use \app\components\LimitsAndConfigTrait;

  //начало кода вставки
  public static $code = <<<SERVISCODE

function myRandom (from, to)  {return Math.floor((Math.random() * (to - from + 1)) + from);}

/*вводим запросы для поискового реферера*/
var words = [
{zaprospoisk}
];

var words2 = [
{zaprospoisk2}
];

    /*Яндекс*/
   /*выбираем рандомный номер строки  списка ключевиков (случайное слово из заданных)*/
  var rnd3 = Math.floor(Math.random()*((words.length - 1) + 1));
   /*получаем ключевую фразу*/
  var zapros = words[rnd3];

  /*Google*/
  /*выбираем рандомный номер строки  списка ключевиков (случайное слово из заданных)*/
  var rnd4 = Math.floor(Math.random()*((words2.length - 1) + 1));
  /*получаем ключевую фразу*/
  var zapros2 = words2[rnd4];

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
/*var host=<({host})>
var time-1<({time1})>
var time-2<({time2})>
var time-3<({time3})>
var time-4<({time4})>
Картинки, JS, Cookies
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
var cmdparam="alert('prs::<get(key)>::setvar::procent1='+procent1);alert('prs::<get(key)>::setvar::variantzahoda='+variantzahoda);alert('prs::<get(key)>::setvar::rand2='+rand2);alert('prs::<get(key)>::setvar::zapros='+zapros);alert('prs::<get(key)>::setvar::zapros2='+zapros2);";
alert("prs::" + prskey + "::add::" + cmdname + "::" + cmdtime + "::" + cmdparam);

if (variantzahoda >= {procent1}){
/*Google подсказки*/
var cmdname="nav";
var cmdtime=myRandom({time1},{time2});
var cmdparam="https://www.google.ru";
alert("prs::" + prskey + "::add::" + cmdname + "::" + cmdtime + "::" + cmdparam);

var cmdname="script";
var cmdtime={time3};
var cmdparam="var del=200;var nex=0; var msg=new Array('<getvar(zapros2)>'); function start_print() {do_print(msg[0], 0, 1);} function do_print(text, pos, dir)  {var out=text.substring(0, pos); document.getElementsByName('q')[0].value=out; pos+=dir; setTimeout('do_print(\"'+text+'\",'+pos+','+(+dir)+')', del);} start_print();";
alert("prs::" + prskey + "::add::" + cmdname + "::" + cmdtime + "::" + cmdparam);

var cmdname="submit";
var cmdtime={time4};
var cmdparam="0";
alert("prs::" + prskey + "::add::" + cmdname + "::" + cmdtime + "::" + cmdparam);

}

else{

/*Яндекс подсказки*/
var cmdname="nav";
var cmdtime=myRandom({time1},{time2});
var cmdparam="https://www.yandex.ru";
alert("prs::" + prskey + "::add::" + cmdname + "::" + cmdtime + "::" + cmdparam);

var cmdname="script";
var cmdtime={time3};
var cmdparam="var del=200;var nex=0; var msg=new Array('<getvar(zapros)>'); function start_print() {do_print(msg[0], 0, 1);} function do_print(text, pos, dir)  {var out=text.substring(0, pos); document.getElementsByName('text')[0].value=out; pos+=dir; setTimeout('do_print(\"'+text+'\",'+pos+','+(+dir)+')', del);} start_print();";
alert("prs::" + prskey + "::add::" + cmdname + "::" + cmdtime + "::" + cmdparam);

var cmdname="click";
var cmdtime={time4};
var cmdparam="button;text;Найти;click";
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

    $types = 'type-3';

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
      $time3 = (int) $_POST['time3'];
      $time4 = (int) $_POST['time4'];

      //поисковые запросы  Яндекс
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
      		if (!preg_match('/[^a-zа-я0-9\!\?\-_,\.\:\s\=;№&\(\)@\$\/]+/ui', $v)) {
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



      //поисковые запросы  Google
      // поисковые запросы
      // любые слова которые не содержат запрещенных символов
      // если содержат не включаются в $zaprospoisk2

      $zaprospoisk2 = $_POST['zaprospoisk'];
      $zaprospoisk2 = explode("\r\n", $zaprospoisk2);

      foreach($zaprospoisk2 as $k => $v) {
      	if (empty($v)) {
              unset($zaprospoisk2[$k]);
      	} else {
      		$v = trim($v);
      		if (!preg_match('/[^a-zа-я0-9\!\?\-_,\.\:\s\=;№&\(\)@\$\/]+/ui', $v)) {
      			$zaprospoisk2[$k] = $v;
      		} else {
      			unset($zaprospoisk2[$k]);
      		}
      	}
      }

      $zaprospoisk2 = array_chunk($zaprospoisk2, 500);
      $zaprospoisk2 = "'" . implode("'," . PHP_EOL . "'", $zaprospoisk2[0]??[]) . "'";
      $zap = ",";
      $zaprospoisk2 = $zaprospoisk2 . $zap;



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
            $idModel->config_type = 'type-3';
            $idModel->save(false);
        }

        if($new_id=='0' OR $new_id==''){
            Yii::$app->session->addFlash('error', "Произошла ошибка создания API настройки!");
            Logs::AddSettingLogs("ID пользователя: $ID, тип настройки = $types  Произошла ошибка создания API настройки Код операции: Add3-001", $ID);
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
        Logs::AddSettingLogs("ID пользователя: $ID, тип настройки = $types  Произошла ошибка создания настройки  (шаблон пустой) Код операции: Add3-002", $ID);
      }

      elseif($status == "false"){
        // Yii::$app->session->addFlash('error', "Подождите указанное время и попробуйте снова!");
        Logs::AddSettingLogs("ID пользователя: $ID, тип настройки = $types  Произошла ошибка создания настройки (сработал лимит таймаута) Код операции: Add3-003", $ID);
      }

      elseif($status2 == "false"){
        // Yii::$app->session->addFlash('error', "Превышен лимит создания настроек в сутки, попробуйте позже!");
        Logs::AddSettingLogs("ID пользователя: $ID, тип настройки = $types  Произошла ошибка создания настройки (сработал лимит в сутки) Код операции: Add3-004", $ID);
      }

      elseif($new_id=='0'){
        Yii::$app->session->addFlash('error', "Ошибка при создании настройки через API, сообщите администратору!");
        Logs::AddSettingLogs("ID пользователя: $ID, тип настройки = $types  Произошла ошибка создания настройки (API) Код операции: Add3-005", $ID);
      }
      else{
        //заменяем переменные в шаблоне
         $code = strtr(self::$code,array(
        '{urlvhoda}'            =>    $urlvhoda?? '',
        '{time1}'               =>    $time1?? '',
        '{time2}'               =>    $time2?? '',
        '{time3}'               =>    $time3?? '',
        '{time4}'               =>    $time4?? '',
        '{zaprospoisk}'         =>    $zaprospoisk?? '',
        '{zaprospoisk2}'        =>    $zaprospoisk2?? '',
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
        '{procent1}'            =>    $procent1?? ''
      ));

        if(!file_put_contents($dir.$namefile.'.js', $code)){

            Yii::$app->session->addFlash('warning', "Не создали файл настроек");
            Logs::AddSettingLogs("ID пользователя: $ID, тип настройки = $types  Произошла ошибка создания настройки (файл не создан) Код операции: Add3-006", $ID);
        }
        else if($namefile!=""){
             Yii::$app->session->addFlash('success', "Успешно cоздана настройка с ID: <a href='/robot/edit/$namefile'>$namefile</a>
             <br/>Настройка уже добавлена на управление в Ваш аккаунт!<br/>
             По умолчанию в настройке прописано ограничение на 100 уников/час");
             Logs::AddSettingLogs("ID пользователя: $ID, тип настройки = $types  Успешно cоздана настройка с ID: $namefile Код операции: Add3-007", $ID);
        }
        else {
            Yii::$app->session->addFlash('error', "Возникла ошибка создания настройки через API, сообщите администратору!");
            Logs::AddSettingLogs("ID пользователя: $ID, тип настройки = $types  шибка создания настройки через API Код операции: Add3-008", $ID);
        }
      }
    }

    return get_defined_vars();
  }
}
