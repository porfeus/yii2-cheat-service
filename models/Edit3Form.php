<?php

namespace app\models;

use Yii;
use yii\base\Model;
use app\components\ApiFunctions;
use app\components\Coderton;
use app\models\Logs;
use app\models\Jetid;
use app\models\Conf;

class Edit3Form extends Model
{
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

  public static function loader($definedVars){
    extract( $definedVars );

    if (file_exists($dir)) {

      //загружаем файл в буфер
      $filename=file_get_contents($dir);

      //парсим ключевые слова => $out_words
      preg_match('~var words \= \[(.*)\];~isU', $filename, $words_vh);
      preg_match_all('~\'(.*)\'~', $words_vh[1]??'', $words);
      $out_words = '';
      foreach($words[1] as $val){
        $out_words .= $val."\n";
      }
      $out_words = trim($out_words);

      //парсим ключевые слова => $out_words2
      preg_match('~var words2 \= \[(.*)\];~isU', $filename, $words_vh2);
      preg_match_all('~\'(.*)\'~', $words_vh2[1], $words2);
      $out_words2 = '';
      foreach($words2[1] as $val2){
        $out_words2 .= $val2."\n";
      }
      $out_words2 = trim($out_words2);


      //парсим вкл/выкл изображения => $img[1];
      preg_match('|<set\(img=(.*)\)>|Uis', $filename, $img);

      //парсим вкл/выкл JS => $js[1];
      preg_match('|<set\(js=(.*)\)>|Uis', $filename, $js);

      //парсим вкл/выкл mouse => $mouse[1];
      preg_match('|<set\(mouse=(.*)\)>|Uis', $filename, $mouse);

      //парсим вкл/выкл csp => $csp[1];
      preg_match('|<set\(csp=(.*)\)>|Uis', $filename, $csp);

      //парсим вкл/выкл cookies => $cookies[1];
      preg_match('|var cookies = (.*)\;|Uis', $filename, $cookies);

      //парсим время один time1 => $time1[1];
      preg_match('|var time-1<\((.*)\)>|Uis', $filename, $time1);

      //парсим время два time2 => $time2[1];
      preg_match('|var time-2<\((.*)\)>|Uis', $filename, $time2);

      //парсим время два time2 => $time3[1];
      preg_match('|var time-3<\((.*)\)>|Uis', $filename, $time3);

      //парсим время два time2 => $time3[1];
      preg_match('|var time-4<\((.*)\)>|Uis', $filename, $time4);

      //парсим браузеры  $IE[1];
      preg_match('|IE<\((.*)\)>;|Uis', $filename, $IE);
      preg_match('|FF<\((.*)\)>;|Uis', $filename, $FF);
      preg_match('|Chrome<\((.*)\)>;|Uis', $filename, $Chrome);
      preg_match('|Opera<\((.*)\)>;|Uis', $filename, $Opera);
      preg_match('|Safari<\((.*)\)>;|Uis', $filename, $Safari);
      preg_match('|Mobile<\((.*)\)>;|Uis', $filename, $Mobile);
      preg_match('|Other<\((.*)\)>;|Uis', $filename, $Other);

      //процент поискового реферера
      preg_match('|var procent1 = (.*)\;|Uis', $filename, $procent1);
      $procent2 = (100 - ($procent1[1] ?? 0));


      //вставка собственного кода
      preg_match('|{uniks_code}\((.*)\){/uniks_code}|Uis', $filename, $comment_code);
      preg_match('|var coderton = (.*)\;|Uis', $filename, $coderton);
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

    //совпал ли переданный Id в GET запросе с полученным из базы
    if ($id == $id_user) {

      $status_edit = "true";

      extract( self::loader( get_defined_vars() ) );
    }

    //настройка не была найдена в базе

    if ($id_user != $id) {
      // Yii::$app->session->addFlash('error', "Данная настройка не найдена в Вашем управлении или ее не существует!");
      Logs::AddSettingLogs("ID пользователя: $ID, Редактирование шаблона: тип настройки = $types (не смог отредактировать, настройка не найдена в управлении) Код операции #Edit3-0001", $ID);
    }



    if (!file_exists($dir)) {
      Yii::$app->session->addFlash('warning', "Файл настройки $id не найден!");
      Logs::AddSettingLogs("ID пользователя: $ID, Редактирование шаблона: тип настройки = $types  (файл настройки не найден) Код операции #Edit3-0002", $ID);
    }

    if(isset($_POST['generate'])){

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
      $coderton = (int) ($_POST['coderton']??'0'); //да или нет
      $codert = $_POST['codert']; //сам код

      $codert = Coderton::commentsEncode($codert);

      if ($coderton=='1'){
        extract( Coderton::run( get_defined_vars() ) );
      //берем переменную $codert_end с вставкой кода
      }

      //$date_generate = date("создана: дата d.m.y время H:i:s");

      //новый ID получать через api если разрешено юзеру!
      if($status_edit == "true"){
        $namefile = $id_user;
      }

      //вывод ошибок
      if(empty(self::$code)){
        Yii::$app->session->addFlash('error', "Шаблон кода пустой");
        Logs::AddSettingLogs("ID пользователя: $ID, Редактирование шаблона: тип настройки = $types  (шаблон кода постой) Код операции #Edit3-0003", $ID);
      }

      else{

        if( is_array($comment_code) )
          $comment_code = ''; // если coderton отключен


        //заменяем переменные в шаблоне
        $code = strtr(self::$code,array(
        '{time1}'               =>    $time1?? '',
        '{time2}'               =>    $time2?? '',
        '{time3}'               =>    $time3?? '',
        '{time4}'               =>    $time4?? '',
        '{zaprospoisk}'         =>    $zaprospoisk?? '',
        '{zaprospoisk2}'        =>    $zaprospoisk2?? '',
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
        '{procent1}'            =>    $procent1?? ''
      ));



        if(!file_put_contents($dir, $code, LOCK_EX)){

          Yii::$app->session->addFlash('warning', "Не создали файл настроек");
          Logs::AddSettingLogs("ID пользователя: $ID, Редактирование шаблона: тип настройки = $types
          (не создали файл настроек) Код операции #Edit3-0005", $ID);

        }
        else if($namefile!=""){
          extract( self::loader( get_defined_vars() ) );
          Yii::$app->session->addFlash('success', "Настройка ID: $id успешно сохранена");
          Logs::AddSettingLogs("ID пользователя: $ID, Редактирование шаблона: тип настройки = $types
          (успешно изменен файл шаблона id: $id) Код операции #Edit3-0006", $ID);
        }
        else {
          Yii::$app->session->addFlash('error', "Возникла ошибка создания настройки через API, сообщите администратору!");
          Logs::AddSettingLogs("ID пользователя: $ID, Редактирование шаблона: тип настройки = $types
          (ошибка создания настройки через API) Код операции #Edit3-0004", $ID);
        }
      }
    }

    return get_defined_vars();
  }
}
