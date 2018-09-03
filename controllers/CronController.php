<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\Conf;
use app\models\Logs;
use app\models\Users;
use app\models\Jetid;
use app\models\Schedule;
use app\models\ApiRequests;
use app\components\ApiFunctions;
use app\components\Sms;

class CronController extends Controller

{
	/**
	 * Displays Курс доллара Dollar.
	 *
	 * @return string
	 */
	public function actionDollar()
	{

		// Определяем сегодняшнюю дату в формате, который используется на сайте cbr.ru

		$date = date("d/m/Y");

		// Определим код нашей валюты на сайте cbr.ru
		// Америконский доллар

		$code = 'R01235';

		// Cчитываем страницу с курсами валют в переменную

		$file = file_get_contents("http://www.cbr.ru/scripts/XML_daily.asp?date_req=" . $date);

		// Разбираем страницу с помощью регулярок:

		preg_match("/\<Valute ID=\"" . $code . "\".*?\>(.*?)\<\/Valute\>/is", $file, $m);
		preg_match("/<Value>(.*?)<\/Value>/is", $m[1], $r);

		// В переменной $usd находится курс доллара в рублях

		$usd = str_replace(",", ".", $r[1]);
		if ($usd > 0 AND $usd != '') {
			$usd = $usd;
			echo "Курс доллара $usd";
		}

		// иначе берем заведомо большой

		else {
			$usd = 100;
			echo "Курс доллара $usd";
		}

		// сохраняем курс долллара

		$model = Conf::find()->where(['name' => 'usd'])->one();
		$model->value = $usd;
		$model->save();

		//  Отправляем в лог

		Logs::AddCronLogs("Обновлен курс доллара США, 1$ = $usd рублей");
	}

	/**
	 * Displays Курс 1к кредитов Джетсвеп.
	 *
	 * @return string
	 */
	public function actionCreditonejet()
	{

		// делаем запрос на джетсвеп за стоимостью 1к кредитов

		$priceperone = file_get_contents("http://go.jetswap.com/stock.txt");
		if ($priceperone > 0) {
			$priceperone = $priceperone;
			echo "Стоимость 1к кредитов = $priceperone";
		}
		else {

			// иначе берем заведомо большое

			$priceperone = 5;
			echo "Стоимость 1к кредитов = $priceperone";
		}

		// сохраняем курс 1к кредитов

		$model = Conf::find()->where(['name' => 'priceperone'])->one();
		$model->value = $priceperone;
		$model->save();

		//  Отправляем в лог

		Logs::AddCronLogs("Проверка стоимости 1к кредитов в долларах на Jetswap, 1к стоит  = $priceperone $");
	}

	/**
	 * Displays Баланс кредитов Джетсвеп.
	 *
	 * @return string
	 */
	public function actionBalanscredit()
	{
		$login = Conf::getParams('jetlogin');
		$password = Conf::getParams('jetpass');
		$balanscred = Conf::getParams('balanscred');
		function request($url, $post = NULL)
		{
			$ch = curl_init();
			$user_agent = "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/65.0.3325.181 Safari/537.36";

			// если ведется проверка HTTP User-agent, то передаем один из возможных допустимых вариантов:

			curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			if (!empty($post) && count($post) > 0) {
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
			}

			curl_setopt($ch, CURLOPT_TIMEOUT, 150);
			curl_setopt($ch, CURLOPT_COOKIEJAR, "cookie.txt");
			curl_setopt($ch, CURLOPT_COOKIEFILE, "cookie.txt");
			$content = curl_exec($ch);
			curl_close($ch);
			return $content;
		}

		// авторизация №1

		$data1 = request("http://go.jetswap.com/account?mode=auth", array(
			"user" => $login,
			"pss" => $password
		));
		sleep(5);

		// авторизация №2

		$data = request("http://go.jetswap.com/account?mode=auth", array(
			"evc" => Conf::getParams('evc') ,
			"fnp" => Conf::getParams('fnp')
		));
		sleep(5);

		// забираем строку с балансом и кредитам

		$data = request("http://go.jetswap.com/account?mode=site");
		sleep(5);

		// парсим данные

		$pattern = '~<center>(.*?)</center>~is';
		preg_match($pattern, $data, $matches);

		// берем 1 строку балансом

		$out = $matches[1];

		// берем кредиты

		$pattern = '~<b>(.*?)</b>~is';
		preg_match_all($pattern, $out, $matches);

		// строка => в число

		$data_credits = (Int)$matches[1][1];

		// баланс кредитов

		echo $data_credits;

		// если баланс кредитов меньше заданного в админке шлем письмо администратору

		if (($data_credits < $balanscred) AND ($data_credits > 0)) {
			Yii::$app->mailer
        ->compose()
        ->setFrom([Yii::$app->params['sendFrom']['email'] => Yii::$app->params['sendFrom']['name']])
        ->setReplyTo(Yii::$app->params['sendFrom']['email'])
        ->setTo(Conf::getParams('adminmail'))
        ->setSubject('Уведомление о балансе кредитов!')
        ->setTextBody("На балансе аккаунта Jetswap кредитов стало менее $balanscred кредитов. Текущий баланс: $data_credits кредитов\n")
        ->send();
		}

		//  Отправляем в лог

		Logs::AddCronLogs("Проверка баланса кредитов на Jetswap, баланс = $data_credits кредитов, " . "уведомление срабатывает при менее $balanscred");
	}

	/**
	 * Displays Специальные данные с  Джетсвеп.
	 *
	 * @return string
	 */
	public function actionSpecialconfigjet()
	{

		// назначаем логин и пароль для входа

		$login = Conf::getParams('jetlogin');
		$password = Conf::getParams('jetpass');

		// функция авторизации в джетсвеп

		function request($url, $post = NULL)
		{
			$ch = curl_init();

			// если ведется проверка HTTP User-agent, то передаем один из возможных допустимых вариантов:

			$user_agent = "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/65.0.3325.181 Safari/537.36";
			curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			if (!empty($post) && count($post) > 0) {
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
			}

			curl_setopt($ch, CURLOPT_TIMEOUT, 10);
			curl_setopt($ch, CURLOPT_COOKIEJAR, "cookie.txt");
			curl_setopt($ch, CURLOPT_COOKIEFILE, "cookie.txt");
			$content = curl_exec($ch);
			curl_close($ch);
			return $content;
		}

		// входим в аккаунт

		$data1 = request("http://go.jetswap.com/account?mode=auth", array(
			"user" => $login,
			"pss" => $password
		));
		sleep(5);

		// обходим защиту JS

		$data = $data2 = request("http://go.jetswap.com/account?mode=auth", array(
			"evc" => Conf::getParams('evc') ,
			"fnp" => Conf::getParams('fnp')
		));
		sleep(5);

		// парсим случайную настройку

		$data = $data3 = request("http://go.jetswap.com/account?mode=site");

		// берем все настройки

		preg_match_all('|cmh2\(this,"edit",(.*)\)|U', $data, $out);

		// берем одну из настроек с первым ID .

		if (isset($out[1][0]) && is_numeric($out[1][0])) {
			$id = $out[1][0];
		}

		// забираем все параметры у найденного ID

		$data = request("http://go.jetswap.com/account?mode=url&cmd=edit&idst={$id}&fill={$id}");
		sleep(5);

		// берем rnac

		preg_match_all('|<input id=rnac value="(.*)" type=hidden>|U', $data, $out);
		$rnac = $out[1][0];

		// берем gnac

		preg_match_all('|<input id=gnac value="(.*)" type=hidden>|U', $data, $out);
		$gnac = $out[1][0];

		// берем rnaci

		preg_match_all('|<input id=rnaci value="(.*)" type=hidden>|U', $data, $out);
		$rnaci = $out[1][0];

		// берем gnaci

		preg_match_all('|<input id=gnaci value="(.*)" type=hidden>|U', $data, $out);
		$gnaci = $out[1][0];

		// данные успешно загружены

		echo "rnac = $rnac gnac = $gnac rnaci = $rnaci gnaci= $gnaci";

		// если rnac или gnac или rnaci или gnaci не пусто

		if ((!empty($rnac)) AND (!empty($gnac)) AND (!empty($rnaci)) AND (!empty($gnaci))) {

			// сохраняем rnac

			$model = Conf::find()->where(['name' => 'rnac'])->one();
			$model->value = $rnac;
			$model->save();

			// сохраняем gnac

			$model = Conf::find()->where(['name' => 'gnac'])->one();
			$model->value = $gnac;
			$model->save();

			// сохраняем rnaci

			$model = Conf::find()->where(['name' => 'rnaci'])->one();
			$model->value = $rnaci;
			$model->save();

			// сохраняем gnaci

			$model = Conf::find()->where(['name' => 'gnaci'])->one();
			$model->value = $gnaci;
			$model->save();

			// Отправляем в лог

			Logs::AddCronLogs("Данные c JetSwap rnac = $rnac, gnac = $gnac, rnaci = $rnaci, gnaci = $gnaci спарсили успешно, обновлены старые.");
		}

		// проверяем одну из переменных на пустоту для надежности

		if (empty($rnac)) {

			// Отправляем в лог если не удалось запросить

			Logs::AddCronLogs("Данные c JetSwap rnac = $rnac, gnac = $gnac, rnaci = $rnaci, gnaci = $gnaci не удалось спарсить, старые не изменяли");
		}
	}

	/**
	 * Displays Удаление старых юзеров в определенный день недели.
	 *
	 * @return string
	 */
	public function actionRemoveoldusers()
	{

		// берем день для удаления не активных юзеров

		$day_delete = Conf::getParams('day_delete');

		// дни недели

		function getDayRus()
		{

			// массив с названиями дней недели

			$days = array(
				'Воскресенье',
				'Понедельник',
				'Вторник',
				'Среда',
				'Четверг',
				'Пятница',
				'Суббота'
			);

			// номер дня недели с 0 до 6, 0 - воскресенье, 6 - суббота

			$num_day = (date('w'));

			// получаем название дня из массива

			$name_day = $days[$num_day];

			// вернем название дня

			return $name_day;
		}

		// текущий день

		$name_day = getDayRus();

		// если день для удаления совпад с заданным в админке

		if ($day_delete == $name_day) {

			// удаляем юзеров без оплаты (поле pay = 0)

			$model = Users::deleteAll('pay = :pay', [':pay' => '0']);

			// Отправляем в лог если не удалось запросить

			Logs::AddCronLogs("Запущен скрипт удаляющий неактивных юзеров, " . "Юзеры успешно удалены, Лог $day_delete равен $name_day");
		}

		// иначе ничего не удаляем

		else {

			// Отправляем в лог если не удалось запросить

			Logs::AddCronLogs("Запущен скрипт удаляющий неактивных юзеров, " . "Юзеры не удалены т.к. день недели не совпад с заданным в админке, " . "Лог $day_delete не равен $name_day");
		}
	}

	/**
	 * Displays Рассылка писем пользователям без оплаты.
	 *
	 * @return string
	 */
	public function actionEmailsender()
	{

		// вытаскиваем всех юзеров, кто не активирован по оплате

		$users = Users::find()->select('email')->where(['pay' => 0, ])->all();

		// кол-во юзеров

		$num_users = Users::find()->select('email')->where(['pay' => 0])->count();
		$all_emails = '';

		// разбираем массив тарифов

		foreach($users as $u) {

			// список юзеров с почтами
			// echo "$u->email <br/>";

			Yii::$app->mailer
        ->compose()
        ->setFrom([Yii::$app->params['sendFrom']['email'] => Yii::$app->params['sendFrom']['name']])
        ->setReplyTo(Yii::$app->params['sendFrom']['email'])
        ->setTo($u->email)
        ->setSubject('Вам нужен трафик? Мы не забыли про вас!')
        ->setTextBody("Вы зарегистрировались в системе https://site.ru и не активировали аккаунт\r\n" . "Для активации аккаунта пополните баланс любую сумму и Вы сможете пользоваться всеми услугами\r\n" . "У нас можно накручивать клики, трафик, различные переходы и действия!\r\n" . "Возвращайтесь к нам, мы рады будем Вас видеть!")
        ->send();

			// берем всех юзеров

			$all_emails = "$u->email, $all_emails";
		}

		// Отправляем в лог если не удалось запросить

		Logs::AddCronLogs("Разосланы уведомления на почту юзерам у которых нет активации профиля! Отправили письма $num_users юзеру. Список юзеов: $all_emails");
	}

	/**
	 * функция расписания
	 *
	 */
	public function actionSchedule(){
		$stats = array(
			'api_req' => 0,
			'user_ids' => array(),
		);

		$sched = Schedule::find()
      ->where(['!=', 'last_upd', '0000-00-00 00:00:00'])
	  	->andFilterWhere(['disabled' => 0])
      ->all();

		foreach ($sched as $model) {
			$row = $model->toArray();

			if (empty($row['site_id'])) {
				continue;
			}

			$_pkhr = unserialize($row['pkhr']);
			$_pktm = unserialize($row['pktm']);
			$_pktml = unserialize($row['pktml']);
			$result = ApiFunctions::SiteTask(array(
				$row['site_id']
			) , array(
				"pkhr" => $_pkhr,
				"pktm" => $_pktm,
				"pktml" => $_pktml
			));
			$stats['user_ids'][] = $row['site_id'];
			$stats['api_req']+= 1;

			// No more update
			$model->last_upd = '0000-00-00 00:00:00';
			$model->save(false);
		}

		if ($stats['api_req'] == 0) {
			Logs::AddCronLogs("Запущен скрипт обновляющий расписание показов. При запуске скрипта обновления расписания данные в базе не менялись, запросов на сервер не отправлено");
		}
		else {
			Logs::AddCronLogs("Запущен скрипт обновляющий расписание показов. ID настроек с обновленным расписанием: " . implode('; ', $stats['user_ids']) . "");
		}
	}

	/**
	 * функция обновления статистики
	 *
	 */
	public function actionRefreshStat(){
		$attempt_count = 1;

		while (true) {

			//Обновляем статистику (макс 3 попытки)
			if (ApiFunctions::UpdateStats($attempt_count) || $attempt_count++ >= 3) {
				break;
			}
			else {
				sleep(3);
			}
		}
	}

	/**
	 * архивные настройки
	 *
	 */
	public function actionArchive(){
		// Выбираем активированных пользователей, либо тех, на ком не стоит метка об обнулении
		$logs = array();
		$logs[] = "Проведена проверка архивных и активных настроек с JETSWAP";

		//Получаем список пользователей, которые не платили или с реалами меньше 10
		$usersRows = Users::find()
			->where(['pay' => '0'])
			->orWhere(['<', 'trafbalans', 10])
			->asArray()
			->all();

		if ( count($usersRows) ) {
			// Перебираем подходящих пользователей
			foreach ($usersRows as $row) {

				// Список ID-ов настроек текущего пользователя
				$jet_ids = Jetid::find()
          ->select('id')
          ->where(['uz'=> $row['id']])
          ->asArray()
          ->column();

				// Если у пользователя есть настройки
				if (count($jet_ids)) {
					unset($temp);
					$temp = array();

					//Получаем список неархивированных настроек
					$idsModels = Jetid::find()
						->where(['IN', 'id', $jet_ids])
						->andWhere(['view' => 1])
						->indexBy('id')
						->all();

					foreach ($idsModels as $model){
						$t = $model->toArray();
						$temp[$t['id']] = $t;
					}

					//Если есть неархивированные настройки
					if (!empty($temp) && count($temp) > 0) {
						$ids = array();
						foreach($temp as $t) $ids[$t['id']] = $t['uz'];

						//Получаем статистику настроек
						$siteStat = ApiFunctions::SiteStat(array_keys($ids));

						//Проходимся по статистике настроек
						foreach($siteStat as $id => $site) {

							//Если настройка в архиве - меняем статус настройки у нас
							if (isset($site['notexists'])) {

								$idModel = $idsModels[$id];
								$idModel->traf = '0';
								$idModel->pokaz = '0';
								$idModel->ch = 0;
								$idModel->d = 0;
								$idModel->oll = 0;
								$idModel->view = 0;
								$idModel->save(false);
								$logs[] = "Настройка $id перемещена в архив";

								if ($temp[$id]['traf'] > 0) { // Если на настройке был траик
									$userModel = Users::find()
										->where(['id' => $ids[$id] ])
										->one();
									$userModel->trafbalans += $temp[$id]['traf'];
									$userModel->save(false);
								}
							}
						}
					}
				}
			}
		}

		//Получаем список неактивных настроек не в архиве (использованные меньше 7 дней)
		$idsModels = Jetid::find()
			->where(['<', 'last', (time() - 7 * 24 * 60 * 60) ])
			->andWhere(['view' => 1])
			->indexBy('id')
			->all();

		if (!count($idsModels)) die;

		//Если есть неархивированные настройки
		foreach ($idsModels as $model) {
			$row = $model->toArray();

			//Получаем статистику настроек
			$siteStat = ApiFunctions::SiteStat($row['id']);

			//Если настройка в архиве - меняем статус настройки у нас
			if (isset($siteStat[$row['id']]['notexists'])) {
				$idModel = $idsModels[ $row['id'] ];
				$idModel->traf = '0';
				$idModel->pokaz = '0';
				$idModel->ch = 0;
				$idModel->d = 0;
				$idModel->oll = 0;
				$idModel->view = 0;
				$idModel->save(false);
				$logs[] = "Настройка $id перемещена в архив";

				if ($row['traf'] > 0) {

					// Если на настройке был трафик
					$userModel = Users::find()
						->where(['id' => $row['uz'] ])
						->one();
					$userModel->trafbalans += $row['traf'];
					$userModel->save(false);
				}
			}
		}

		Logs::AddCronLogs(implode('<br>', $logs));
	}

	/**
	 * Функция уведомления по SMS
	 *
	 */
	public function actionNotify(){

		//Загружаем всех юзеров
		$usersModels = Users::find()->all();
		$message_log1 = '';
		$message_log2 = '';
		$message_log3 = '';
		$message_log4 = '';

		foreach ($usersModels as $row) {
				//Загружаем настройки уведомления юзера
		    $notify = unserialize($row['notify']);

		    $send = FALSE;
				//Если уведомление юзеру еще не отправляли
		    if (!$row['notify_send']) {
						//Если недостаточно баланса на счете
		        if ($row['trafbalans'] < $notify['traf1'] ?? 0 AND $row['trafbalans']!='0.00') {
								//Если указана почта и разрешено уведомление по почте
		            if ($notify['onmail1'] ?? '' && $notify['mail1'] ?? '') {
		                //Шлем на почту

		                Yii::$app->mailer->compose()
		                  ->setFrom([
		                    Yii::$app->params['sendFrom']['email'] => 'Сервис Go-Ip.ru'
		                  ])
		                  ->setReplyTo(Yii::$app->params['sendFrom']['email'])
		                  ->setTo( $row['email'] )
		                  ->setSubject('Кончились реалы на балансе')
		                  ->setHtmlBody(
		                    str_replace(
													'{login}',
													$row['login'],
													Conf::getParams('mail_smsnotify_user_message')
												)
		                  )
		                  ->send();
		                $message_log1 .= "".$row['id'].':'.$row['email'].", ";

		                $send = TRUE;
		            }

								//Если указан телефон и разрешено уведомление по sms
		            if ($notify['ontel1'] ?? '' && $notify['tel1'] ?? '') {
										//Шлем на sms
		                $tel = Sms::process_phone($notify['tel1']);

		                Sms::send_sms($tel, Conf::getParams('sms_smsnotify_user_message'));
		                $send = TRUE;
										$message_log3 .= "".$row['id'].':'.$tel.", ";
		            }
		            if ($send) {
										//Ставим флаг "уведомили", если отправили любое уведомление
		                $row->notify_send = 1;
		                $row->save(false);
		            }
		        }
		    }

				//Загружаем все настройки
		    $idsModels = Jetid::find()
		      ->where(['uz' => $row['id']])
		      ->all();

		    foreach ($idsModels as $j) {
		        $send = FALSE;
						//Если уведомление не отправляли на настройку
		        if (!$j['notify_send']) {
								//Если недостаточно баланса на настройке
		            if ($j['traf'] < $notify['traf2'] ?? 0 AND $j['traf']!="0.00") {
										//Если указана почта и разрешено уведомление по почте
		                if ($notify['onmail2'] ?? '' && $notify['mail2'] ?? '') {
												//Шлем на почту

		                        Yii::$app->mailer->compose()
		                          ->setFrom([
		                            Yii::$app->params['sendFrom']['email'] => 'Сервис Go-Ip.ru'
		                          ])
		                          ->setReplyTo(Yii::$app->params['sendFrom']['email'])
		                          ->setTo( $row['email'] )
		                          ->setSubject('Кончились реалы на балансе')
		                          ->setHtmlBody(
		                            str_replace([
																	'{id}',
																	'{traf}',
																	'{login}',
																], [
																	$j['id'],
																	$j['traf'],
																	$row['login'],
																], Conf::getParams('mail_smsnotify_jetid_message')
																)
		                          )
		                          ->send();
		                        $message_log2 .= "".$j['id'].':'.$row['email'].", ";

		                        $send = TRUE;
		                }

										//Если указан телефон и разрешено уведомление по sms
		                if ($notify['ontel2'] ?? '' && $notify['tel2'] ?? '') {
												//Шлем на sms
		                    $tel = Sms::process_phone($notify['tel2']);
		                    Sms::send_sms($tel, Conf::getParams('sms_smsnotify_jetid_message'));
		                    $send = TRUE;
												$message_log4 .= "".$j['id'].':'.$tel.", ";
		                }
		                if ($send) {
												//Ставим флаг "уведомили", если отправили любое уведомление
		                    $j->notify_send = 1;
		                    $j->save(false);
		                }
		            }
		        }
		    }
		}

		if ($message_log1 == ''){
			$message_log1 = 'нет юзеров';
		}

		if ($message_log2 == ''){
			$message_log2 = 'нет юзеров';
		}

		if ($message_log3 == ''){
			$message_log3 = 'нет юзеров';
		}

		if ($message_log4 == ''){
			$message_log4 = 'нет юзеров';
		}

		//Отправляем отчет по отправленным уведомлениям
		Logs::AddCronLogs("Разосланы письма на e-mail об остановке настроек, маленьком балансе, планово раз в час<br/>
	  E-mal, Кончились реалы на общем балансе: $message_log1<br/>
	  E-mal, Кончились реалы на конкретной настройке: $message_log2<br/>
	  СМС уведомления, Общий баланс: $message_log3<br/>
	  СМС уведомления, Конкретная настройка: $message_log4<br/>");
	}

	/**
	 * Функция срабатывает раз в сутки
	 *
	 */
	public function actionDaily(){

		//Сбрасываем суточный лимит создаваемых настроек у юзера
		ApiRequests::updateAll(['last_request' => '0'], 'type = "limit_n_id" and last_request > 0');
	}
}
