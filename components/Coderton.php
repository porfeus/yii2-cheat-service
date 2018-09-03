<?php

namespace app\components;

use Yii;
use app\models\Conf;
use app\models\Logs;

set_time_limit(100);

class Coderton
{

    /**
   * Проверка текста на предмет совпадения открывающих и закрывающих тэгов
   *
   * @param string $txt    Текст для разбора
   * @param array  $tags   Массив, описывающий состав и начертание
   *                       открывающих и закрывающих тэгов
   *                           array(
   *                               'tag name' =>
   *                                   array(
   *                                       'open'  => '<tag>'
   *                                     , 'close' => '</tag>'
   *                                   )
   *                             ...
   *                           )
   *
   * @return array         Массив, рписывающий результат разбора
   *                           array(
   *                               'result' => true | false
   *                               'reason' => description of problem
   *                           )
   *
   *                           Параметр 'result':
   *                               0     - количество и порядок
   *                                       закрывающих тэгов
   *                                       соответствует количеству
   *                                       и порядку открывающих тэгов
   *                               1     - ошибка несоответствия тэгов
   *                               2     - ошибка во входных параметрах
   *
   *                           Параметр 'reason':
   *                               текстовое описание ошибки
   *
  **/
  public static function checkTags( $txt, $tags ) {
    //удаляем комментарии с генерируемого кода
    $txt = self::commentsClear($txt);

    // Возможные результаты
    $result_true = array(
        'result' => true
      , 'reason' => '<font color=green>Тэги корректны</font><br/>'
    );
    $result_false_match = array(
        'result' => false
      , 'reason' => '<font color=red>Ошибка: проверьте открывающие и закрывающие теги</font><br/>'
    );
    $result_false_pars = array(
        'result' => false
      , 'reason' => '<font color=red>Ошибка во входных параметрах уникального кода</font><br/>'
    );

    // Проверка входных параметров
    $regexp = '/\.*';
    $re     = array();
    $search = array();
    foreach( $tags as $t => $v ) {
        // Обязательные элементы массива параметров
        if( empty( $v['open'] ) || empty( $v['close'] ) ) {
            return $result_false_pars;
        }

        // Попутно собираем термы в один регэксп
        $re[] = addcslashes($v['open'].'|'.$v['close'], '/');

        // Попутно формируем массив разбора -
        // ключ - открывающий тэг, значение - закрыващий
        if( !empty($search[$v['open']]) ) {
			      // Не допускаются совпадения открывающих тэгов
            return $result_false_pars;
		    }
		    $search[$v['open']] = $v['close'];
	  }
    $regexp .= join( '|', $re ).'/';


    // Разбор входного текста


    // Вырожденный случай - just for test
    if( strlen($txt) == 0 ) {
        return $result_true;
    }

    // Формируем массив разбора
    preg_match_all( $regexp, $txt, $out );
    $out = $out[0];

    // Разбираем массив с помощью стека
    $stack = array();
    foreach( $out as $tag ) {
        $open = false;
        foreach( $tags as $k => $v ) {
            if( preg_match( "/".addcslashes($v['open'], '/')."/", $tag ) ) {
                // Открывающий тэг - пишем в стек
                // соответствующий закрывающий тэг
                $stack[] = $search[ $v['open'] ];
                $open = true;
                break;
            }
		    }
		    if( !$open ) {
			      // Закрывающий тэг - извлекаем
            // и проверяем последний элемент стека
            if( array_pop( $stack ) != $tag ) {
                return $result_false_match;
            }
		    }

    } // разбор результатов поиска тэгов

    // Если стек после разбора непуст -
    // налицо несоответствие тэгов
    if( count( $stack ) > 0 ) {
        return $result_false_match;
    }

    // Все условия выполнены, тэги соответствуют
    return $result_true;
  }

  //Добавляем в список тегов теги с параметрами
  private static function addPlaceholders( $definedVars ){
    extract( $definedVars );

    $placeholders_copy = $placeholders;
    $vals_copy = $vals;

    for($i=0; $i<count($placeholders_copy); $i++){

      //подготавливаем шаблон поиска {команда} = {команда=1,2}
      $pattern = str_replace(['{','}'], ['\{', '=[^\}]+\}'], $placeholders_copy[$i]);
      $matches = false;

      //находим в коде команды с параметрами
      preg_match_all('@'.$pattern.'@', $current_txt, $matches);
      if( count($matches[0]) ){
        foreach( $matches[0] as $match ){

          //если команда парная
          if( isset($placeholders_copy[$i+1]) && strstr($placeholders_copy[$i+1], '{/') ){ //close tag
            //добавляем закрывающие теги
            array_unshift($placeholders, $placeholders_copy[$i+1]);
            array_unshift($vals, $vals_copy[$i+1]);

            //Помечаем парные теги
            //Смещаем очереди на количество добавленных тегов
            $a = array_map(function($value){
              return $value + 2;
            }, $a);
            $b = array_map(function($value){
              return $value + 2;
            }, $b);
            array_unshift($a, 0);
          }else{
            //Помечаем одиночные теги
            //Смещаем очереди на количество добавленных тегов
            $a = array_map(function($value){
              return $value + 1;
            }, $a);
            $b = array_map(function($value){
              return $value + 1;
            }, $b);
            array_unshift($b, 0);
          }
          //добавляем теги с параметрами
          array_unshift($placeholders, $match);
          array_unshift($vals, $vals_copy[$i]);
        }
      }
    }

    //очищаем память от временных переменных
    unset($placeholders_copy);
    unset($vals_copy);

    return get_defined_vars();
  }

  //заменяет теги на команды, включая теги с параметрами
  private static function placeholder_replace($placeholders, $vals, $codert){
    //проходим по всем тегам
    for($i=0; $i<count($placeholders); $i++){
      $matches = false;
      //ищем теги с параметрами
      preg_match('@=([^\}]+)\}@', $placeholders[$i], $matches);
      if( count($matches) ){
        //получаем список параметров
        $manualValues = explode(',', $matches[1]);
        foreach($manualValues as $a=>$manualValue){
          //Если значение не задано или не число, то пропускаем его
          if( empty($manualValue) || !is_numeric($manualValue) ) continue;
          $n = $a + 1;
          //заменяем параметр на ручное значение в коде "до"
          $vals[$i] = preg_replace("@\{v:[^:]+:".$n."\}@", $manualValue, $vals[$i]);
          if( isset($placeholders[$i+1]) && strstr($placeholders[$i+1], '{/') ){ //close tag
            //ищем параметр в коде "после"
            $vals[$i+1] = preg_replace("@\{v:[^:]+:".$n."\}@", $manualValue, $vals[$i+1]);
          }
        }
      }
      //заменяем теги на параметры
      $codert = str_replace($placeholders[$i], $vals[$i], $codert);
    }

    //устанавливаем тегам параметры по умолчанию, если не заданы вручную
    \app\models\Coderton::setDefaultValues($codert);

    return trim($codert);
  }

  //заменяем комментарии на теги
  public static function commentsEncode($code){
    return str_replace([
      '/*',
      '*/',
      '<!--',
      '-->',
    ],
    [
      '{commentJ}',
      '{/commentJ}',
      '{commentH}',
      '{/commentH}',
    ], $code);
  }

  //возвращаем комментарии, заменяя на соответствующие теги
  public static function commentsDecode($code){
    return str_replace([
      '{commentJ}',
      '{/commentJ}',
      '{commentH}',
      '{/commentH}',
    ],
    [
      '/*',
      '*/',
      '<!--',
      '-->',
    ], $code);
  }

  //удаляем комментарии с генерируемого кода
  public static function commentsClear($code){
    $code = preg_replace('@{comment.}.*{/comment.}@siU', '', $code);

    return $code;
  }

  public static function run( $definedVars ){
    extract( $definedVars );

    $codertonHtml = '';

    $IP = $_SERVER['REMOTE_ADDR'];

    //Массив, описывающий состав и начертание открывающих и закрывающих тэгов
    $tags = [];

    //массив ЧТО будем заменять
    $placeholders = [];

    //перечисляем теги
    $vals = [];

    //Проверка на корректность парные и не парные
    //парные теги {}текст{/}
    $a = [];

    //не парные - одиночные теги {}
    $b = [];

    //Заполняем переменные для парсинга
    \app\models\Coderton::setParserData($tags, $placeholders, $vals, $a, $b);


    // мусор элементы, которые обязательно удалим в коде
    $search = array('<get(log)>', '<get(logtxt)>', ';;;', '<get(id)>','XMLHttpRequest()');

    // очистка от мусора
    $codert = str_replace($search, '', $codert);
    $comment_code = $codert;

    // валидация тегов
    $result_true = self::checkTags( $codert, $tags ) and !strpos($codert,"<") and !strpos($codert,">");

    if($result_true['result'] == "1"){

      //удаляем комментарии с генерируемого кода
      $codert = self::commentsClear($codert);
      $current_txt = $codert;
      //Добавляем в список тегов теги с параметрами
      extract( self::addPlaceholders( get_defined_vars() ) );

      do
      {
        //временно отключаем очистку в проверочном коде символов новой строки и табуляции
      	false && $current_txt = trim($current_txt,"\n\t");

        //очищаем проверочный код от пробелов
      	$current_txt = trim($current_txt);

        //статус по умолчанию - ошибка парсинга
      	$er = true;

        //если проверочный код начинается с известного тега - помечаем, что ошибки нет
      	foreach ($placeholders as $place) if (strpos($current_txt,$place)===0) $er = false;

        //если ошибки нет
      	if (!$er)
      	{
          //проходимся по одиночным тегам
      		for ($i=0;$i<count($b);$i++)
            //если следующий тег является одиночным
      			if (strpos($current_txt,$placeholders[$b[$i]])===0)
              //вырезаем с проверочного кода этот тег
      				$current_txt = trim(substr($current_txt,strlen($placeholders[$b[$i]])));

      		$n = -1;
      		$mid = "";

          //проходимся по парным тегам
      		for ($i=0;$i<count($a);$i++)
      		{
            //ищем позицию открывающего тега
      			$n1 = strpos($current_txt,$placeholders[$a[$i]]);
            //ищем позицию закрывающего тега
      			$n2 = strpos($current_txt,$placeholders[$a[$i]+1]);
            //определяем длину содержимого тега
      			$n3 = $n2 - strlen($placeholders[$a[$i]]);

            //если проверочный код начинается с известного парного тега и указано содержимое тега
      			if ($n1 === 0 and $n2 > 0 and $n3 > 0)
      			{
              //получаем содержимое тега
      				$mid = substr($current_txt, strlen($placeholders[$a[$i]]), $n3);

              //если в содержимом тега есть другие теги - устанавливаем статус "ошибка парсинга"
      				foreach ($placeholders as $place) if (strpos($mid,$place)>-1) $er = true;

              //если нет ошибки - вырезаем с проверочного кода парный тег
      				if (!$er) $current_txt = trim(substr($current_txt,$n2 + strlen($placeholders[$a[$i]+1])));
      			}
      		}
      	}
      }

      //выполняем пока нет ошибки парсинга и пока есть теги в проверочном коде
      while (!$er and strlen($current_txt)>0);

      //если есть ошибка парсинга или остались теги в проверочном коде
      if ($er or trim($current_txt)!="") {
        //код не сохранен
      	$codert_end  = self::placeholder_replace($placeholders, $vals, $codert);
        Yii::$app->session->addFlash('warning', "В коде много действий, которые нельзя проверить.<br/>
      	Если вы не уверены в корректности вашего кода, лучше прибегнуть к стандартным командам или помощи тех.поддержки!");
      	Logs::AddSettingLogs("ошибочно сгенерирован уникальный код - неизвестная ошибка, настройка #$id, IP: $IP, Код операции: Coderton-001", $ID);
      }
      else {
        //если превышена длина кода
      	if (strlen($codert)>200000){
      		$codert_end = '/* Уникальный код больше 200 000 символов, поставили заглущку!*/';
          Yii::$app->session->addFlash('error', "Максимальная длина всего кода не более 200 000 символов!");
      		Logs::AddSettingLogs("ошибочно сгенерирован уникальный код - более 200 000 символов, настройка #$id, IP: $IP, Код операции: Coderton-002", $ID);
        }else {
      		//заменяем  все вхождения
      		$codert_end = self::placeholder_replace($placeholders, $vals, $codert);
          Yii::$app->session->addFlash('info', "Уникальный код сгенерирован успешно!");
      		Logs::AddSettingLogs("успешно сгенерирован уникальный код, настройка #$id, IP: $IP, Код операции: Coderton-003", $ID);
      	}
      }


    }
    //проверка на теги
    else{
       $codert_end = '/* не получилось добавить код, какая-то ошибка*/';
       //$codertonHtml.=  $result_true['reason'];
       Yii::$app->session->addFlash('error', "Пожалуйста проверьте закрываюшие и открывающие теги. Код не сохранен!");
       Logs::AddSettingLogs("ошибочно сгенерирован уникальный код - неизвестная ошибка, возможно неправильные теги, настройка #$id, IP: $IP, Код операции: Coderton-004", $ID);
    }

    return get_defined_vars();
  }
}
