<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\components\ApiFunctions;
use app\components\Curl;
use app\models\Logs;
use app\models\Jetid;
use app\models\Conf;
use app\models\AjaxForm;

class AjaxController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            \app\components\SetMetaBehavior::className(),
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['xml'],
                        'roles' => ['?'],
                    ],
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Выполняем код перед запуском действия
     */
    public function beforeAction($action) {

        //Устанавливаем title, keywords и description для функциональных страниц
        $this->setTitleAndMetatags();

        return parent::beforeAction($action);
    }

    public function actionIndex() //ajax.php
    {
    	$ID = Yii::$app->user->id;

    	switch (Yii::$app->request->post('do')) {

    		// получаем статистику IP
    	case "getips":
    		AjaxForm::getips();
    		break;

    		// очистка статистики
    	case "clear_stat":
    		AjaxForm::clearStat();
    		break;

    		// запрос изменения настроек
    	case "set_stat":
    		return AjaxForm::setStat($this);
    		break;

        // операции с реалами
      case "cred_ops":
      	$ids = trim(Yii::$app->request->post('ids'), ",");
      	if ($ids) {
      		$ids = explode(",", $ids);
      	}

      	$ids = array_map("intval", $ids);

        $myids = Jetid::find()
          ->where(['uz' => $ID])
          ->andWhere(['IN', 'id', $ids])
          ->asArray()
          ->all();

      	return $this->renderPartial('cred_ops', ['myids' => $myids]);

      	break;

      	// операция с реалами
      case "cred_op":
        AjaxForm::credOp();
      	break;
    	}
    }


    /**
    * Редактирование настройки
    */
    public function actionEdit(){
      header('Content-type: application/json');

      $avt = 'OK';
      $json = ApiFunctions::save_start($avt, Yii::$app->user->id);
      echo json_encode($json);
      exit;
    }

    /**
    * Добавление и снятие реалов
    */
    public function actionApi(){
      header('Content-Type: application/json');

      $avt = 'OK';
      $json = ApiFunctions::move($avt, Yii::$app->user->id);
      echo json_encode($json);
      exit;
    }

    /**
    * Обновление статистики
    */
    public function actionRefreshStat(){

      $ID = Yii::$app->user->id;
      $userModel = Yii::$app->user->identity;

      if (!empty($ID)) {
      	$type = "refresh_stat";
      	$user_id = $ID;
      	if (ApiFunctions::checkTime("refresh_stat")) {
      		ApiFunctions::setLastRequest($user_id, $type);

      		// тут получается 5 попыток
      		for ($iu = 1; $iu <= 5; $iu++) {

      			// ---
            // Список ID-ов настроек текущего пользователя
            $jet_ids = array_keys($userModel->settings);

            // Если у пользователя есть настройки
            if (count($jet_ids)) {
              $mySettings = array_chunk($jet_ids, 50); //разбитый массыв по 50 значений
              $cout = 0;
              foreach($mySettings as $pp) {

                // посылаем запрос на API
                $siteStat = ApiFunctions::SiteStat($pp);
                $cout++;
                foreach($siteStat as $id => $site) {
                  if (isset($site['notexists'])) continue;
                  $update_stata[] = $id;

                  $settingsModel = $userModel->settings[$id];
                  $settingsModel->traf = $site['cr'];
                  $settingsModel->pokaz = $site['cr'];
                  $settingsModel->ch = $site['pkh'];
                  $settingsModel->d = $site['pkd'];
                  $settingsModel->oll = $site['pk'];
                  $settingsModel->last = $site['lp'];
                  $settingsModel->save(false);
                }
              }
            }

      			if (empty($update_stata)) {
      				$json["success"] = false;
      			}

      			if (isset($update_stata)) {
      				$json["success"] = true;
      			}
      			// ---

      			// проверяем условие на true переменной
      			if ($json["success"]) {
              Logs::AddSettingLogs("Пользователь обновил статистику настроек
                  (Кол-во запросов: $cout) обновлены настройки, попыток ($iu): " . implode(", ", $update_stata) . "", $ID);

      				// получили положительный ответ - выходим
      				break;
      			}

      			// ставим интервал в 1 секунду между запросами
      			sleep(1);
      		}

      		if (!$json["success"]) {
            Logs::AddSettingLogs("Пользователь запросил обновление настроек,
                ошибка - статистика не обновилась, попыток($iu)", $ID);
      		}

      		$json["date"] = date("d.m.Y H:i");
      	}
      	else {
      		$json["error"] = ApiFunctions::getTimeLimitError($user_id, "$type");
      		$json["success"] = false;
      	}
      }
      else {
      	$json["error"] = "Вы не авторизованы";
      	$json["success"] = false;
      }

      header('Content-Type: application/json');
      echo json_encode($json);
      exit;
    }


    /**
    * Показывает график настройки
    */
    public function actionStatSetting($id, $type){

      $ID = Yii::$app->user->id;
      $userModel = Yii::$app->user->identity;

      //Проверка типа графика и наличия настройки у юзера
      if( !in_array($type, [1, 2]) || !isset($userModel->settings[$id]) ) {
        die('Нет данных');
      }

      //Получаем данные графика по api
      $otvet = \app\components\ApiFunctions::SiteApiRequest("showstat", array(
        "idst" => intval($id),
        "interval" => $type,
      ));

      $data = [];
      $otvet = $otvet[$id];

      //Если есть данные по графику, подготавливаем их
      if( !isset($otvet['notexists']) ){
        foreach($otvet as $val){
          //Поправляем отставание времени на 3 часа и переводим в миллисекунды для жс
          $val['time'] = ($val['time'] + 3600*3) * 1000;
          $val['amount'] = intval($val['amount']);
          $data[] = array_values($val);
        }
      }else{
        die('Нет данных');
      }

      return $this->renderPartial('stat_setting', [
        'data' => $data,
        'type' => $type,
      ]);
    }


    /**
    * Скрипт берет данные с Jetswap
    * Об актуальных городах и странах
    * Кол-во городов постоянно меняется
    */
    public function actionXml()
    {
        //если id настройки не пустой

        $get = Yii::$app->request->get();

        //берем параметры
        $jetid = $get['id'] ?? '000000';
        $a = $get['a'] ?? 0;
        $s = $get['s'] ?? 0;

        if ((!is_numeric($jetid)) OR (strlen($jetid) > 10) OR (strlen($jetid) < 6)) {
          exit('Ошибка запроса');
        }

        if ((!is_numeric($s)) OR ($s > 1) OR (!is_numeric($a)) OR ($a > 1)){
          exit('Ошибка запроса');
        }

        $url = "http://go.jetswap.com/xml.php" . '?a=' . $a . '&s=' . $s;

        //Берем настройки гео через Curl
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0; MyIE2; .NET CLR 1.1.4322)");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_REFERER, "http://go.jetswap.com/account?mode=url&cmd=edit&idst=$jetid&fill=$jetid");
        $result = curl_exec($ch);
        $result = iconv("Windows-1251", "UTF-8", $result);
        curl_close($ch);

        if( Yii::$app->request->isAjax ){
          return $this->renderPartial('xml', ['result' => $result]);
        }else{
          $this->layout = 'blank';
          return $this->render('xml', ['result' => $result]);
        }
    }
}
