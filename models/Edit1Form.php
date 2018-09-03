<?php

namespace app\models;

use Yii;
use yii\base\Model;
use app\components\ApiFunctions;
use app\components\Coderton;
use app\models\Logs;
use app\models\Jetid;
use app\models\Conf;

class Edit1Form extends Model
{
  //начало кода вставки
  public static $code = <<<SERVISCODE

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
конец настроек поисковика*/

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

var refercheks = {refercheks};


/*вводим запросы для поискового реферера*/
var words = [
{zaprospoisk}
];


/*вводим точки входа сайта*/
var url_vhoda = [
{urlvhoda}
];

/*определяем ссылочный реферер*/
var refer1 = [
{refer1}
];

if (refercheks == 0){

//берем случайные точки входа
var rnd1 = Math.floor(Math.random() * ((url_vhoda.length - 1) + 1));
var url_vhoda = url_vhoda[rnd1];

//берем случайный реферер
var rnd2 = Math.floor(Math.random() * ((refer1.length - 1) + 1));
var refer1 = refer1[rnd2];

//выбираем рандомный номер строки  списка ключевиков
var rnd3 = Math.floor(Math.random()*((words.length - 1) + 1));
var zapros = words[rnd3];

}

else {
var rnd1 = Math.floor(Math.random() * ((url_vhoda.length - 1) + 1));
var rnd2 = rnd1;
var rnd3 = rnd1;

var url_vhoda = url_vhoda[rnd1];
var refer1 = refer1[rnd2];
var zapros = words[rnd3];
}


var cmdname="script";
var cmdtime=1;
var cmdparam="<set(img={img})><set(js={js})><set(mouse={mouse})><set(csp={csp})>";
alert("prs::" + prskey + "::add::" + cmdname + "::" + cmdtime + "::" + cmdparam);


/*составляем массив поисковых машин*/
var arr = new Array({arrpoiskoviki});

/*выбираем случайный поисковик (номер из заданных, например яндекс или гугл)*/
var rand = arr[Math.floor(Math.random()*(arr.length))];



/*закладки*/
if (0 == rand){
var refer2 = 'about:blank';
      }

/*yandex.ru */
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

/*{uniks_code}({comment_code}){/uniks_code}*/

/*настройки мышки, таймайтутов, хоста
var host=<({host})>
var time-1<({time1})>
var time-2<({time2})>
prosmotr1<({prosmotr1})>
prosmotr2<({prosmotr2})>
*/

/*вариант захода на сайт*/
var variantzahoda = myRandom(1,100);

/*вариант посещений*/
var m = myRandom({prosmotr1},{prosmotr2});

/*какую маску брать = автоматом или ручная*/
var hostvar = {hostvar};

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
Other<({br-6})>; - другие браузер*/

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

/*Получаем оставшееся время показа презентации. Пока скрипт загружался, прошло несколько секунд*/
var times=prompt("prs::" + prskey + "::get::time");

/*рассчитываем максимальное время просмотра для команды*/
var min_t_vr = Math.round(((times/m)/100)*70);

/*рассчитываем минимальное время просмотра для команды*/
var max_t_vr = Math.round(((times/m)/100)*100);

/*получаем переменные для показа на вкладке "журнал", при тестинге скрипта*/
var cmdname="script";
var cmdtime=1;
var cmdparam="alert('prs::<get(key)>::setvar::refer1='+refer1);alert('prs::<get(key)>::setvar::refer2='+refer2);alert('prs::<get(key)>::setvar::zapros='+zapros);alert('prs::<get(key)>::setvar::rand='+rand);alert('prs::<get(key)>::setvar::m='+m);alert('prs::<get(key)>::setvar::url_vhoda='+url_vhoda);alert('prs::<get(key)>::setvar::times='+times);alert('prs::<get(key)>::setvar::procent1='+procent1);alert('prs::<get(key)>::setvar::variantzahoda='+variantzahoda);alert('prs::<get(key)>::setvar::rand2='+rand2);alert('prs::<get(key)>::setvar::min_t_vr='+min_t_vr);alert('prs::<get(key)>::setvar::max_t_vr='+max_t_vr);";
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

if (hostvar == 0){
/*автоматическая маска для переходов*/
var cmdname="script";
var cmdtime=1;
var cmdparam="alert('prs::<get(key)>::setvar::host='+document.location.host);";
alert("prs::" + prskey + "::add::" + cmdname + "::" + cmdtime + "::" + cmdparam);
}

if (hostvar == 1){
/*ручная маска для переходов*/
var cmdname="script";
var cmdtime=1;
var cmdparam="alert('prs::<get(key)>::setvar::host={host}');";
alert("prs::" + prskey + "::add::" + cmdname + "::" + cmdtime + "::" + cmdparam);
}


/*вставляем уникальный код после перехода*/
if (coderton == 1){
{codert_end}
}

function myRandom (from, to)  {return Math.floor((Math.random() * (to - from + 1)) + from);}
function perehod(a,b,c){
    var cmdname="event";
    var cmdtime=a;
    var cmdparam="a;link;<getvar(host)>;click;-1";
    alert("prs::" + prskey + "::add::" + cmdname + "::" + cmdtime + "::" + cmdparam);

    var cmdname="link";
    var cmdtime=myRandom(b,c);
    var cmdparam="last;0";
    alert("prs::" + prskey + "::add::" + cmdname + "::" + cmdtime + "::" + cmdparam);

   var cmdname="script";
    var cmdtime=myRandom(1,3);
    var cmdparam="window.scrollTo(<rndr(100:500)>,<rndr(50:1000)>);";
    alert("prs::" + prskey + "::add::" + cmdname + "::" + cmdtime + "::" + cmdparam);

    var cmdname="script";
    var cmdtime=myRandom(1,3);
    var cmdparam="window.scrollTo(<rndr(100:1000)>,<rndr(50:1500)>);";
    alert("prs::" + prskey + "::add::" + cmdname + "::" + cmdtime + "::" + cmdparam);
}

    for(var i=0; i<m; i++){
        perehod(2, min_t_vr, max_t_vr);
    }


alert("prs::" + prskey + "::set::cmdindex=1;cmdtime=0;");

SERVISCODE;
//конец вставки кода

  public static function loader($definedVars){
    extract( $definedVars );

    if (file_exists($dir)) {

      //загружаем файл в буфер
      $filename = file_get_contents($dir);

      //парсим точки входа => $out_url_vhoda
      preg_match('~var url_vhoda \= \[(.*)\];~isU', $filename, $url_vh);
      preg_match_all('~\'(.*)\'~', $url_vh[1]??'', $url_vhoda);
      //print_r($url_vhoda[1]);
      $out_url_vhoda = '';
      foreach($url_vhoda[1] as $val){
        $out_url_vhoda .= $val."\n";
      }
      $out_url_vhoda = trim($out_url_vhoda);

      //парсим ссылочный реферер => $out_refer1
      preg_match('~var refer1 \= \[(.*)\];~isU', $filename, $url_ref1);
      preg_match_all('~\'(.*)\'~', $url_ref1[1]??'', $url_refer1);
      $out_refer1 = '';
      foreach($url_refer1[1] as $val){
        $out_refer1 .= $val."\n";
      }
      $out_refer1 = trim($out_refer1);


      //парсим ключевые слова => $out_words
      preg_match('~var words \= \[(.*)\];~isU', $filename, $words_vh);
      preg_match_all('~\'(.*)\'~', $words_vh[1]??'', $words);
      $out_words = '';
      foreach($words[1] as $val){
        $out_words .= $val."\n";
      }
      $out_words = trim($out_words);


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

      //парсим вкл/выкл hostvar => $hostvar[1];
      preg_match('|var hostvar = (.*)\;|Uis', $filename, $hostvar);

      //парсим вкл/выкл host => $host[1];
      preg_match('|var host=<\((.*)\)>|Uis', $filename, $host);

      //парсим время один time1 => $time1[1];
      preg_match('|var time-1<\((.*)\)>|Uis', $filename, $time1);

      //парсим время два time2 => $time2[1];
      preg_match('|var time-2<\((.*)\)>|Uis', $filename, $time2);

      //парсим refercheks => $refercheks[1];
      preg_match('|var refercheks = (.*)\;|Uis', $filename, $refercheks);


      //парсим браузеры  $IE[1];
      preg_match('|IE<\((.*)\)>;|Uis', $filename, $IE);
      preg_match('|FF<\((.*)\)>;|Uis', $filename, $FF);
      preg_match('|Chrome<\((.*)\)>;|Uis', $filename, $Chrome);
      preg_match('|Opera<\((.*)\)>;|Uis', $filename, $Opera);
      preg_match('|Safari<\((.*)\)>;|Uis', $filename, $Safari);
      preg_match('|Mobile<\((.*)\)>;|Uis', $filename, $Mobile);
      preg_match('|Other<\((.*)\)>;|Uis', $filename, $Other);


      //парсим поисковики => $param0[1];
      preg_match('|@param0\((.*)\);|Uis', $filename, $param0);
      preg_match('|@param1\((.*)\);|Uis', $filename, $param1);
      preg_match('|@param2\((.*)\);|Uis', $filename, $param2);
      preg_match('|@param3\((.*)\);|Uis', $filename, $param3);
      preg_match('|@param4\((.*)\);|Uis', $filename, $param4);
      preg_match('|@param5\((.*)\);|Uis', $filename, $param5);
      preg_match('|@param6\((.*)\);|Uis', $filename, $param6);
      preg_match('|@param7\((.*)\);|Uis', $filename, $param7);
      preg_match('|@param8\((.*)\);|Uis', $filename, $param8);
      preg_match('|@param9\((.*)\);|Uis', $filename, $param9);
      preg_match('|@param10\((.*)\);|Uis', $filename, $param10);
      preg_match('|@param11\((.*)\);|Uis', $filename, $param11);
      preg_match('|@param12\((.*)\);|Uis', $filename, $param12);
      preg_match('|@param13\((.*)\);|Uis', $filename, $param13);

      //процент поискового реферера
      preg_match('|var procent1 = (.*)\;|Uis', $filename, $procent1);
      $procent2 = (100 - ($procent1[1] ?? 0));


      //вставка собственного кода
      preg_match('|{uniks_code}\((.*)\){/uniks_code}|Uis', $filename, $comment_code);
      preg_match('|var coderton = (.*)\;|Uis', $filename, $coderton);


      //кол-во просмотров
      preg_match('|prosmotr1<\((.*)\)>|Uis', $filename, $prosmotr1);
      preg_match('|prosmotr2<\((.*)\)>|Uis', $filename, $prosmotr2);
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
      Logs::AddSettingLogs("ID пользователя: $ID, Редактирование шаблона: тип настройки = $types  не смог отредактировать, настройка не найдена в управлении Код операции #Edit1-0001", $ID);
    }



    if (!file_exists($dir)) {
      Yii::$app->session->addFlash('warning', "Файл настройки $id не найден!");
      Logs::AddSettingLogs("ID пользователя: $ID, Редактирование шаблона: тип настройки = $types  файл настройки не найден Код операции #Edit1-0002", $ID);
    }

    if(isset($_POST['generate'])){

      //время захода от и до
      $time1 = (int) $_POST['time1'];
      $time2 = (int) $_POST['time2'];

      //Связывать реферер или нет
      $refercheks = (int) $_POST['refercheks'];


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


      //Кол-во просмотров сайта
      $prosmotr1 = (int) $_POST['prosmotr1'];
      $prosmotr2 = (int) $_POST['prosmotr2'];

      //host маски для перехода
      $hostvar = (int) ($_POST['hostvar'] ?? '');
      $host = $_POST['host'] ?? '';

      //использование собственного кода после перехода
      $coderton = (int) ($_POST['coderton']??'0'); //да или нет
      $codert = $_POST['codert']; //сам код

      $codert = Coderton::commentsEncode($codert);

      if ($coderton=='1'){
        extract( Coderton::run( get_defined_vars() ) );
        //берем переменную $codert_end с вставкой кода
      }


      //$date_generate = date("ID user: $ID, создана: дата d.m.y время H:i:s");

      //новый ID получать через api если разрешено юзеру!
      if($status_edit == "true"){
        $namefile = $id_user;
      }


      //вывод ошибок
      if(empty(self::$code)){
        Yii::$app->session->addFlash('error', "Шаблон кода пустой");
        Logs::AddSettingLogs("ID пользователя: $ID, Редактирование шаблона: тип настройки = $types, Шаблон кода пустой Код операции #Edit1-0003", $ID);
      }

      else{

        if( is_array($comment_code) )
          $comment_code = ''; // если coderton отключен


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
        '{hostvar}'             =>    $hostvar?? '',
        '{host}'                =>    $host?? '',
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
        '{prosmotr1}'           =>    $prosmotr1?? '',
        '{prosmotr2}'           =>    $prosmotr2?? '',
        '{refercheks}'          =>    $refercheks?? '',
        '{procent1}'            =>    $procent1?? ''
      ));



        if(!file_put_contents($dir, $code, LOCK_EX)){

          Yii::$app->session->addFlash('warning', "Не создали файл настроек");
          Logs::AddSettingLogs("ID пользователя: $ID, Редактирование шаблона: тип настройки = $types
          не создали файл настроек Код операции #Edit1-0005", $ID);

        }
        else if($namefile!=""){
          extract( self::loader( get_defined_vars() ) );
          Yii::$app->session->addFlash('success', "Настройка ID: $id успешно сохранена");
          Logs::AddSettingLogs("ID пользователя: $ID, Редактирование шаблона: тип настройки = $types
          успешно изменен файл шаблона id: $id Код операции #Edit1-0006", $ID);
        }
        else {
          Yii::$app->session->addFlash('error', "Возникла ошибка создания настройки через API, сообщите администратору!");
          Logs::AddSettingLogs("ID пользователя: $ID, Редактирование шаблона: тип настройки = $types
          ошибка создания настройки через API Код операции #Edit1-0004", $ID);
        }
      }
    }


    return get_defined_vars();
  }
}
