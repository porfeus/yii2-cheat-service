<?php
// SMSC.RU API (smsc.ru) версия 3.0 (29.07.2014)

namespace app\components;

use Yii;
use app\models\Conf;


class Sms
{

  /**
  	* Функция отправки SMS

  	* обязательные параметры:

  	* $phones - список телефонов через запятую или точку с запятой
  	* $message - отправляемое сообщение

  	* необязательные параметры:

  	* $translit - переводить или нет в транслит (1,2 или 0)
  	* $time - необходимое время доставки в виде строки (DDMMYYhhmm, h1-h2, 0ts, +m)
  	* $id - идентификатор сообщения. Представляет собой 32-битное число в диапазоне от 1 до 2147483647.
  	* $format - формат сообщения (0 - обычное sms, 1 - flash-sms, 2 - wap-push, 3 - hlr, 4 - bin, 5 - bin-hex, 6 - ping-sms, 7 - mms)
  	* $sender - имя отправителя (Sender ID). Для отключения Sender ID по умолчанию необходимо в качестве имени
  	* передать пустую строку или точку.
  	* $query - строка дополнительных параметров, добавляемая в URL-запрос ("valid=01:00&maxsms=3&tz=2")
  	* $files - массив путей к файлам для отправки mms-сообщений

  	* возвращает массив (<id>, <количество sms>, <стоимость>, <баланс>) в случае успешной отправки
  	* либо массив (<id>, -<код ошибки>) в случае ошибки
   */
  public static function send_sms($phones, $message, $translit = 0, $time = 0, $id = 0, $format = 0, $sender = false, $query = "", $files = array())
  {
  	static $formats = array(1 => "flash=1", "push=1", "hlr=1", "bin=1", "bin=2", "ping=1", "mms=1");

  	$m = self::_smsc_send_cmd("send", "cost=3&phones=".urlencode($phones)."&mes=".urlencode($message).
  					"&translit=$translit&id=$id".($format > 0 ? "&".$formats[$format] : "").
  					($sender === false ? "" : "&sender=".urlencode($sender)).
  					($time ? "&time=".urlencode($time) : "").($query ? "&$query" : ""), $files);

  	// (id, cnt, cost, balance) или (id, -error)

  	if (Conf::getParams('smsc_debug')) {
  		if ($m[1] > 0)
  			echo "Сообщение отправлено успешно. ID: $m[0], всего SMS: $m[1], стоимость: $m[2], баланс: $m[3].\n";
  		else
  			echo "Ошибка №", -$m[1], $m[0] ? ", ID: ".$m[0] : "", "\n";
  	}

  	return $m;
  }

  /**
   * SMTP версия функции отправки SMS
   */
  public static function send_sms_mail($phones, $message, $translit = 0, $time = 0, $id = 0, $format = 0, $sender = "")
  {
  	return mail("send@send.smsc.ru", "", Conf::getParams('smsc_login').":".Conf::getParams('smsc_password').":$id:$time:$translit,$format,$sender:$phones:$message", "From: ".Conf::getParams('smsc_from')."\nContent-Type: text/plain; charset=".Conf::getParams('smsc_charset')."\n");
  }

  /**
  	* Функция получения стоимости SMS
  	*
  	* обязательные параметры:
  	*
  	* $phones - список телефонов через запятую или точку с запятой
  	* $message - отправляемое сообщение
  	*
  	* необязательные параметры:
  	*
  	* $translit - переводить или нет в транслит (1,2 или 0)
  	* $format - формат сообщения (0 - обычное sms, 1 - flash-sms, 2 - wap-push, 3 - hlr, 4 - bin, 5 - bin-hex, 6 - ping-sms, 7 - mms)
  	* $sender - имя отправителя (Sender ID)
  	* $query - строка дополнительных параметров, добавляемая в URL-запрос ("list=79999999999:Ваш пароль: 123\n78888888888:Ваш пароль: 456")
  	*
  	* возвращает массив (<стоимость>, <количество sms>) либо массив (0, -<код ошибки>) в случае ошибки
   */
  public static function get_sms_cost($phones, $message, $translit = 0, $format = 0, $sender = false, $query = "")
  {
  	static $formats = array(1 => "flash=1", "push=1", "hlr=1", "bin=1", "bin=2", "ping=1", "mms=1");

  	$m = self::_smsc_send_cmd("send", "cost=1&phones=".urlencode($phones)."&mes=".urlencode($message).
  					($sender === false ? "" : "&sender=".urlencode($sender)).
  					"&translit=$translit".($format > 0 ? "&".$formats[$format] : "").($query ? "&$query" : ""));

  	// (cost, cnt) или (0, -error)

  	if (Conf::getParams('smsc_debug')) {
  		if ($m[1] > 0)
  			echo "Стоимость рассылки: $m[0]. Всего SMS: $m[1]\n";
  		else
  			echo "Ошибка №", -$m[1], "\n";
  	}

  	return $m;
  }

  /**
  	* Функция проверки статуса отправленного SMS или HLR-запроса
  	*
  	* $id - ID cообщения
  	* $phone - номер телефона
  	* $all - вернуть все данные отправленного SMS, включая текст сообщения (0 или 1)
  	*
  	* возвращает массив:
  	*
  	* для SMS-сообщения:
  	* (<статус>, <время изменения>, <код ошибки доставки>)
  	*
  	* для HLR-запроса:
  	* (<статус>, <время изменения>, <код ошибки sms>, <код IMSI SIM-карты>, <номер сервис-центра>, <код страны регистрации>, <код оператора>,
  	* <название страны регистрации>, <название оператора>, <название роуминговой страны>, <название роумингового оператора>)
  	*
  	* При $all = 1 дополнительно возвращаются элементы в конце массива:
  	* (<время отправки>, <номер телефона>, <стоимость>, <sender id>, <название статуса>, <текст сообщения>)
  	*
  	* либо массив (0, -<код ошибки>) в случае ошибки
   */
  public static function get_status($id, $phone, $all = 0)
  {
  	$m = self::_smsc_send_cmd("status", "phone=".urlencode($phone)."&id=".$id."&all=".(int)$all);

  	// (status, time, err, ...) или (0, -error)

  	if (Conf::getParams('smsc_debug')) {
  		if ($m[1] != "" && $m[1] >= 0)
  			echo "Статус SMS = $m[0]", $m[1] ? ", время изменения статуса - ".date("d.m.Y H:i:s", $m[1]) : "", "\n";
  		else
  			echo "Ошибка №", -$m[1], "\n";
  	}

  	if ($all && count($m) > 9 && (!isset($m[14]) || $m[14] != "HLR")) // ',' в сообщении
  		$m = explode(",", implode(",", $m), 9);

  	return $m;
  }

  /**
  	* Функция получения баланса
  	* без параметров
  	* возвращает баланс в виде строки или false в случае ошибки
   */
  public static function get_balance()
  {
  	$m = self::_smsc_send_cmd("balance"); // (balance) или (0, -error)

  	if (Conf::getParams('smsc_debug')) {
  		if (!isset($m[1]))
  			echo "Сумма на счете: ", $m[0], "\n";
  		else
  			echo "Ошибка №", -$m[1], "\n";
  	}

  	return isset($m[1]) ? false : $m[0];
  }


  /**
  	* ВНУТРЕННИЕ ФУНКЦИИ
  	* Функция вызова запроса. Формирует URL и делает 3 попытки чтения
   */
  public static function _smsc_send_cmd($cmd, $arg = "", $files = array())
  {
  	$url = (Conf::getParams('smsc_https') ? "https" : "http")."://smsc.ru/sys/$cmd.php?login=".urlencode(Conf::getParams('smsc_login'))."&psw=".urlencode(Conf::getParams('smsc_password'))."&fmt=1&charset=".Conf::getParams('smsc_charset')."&".$arg;

  	$i = 0;
  	do {
  		if ($i) {
  			sleep(2);

  			if ($i == 2)
  				$url = str_replace('://smsc.ru/', '://www2.smsc.ru/', $url);
  		}

  		$ret = self::_smsc_read_url($url, $files);
  	}
  	while ($ret == "" && ++$i < 3);

  	if ($ret == "") {
  		if (Conf::getParams('smsc_debug'))
  			echo "Ошибка чтения адреса: $url\n";

  		$ret = ","; // фиктивный ответ
  	}

  	return explode(",", $ret);
  }

  /**
  	* Функция чтения URL. Для работы должно быть доступно:
  	* curl или fsockopen (только http) или включена опция allow_url_fopen для file_get_contents
   */
  public static function _smsc_read_url($url, $files)
  {
  	$ret = "";
  	$post = Conf::getParams('smsc_post') || strlen($url) > 2000;

  	if (function_exists("curl_init"))
  	{
  		static $c = 0; // keepalive

  		if (!$c) {
  			$c = curl_init();
  			curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
  			curl_setopt($c, CURLOPT_CONNECTTIMEOUT, 10);
  			curl_setopt($c, CURLOPT_TIMEOUT, 60);
  			curl_setopt($c, CURLOPT_SSL_VERIFYPEER, 0);
  		}

  		if ($post || $files)
  		{
  			list($url, $post) = explode("?", $url, 2);
  			curl_setopt($c, CURLOPT_POST, true);

  			if ($files) {
  				parse_str($post, $m);

  				foreach ($m as $k => $v)
  					$m[$k] = isset($v[0]) && $v[0] == "@" ? sprintf("\0%s", $v) : $v;

  				$post = $m;
  				foreach ($files as $i => $path)
  					if (file_exists($path))
  						$post["file".$i] = function_exists("curl_file_create") ? curl_file_create($path) : "@".$path;
  			}

  			curl_setopt($c, CURLOPT_POSTFIELDS, $post);
  		}

  		curl_setopt($c, CURLOPT_URL, $url);

  		$ret = curl_exec($c);
  	}
  	elseif ($files) {
  		if (Conf::getParams('smsc_debug'))
  			echo "Не установлен модуль curl для передачи файлов\n";
  	}
  	else {
  		if (!Conf::getParams('smsc_https') && function_exists("fsockopen"))
  		{
  			$m = parse_url($url);

  			if (!$fp = fsockopen($m["host"], 80, $errno, $errstr, 10))
  				$fp = fsockopen("212.24.33.196", 80, $errno, $errstr, 10);

  			if ($fp) {
  				fwrite($fp, ($post ? "POST $m[path]" : "GET $m[path]?$m[query]")." HTTP/1.1\r\nHost: smsc.ru\r\nUser-Agent: PHP".($post ? "\r\nContent-Type: application/x-www-form-urlencoded\r\nContent-Length: ".strlen($m['query']) : "")."\r\nConnection: Close\r\n\r\n".($post ? $m['query'] : ""));

  				while (!feof($fp))
  					$ret .= fgets($fp, 1024);
  				list(, $ret) = explode("\r\n\r\n", $ret, 2);

  				fclose($fp);
  			}
  		}
  		else
  			$ret = file_get_contents($url);
  	}

  	return $ret;
  }

  /**
   * Подготавливает формат телефона
   */
  public static function process_phone($phone)
  {
      return preg_replace("#^\+7 \(([0-9]{3})\) ([0-9]{3})\-([0-9]{2})\-([0-9]{2})$#si", "8$1$2$3$4", $phone);
  }
}


/*
Examples:
  include "smsc_api.php";
  list($sms_id, $sms_cnt, $cost, $balance) = send_sms("79999999999", "Ваш пароль: 123", 1);
  list($sms_id, $sms_cnt, $cost, $balance) = send_sms("79999999999", "http://smsc.ru\nSMSC.RU", 0, 0, 0, 0, false, "maxsms=3");
  list($sms_id, $sms_cnt, $cost, $balance) = send_sms("79999999999", "0605040B8423F0DC0601AE02056A0045C60C036D79736974652E72750001036D7973697465000101", 0, 0, 0, 5, false);
  list($sms_id, $sms_cnt, $cost, $balance) = send_sms("79999999999", "", 0, 0, 0, 3, false);
  list($cost, $sms_cnt) = get_sms_cost("79999999999", "Вы успешно зарегистрированы!");
  send_sms_mail("79999999999", "Ваш пароль: 123", 0, "0101121000");
  list($status, $time) = get_status($sms_id, "79999999999");
  $balance = get_balance();
*/
