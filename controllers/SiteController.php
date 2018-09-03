<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\RegForm;
use app\models\LoginForm;
use app\models\NewpassForm;
use app\models\Materials;
use app\models\News;
use app\models\Pages;
use app\models\BalansForm;
use app\models\PayForm;
use app\models\Jettarif;
use app\models\Conf;
use yii\data\ActiveDataProvider;
use yii\data\Pagination;
use yii\web\Request;
use app\models\Paymentsinfo;
use app\models\Antifraud;
use app\models\Users;
use app\models\OrderForm;
use app\models\Templatesconf;
use yii\helpers\Url;
use app\models\Jetid;
use app\models\CopyForm;
use app\models\ChangeSettingsForm;
use app\models\DeleteSettingsForm;
use app\models\Schedule;
use app\models\ScheduleForm;
use app\models\ArchiveForm;
use app\models\Logs;
use app\components\Curl;

class SiteController extends Controller
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
                //контроль доступа гость/авторизован только для следующих страниц
                'only' => [
                  'logout', 'reg', 'profile', 'api', 'balans', 'news', 'order', 'add-id',
                  'robot-add', 'robot-edit'
                ],
                'rules' => [
                    [
                        'allow' => true,
                        //доступ открыт только гостям
                        'actions' => ['reg'],
                        'roles' => ['?'],
                    ],
                    [
                        'allow' => true,
                        //доступ открыт только авторизованным
                        'actions' => [
                          'logout', 'profile', 'api', 'balans', 'news', 'order', 'add-id',
                          'robot-add', 'robot-edit'
                        ],
                        'roles' => ['@'],
                    ],
                ],
                'denyCallback' => function($rule, $action) {
                  if( Yii::$app->user->isGuest ){
                    return $this->redirect(['login']);
                  }else{
                    return $this->redirect(['balans']);
                  }
                },
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Отключаем запрет на POST-запросы у некоторых страниц
     */
    public function beforeAction($action) {

        if( in_array($action->id, ['pay', 'pages']) ){
          $this->enableCsrfValidation = false;
        }

        //Устанавливаем title, keywords и description для функциональных страниц
        if( Yii::$app->controller->action->id != 'pages' ){
          $this->setTitleAndMetatags();
        }

        //Проверяем, отключено ли API
        if( Conf::getParams('api_disabled') && in_array($action->id, [
            'delete',
            'schedule',
            'robot-add',
            'robot-edit',
            'copy',
            'edit',
            'add-id',
          ]) ){
          Yii::$app->session->addFlash('error', Conf::getParams('api_disabled_message'));
          header('Location: '.Yii::$app->request->referrer);
          exit;
        }

        return parent::beforeAction($action);
    }




	 public function actionError()
    {
        $this->layout = '/404';
        $exception = Yii::$app->errorHandler->exception;
        if ($exception !== null) {
            return $this->render('error', ['exception' => $exception]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            /*'error' => [
                'class' => 'yii\web\ErrorAction',
            ],*/
        ];
    }

    /**
     * Displays captcha.
     *
     * @return string
     */
    public function actionCaptcha()
    {
      require Yii::getAlias('@securimage');
      $img = new \securimage\Securimage();
      $img->show();
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }


      /**
     * Reg action.
     *
     * @return Response|string
     */
    public function actionReg()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new RegForm();
        if (
          $model->load(Yii::$app->request->post()) &&
          $model->validate() &&
          Users::addUser($model)
        ) {
            //form is valid, user is created
            $model->login(false);

            Yii::$app->mailer->compose()
              ->setFrom([Yii::$app->params['sendFrom']['email'] => Yii::$app->params['sendFrom']['name']])
              ->setReplyTo(Yii::$app->params['sendFrom']['email'])
              ->setTo($model->email)
              ->setSubject('Данные регистрации Go-ip.ru')
              ->setTextBody(
                "Спасибо за регистрацию.\n\n".
                "Ваш логин: {$model->login}\nВаш пароль: {$model->pass}"
              )
              ->send();

            Logs::AddLoginLogs("Регистрация нового пользователя");

            return $this->redirect(['balans']);
        }

        return $this->render('reg', [
            'model' => $model,
        ]);

    }


      /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {

            $user = $model->getUser();
            $user->pv = date("Y-m-d H:i:s");
            $user->lastdate = time();
            $user->save(false);

            Logs::AddLoginLogs("Пользователь успешно авторизовался");

            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }


    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Logs::AddLoginLogs("Пользователь вышел из аккаунта");

        Yii::$app->user->logout();

        return $this->goHome();
    }


    /**
     * Profile
     *
     */
    public function actionProfile()
    {

      //подклчаем слой user
      $this->layout = 'user';

      $model = new \app\models\ProfileForm();

      $model->loadModel();

      if ($model->load(Yii::$app->request->post()) && $model->validate()) {
          $model->saveUser();

          Logs::AddLoginLogs("Пользователь изменил данные профиля");

          Yii::$app->session->setFlash('success', 'Сохранено');
          return $this->redirect(['profile']);
      }

      return $this->render('profile', [
          'model' => $model,
      ]);
    }


    /**
     * Api page
     *
     */
    public function actionApi()
    {

      $this->layout = 'user';

      $userModel = Yii::$app->user->identity;

      return $this->render('api', [
          'userModel' => $userModel,
      ]);
    }


    /**
     * Copy settings
     *
     */
    public function actionCopy($id)
    {
      $this->layout = 'user';

      //Проверяем разрешение на создание настройки
      if( !self::checkRobotAdd() ) return $this->redirect(['api']);

      $result = CopyForm::prepareOutputData($id);

      return $this->redirect(['api']);
    }


    /**
     * Edit page
     *
     */
    public function actionEdit($id)
    {

      $this->layout = 'user';

      $result = Jetid::prepareUserOutputData($id);

      if( isset( $result['redirect'] ) ){
        return $this->redirect( $result['redirect'] );
      }

      return $this->render('edit', $result);
    }


    /**
     * Schedule page
     * @id = setting id
     */
    public function actionSchedule($id)
    {

      $this->layout = 'user';

      //security check
      $jetid = Jetid::find()
        ->where(['id' => $id, 'uz' => Yii::$app->user->id])
        ->one();
      if( empty($jetid)  ){
        Yii::$app->session->setFlash('error', "Настройка не найдена.");
        return $this->redirect(['api']);
      }

      //save schedule
      if( Yii::$app->request->post('dosave', 0) ){
        ScheduleForm::saveForm($id);

        Logs::AddSettingLogs("Пользователь отредактировал расписание настройки #{$id}");
        return $this->redirect(['api']);
      }

      $result = ScheduleForm::prepareUserOutputData($id);
      return $this->render('schedule', $result);
    }


    /**
     * Schedule delete
     * @id = schedule id
     */
    public function actionScheduleDelete($id){
      Schedule::loadModel($id)->delete();

      Logs::AddSettingLogs("Пользователь удалил расписание настройки #{$id}");
      return $this->redirect(['api']);
    }


    /**
     * Schedule disable
     * @id = schedule id
     */
    public function actionScheduleDisable($id, $disable){
      Schedule::loadModel($id)->disable($disable);
      return $this->redirect(['api']);
    }


    /**
     * Order
     *
     */
    public function actionOrder()
    {

      //подклчаем слой user
      $this->layout = 'user';

      $model = new OrderForm();

      if ( $model->load(Yii::$app->request->post()) ) {

          if( Yii::$app->user->identity->balans < Conf::getParams('order_price') ){
            Yii::$app->session->setFlash('error', "На вашем счету недостаточно средств.<br />
            Вы можете подать запрос через обычную тикет систему.");
            return $this->redirect(['order']);
          }

          $model->title = 'Заказ настройки';
          $model->filesList = \yii\web\UploadedFile::getInstances($model, 'filesList');
          if ($model->upload()) {

              if( $model->save() ){
                //списываем средства
                $userModel = Yii::$app->user->identity;
                $balans_before = $userModel->balans;
                $trafbalans_before = $userModel->trafbalans;
                $userModel->balans -= Conf::getParams('order_price');
                $userModel->save(false);

                //записываем в историю транзакций
                Paymentsinfo::add([
                  'cost' => Conf::getParams('order_price'),
                  'note' => "Было рублей: {$balans_before}, стало: {$userModel->balans}. Было реалов: {$trafbalans_before}, стало: {$userModel->trafbalans}",
                ], Paymentsinfo::BALANS_ADDSET_MINUS);

                Logs::AddTransactionLogs("Пользователь заказал платное создание настройки. Было рублей: {$balans_before}, стало: {$userModel->balans}. Было реалов: {$trafbalans_before}, стало: {$userModel->trafbalans}");
                //---
              }

              OrderForm::onTiketCreated($model);

              return $this->redirect('order');
          }
      }

      return $this->render('order', ['model' => $model]);
    }


    /**
     * ChangeSettings
     *
     */
    public function actionChangeSettings()
    {

      $model = new ChangeSettingsForm();

      if ( $model->load(Yii::$app->request->post()) ) {

          $siteid = intval(Yii::$app->request->post('siteid'));

          $jetid = Jetid::find()
            ->where(['id' => $siteid, 'uz' => Yii::$app->user->id])
            ->one();

          if( empty($jetid)  ){
            Yii::$app->session->setFlash('error', "Настройка не найдена.");
            return $this->redirect(['api']);
          }

          if( $jetid->set_pause + (3600 * Conf::getParams('set_pause')) > time() ){

            if( Conf::getParams('set_pause') >= 1 ){
              $timeLeft = ceil(($jetid->set_pause + (3600 * Conf::getParams('set_pause')) - time()) / 3600). " ч.";
            }else{
              $timeLeft = ceil(($jetid->set_pause + (3600 * Conf::getParams('set_pause')) - time()) / 60). " мин.";
            }

            Yii::$app->session->setFlash('error', "Заказ изменения настроек возможен через ~{$timeLeft}");
            return $this->redirect(['api']);
          }

          if(
            Conf::getParams('change_settings_price') > 0 &&
            Yii::$app->user->identity->balans < Conf::getParams('change_settings_price')
          ){
            Yii::$app->session->setFlash('error', "На вашем счету недостаточно средств.");
            return $this->redirect(['api']);
          }

          $model->title = 'Изменение настройки ID: '.$siteid;
          $model->filesList = \yii\web\UploadedFile::getInstances($model, 'filesList');
          if ($model->upload() && $model->save()) {

            $jetid->set_pause = time();
            $jetid->save(false);

            //списываем средства
            $userModel = Yii::$app->user->identity;
            $balans_before = $userModel->balans;
            $trafbalans_before = $userModel->trafbalans;
            $userModel->balans -= Conf::getParams('change_settings_price');
            $userModel->save(false);

            //записываем в историю транзакций
            Paymentsinfo::add([
              'cost' => Conf::getParams('change_settings_price'),
              'note' => "Заказ изменения настройки id {$siteid}. Было рублей: {$balans_before}, стало: {$userModel->balans}. Было реалов: {$trafbalans_before}, стало: {$userModel->trafbalans}",
            ], Paymentsinfo::BALANS_EDITSET_MINUS);
            //---

            Logs::AddTransactionLogs("Пользователь заказал платное изменение настройки #{$siteid}. Было рублей: {$balans_before}, стало: {$userModel->balans}. Было реалов: {$trafbalans_before}, стало: {$userModel->trafbalans}");

            ChangeSettingsForm::onTiketCreated($model);
          }
      }

      return $this->redirect('api');
    }


    /**
     * Archive settings
     *
     */
    public function actionArchive($id)
    {
      if (isset($_REQUEST['do']) && $_REQUEST['do'] == "delete") {
        ArchiveForm::del($id);
      }else{
        ArchiveForm::recovery($id, Yii::$app->user->id);
      }
      return $this->redirect(Yii::$app->request->referrer);
    }


    /**
     * Delete settings
     *
     */
    public function actionDelete($id)
    {
      //подклчаем слой user
      $this->layout = 'user';

      if ( Yii::$app->request->post('deleteid', 0) ) {
        if( Yii::$app->request->post('confirm', '') == 'yes' ){
          DeleteSettingsForm::delete($id, Yii::$app->user->id);
        }else{
          // юзер не отметил галочку
          DeleteSettingsForm::noConfirm();
        }
      }

      return $this->render('delete', ['id' => $id]);
    }


    /**
     * Проверяем, может ли пользователь добавить настройку
     *
     */
    public static function checkRobotAdd(){
      $userModel = Yii::$app->user->identity;

      //лимит на общее кол-во настроек в панели
      $limit_value = $userModel->limitsByType['limit_add_id']['trueTime'];

      //лимит на общее кол-во настроек в панели неплатившим
      $limit_value_not_paying = Conf::getParams('not_paying_limit_add_id');

      //берем общее кол-во настроек в панели
      $limit_value_id = $userModel->countSettings();//всего настроек

      if( !Yii::$app->user->identity->pay ){
        //Если не предусмотрено создание настроек неплатившим
        if( !$limit_value_not_paying ){
          Yii::$app->session->setFlash('error', "Создание настроек доступно активированным пользователям, для активации профиля пополните баланс.");
          return false;
        }else
        if( $limit_value_id >= $limit_value_not_paying ){
          Yii::$app->session->setFlash('error', "Максимальное количество настроек у пользователей без пополнения баланса = $limit_value_not_paying шт. <br />Для увеличения лимита, пожалуйста, пополните баланс. <br />Ваш лимит автоматически увеличится до $limit_value шт");
          return false;
        }
      }else{
        if( $limit_value_id >= $limit_value ){
          Yii::$app->session->setFlash('error', "Вы превысили общий лимит настроек ($limit_value шт всего), удалите некоторые шаблоны или воспользуйтесь старыми!<br />
          Увеличение лимита возможно через запрос в администрацию");
          return false;
        }
      }

      return true;
    }


    /**
     * add template
     *
     */
    public function actionAddId()
    {

      //подклчаем слой user
      $this->layout = 'user';

      //Проверяем разрешение на создание настройки
      if( !self::checkRobotAdd() ) return $this->redirect(['order']);

      $models = Templatesconf::find()->all();

      return $this->render('add-id', ['models' => $models]);
    }


    /**
     * Robot add
     *
     */
    public function actionRobotAdd($type)
    {

      //подклчаем слой user
      $this->layout = 'user';

      //Проверяем разрешение на создание настройки
      if( !self::checkRobotAdd() ) return $this->redirect(['order']);

      $ID = Yii::$app->user->id;
      $userModel = Yii::$app->user->identity;
      $rpay = $userModel['pay']; //юзер платил

      $templateModel = Templatesconf::findOne(['url' => $type]);

      //если есть настройка шаблона
      if( !empty($templateModel) ){
        if( $templateModel->disabled ){
          Yii::$app->session->setFlash('error', "В данное время редактировать/создавать шаблоны данного типа нельзя, происходят технические работы.");
          return $this->redirect(Yii::$app->request->referrer);
        }

        $className = '\app\models\Add'.$type.'Form';
        extract( $className::loadForm( get_defined_vars() ) );
        return $this->render('//robot/add-'.$type, get_defined_vars());
      }else{
        throw new \yii\web\HttpException(404);
      }
    }


    /**
     * Robot edit
     *
     */
    public function actionRobotEdit($id)
    {

      //подклчаем слой user
      $this->layout = 'user';

      $html = '';
      $ID = Yii::$app->user->id;
      $userModel = Yii::$app->user->identity;

      #получаем настройку из базы, проверяем принадлежит ли она юзеру
      $id_user = '';
      $types = '';
      if( isset($userModel->settings[$id]) ){
        //есть ли настройка у юзера, ложим если нашли в $id_user
        $id_user = $userModel->settings[$id]->id;
        //есть ли настройка у юзера, ложим если нашли в $id_user
        $types = $userModel->settings[$id]->config_type;
      }

      $type = str_replace('type-', '', $types);
      $templateModel = Templatesconf::findOne(['url' => $type]);

      //если есть настройка шаблона
      if( !empty($templateModel) ){
        if( $templateModel->disabled ){
          Yii::$app->session->setFlash('error', "В данное время редактировать/создавать шаблоны данного типа нельзя, происходят технические работы.");
          return $this->redirect(Yii::$app->request->referrer);
        }

        $out_types=$templateModel->name;
        $className = '\app\models\Edit'.$type.'Form';
        extract( $className::loadForm( get_defined_vars() ) );
        $html = $this->renderPartial('//robot/edit-'.$type, get_defined_vars());
      }else{
        $out_types='неопределен';
        $html = "Тип настройки <b>не определен</b>, редактирование невозможно!<br>
        Скорей всего Ваша настройка была создана в ручном режиме администратором, в таком случае свяжитесь с технической поддержкой!<br>
        Также вы можете удалить настройку и создать ее сами заново через <a href='/add-id'>мастер настройки</a>";

        Logs::AddSettingLogs("Пользователь не смог открыть шаблон редактирования (types не определился) Код: Edit-error-unknow", $ID);
      }

      return $this->render('//robot/edit', get_defined_vars());
    }



    /**
     * Displays Materials page.
     *
     * @return string
     */

     public function actionMaterials($url = null)
    {

     //проверяем юзера на авторизацию
    if(!\Yii::$app->user->isGuest){

    //подключаем слой user
    $this->layout = 'user';

     }

    if($url !== null){

        //берем одную статью по URL
        $model = Materials::find()->where(['url' => $url])->one();

        //ошибка если не найдено
       if(!$model || $model->disabled){
           throw new \yii\web\HttpException(404);
           return $this->redirect('/site/error');
       }

       return $this->render('materials_show', ['model' => $model]);
   }

      //делаем пагинацию
    $query = Materials::find()->where(['disabled' => 0]);
    $pages = new Pagination([
      'totalCount' => $query->count(),
      'pageSize' => Conf::getParams('number_materials'),
    ]);
    $posts = $query->offset($pages->offset)->limit($pages->limit)->orderBy(['date' => SORT_DESC])->all();
    return $this->render('materials', compact('posts', 'pages'));
       }





      /**
     * Displays Pages page.
     *
     * @return string
     */
     public function actionPages($url = null)
    {
      //берем url страницы статьи
      if($url !== null){
        if(!\Yii::$app->user->isGuest){
          //подклчаем слой user
          $this->layout = 'user';
        }

        if( $url == 'index' ) $url = null; //Пропускаем страницу index

        $model = Pages::find()->where(['url' => $url])->one();

        //иначае редирект на 404
        if(!$model || $model->disabled){
            throw new \yii\web\HttpException(404);
            return $this->redirect('/site/error');
        }
        return $this->render('pages', ['model' => $model]);
      }
    }



      /**
     * Displays News page.
     *
     * @return string
     */
     public function actionNews()
    {
      //подключаем слой user
      $this->layout = 'user';


      //берем все статьи
      $query = News::find()->where(['disabled' => 0]);


      //если не найдено 404 ошиька
      if(!$query){
         throw new \yii\web\HttpException(404);
         return $this->redirect('/site/error');
      }

      $user = Yii::$app->user->identity;
      $user->news = '1';
      $user->save(false);

       //пагинация
      $pages = new Pagination([
        'totalCount' => $query->count(),
        'pageSize' => Conf::getParams('number_news'),
      ]);
      $posts = $query
        ->offset($pages->offset)
        ->limit($pages->limit)
        ->orderBy(['data' => SORT_DESC])
        ->all();

      return $this->render('news', compact('posts', 'pages'));
    }



      /**
     * Displays Balans page.
     *
     * @return string
     */
     public function actionBalans()
    {

       //подклчаем слой user
       $this->layout = 'user';

       //id юзера в базе
       $id = Yii::$app->user->id;

       //берем профиль конкретного юзера
       $userModel = Users::find()->where(['id' => $id])->one();

       //подключаем тарифы
       $tarif = Jettarif::find()->all();

       //цена для первого тарифа
       $first_tarif = Jettarif::find()->one();

       //курс доллара
       $usd = Conf::getParams('usd');

       //коэф-т наценки по умолчанию для юзера
       $coef_default = Conf::getParams('coef_default');

       //день удаления
       $day_delete = Conf::getParams('day_delete');

       //цена за 1к кредитов в рублях
       $priceperone = Conf::getParams('priceperone');

       //минимальная сумма оплаты
       $min_pay_summ = Conf::getParams('min_pay_summ');

       //---

       $model = new BalansForm();

       if ($model->load(Yii::$app->request->post()) && $model->validate()) {

           if($userModel->coef1 != 0){
             $coef1 = $userModel->coef1;
           }
           else{
               $coef1 = $coef_default;
           }

           //цена в рублях за 1к кредитов, с учетом коэфф-та юзера
           //цена в рублях 1к кредитов = доллар * цена долларах за 1к кредитов * коэфф-т юзера
           $priceonerub = $usd*$priceperone*$coef1;

           //Детали тарифа, для истории транзакций
           $tarifDetails = '';

           if( $model->tarch == 'rozn' ){ //розничный тариф
             //цена с учетом скидки для юзера
             $mcost = ($priceonerub*($userModel->koef == 0? 1: (100 - $userModel->koef)/100)/1000);

             //определяем баланс и реалы
             $balans = round($model->mytarinp * $mcost, 0);
             $trafbalans = $model->mytarinp;

             $tarifDetails = "Розничный тариф, $balans руб";

           }else{ //определенный тариф
             //загружаем выбранный тариф
             $tarifSelected = Jettarif::findOne($model->tarch);

             //цена за тариф в рублях
             $mcost = $priceonerub*$tarifSelected->count/1000;

             //цена с учетом скидки за тариф
             $mcost -= $mcost*$tarifSelected->skidka/100;

             //цена с учетом скидки для юзера
             $mcost = $mcost*($userModel->koef == 0? 1: (100 - $userModel->koef)/100);

             //определяем баланс и реалы
             $balans = round($mcost, 0);
             $trafbalans = $tarifSelected->count;

             $tarifDetails = "{$tarifSelected->name}, $balans руб";
           }

           //проверяем доступную сумму
           if( $userModel->balans < $balans ){
             Yii::$app->session->setFlash('error', "На вашем счету недостаточно средств.");
             return $this->redirect(['balans']);
           }

           //добавляем дополнительные реалы при покупке
           if( $userModel->procent > 0 ){
             $trafbalans += round($trafbalans/100*$userModel->procent);
           }

           //сохраняем данные
           $trafbalans_before = $userModel->trafbalans;
           $balans_before = $userModel->balans;
           $userModel->balans -= $balans;
           $userModel->trafbalans += $trafbalans;
           $userModel->notify_send = 0;

           if( $userModel->save(false) ){
             Yii::$app->session->setFlash('success', "
             Баланс пополнен на {$trafbalans} реалов.<br />
             С основного баланса списано {$balans} руб.
             ");

             //сохраняем детали платежа
             Paymentsinfo::add([
               'cost' => $trafbalans,
               'note' => "Покупка пакета реалов - {$tarifDetails}. Было рублей: {$balans_before}, стало: {$userModel->balans}. Было реалов: {$trafbalans_before}, стало: {$userModel->trafbalans}",
             ], Paymentsinfo::TRAFBALANS_BUY);

			 //проверка на мошенничество
			 Antifraud::add([
			  'type' => Antifraud::TRAFBALANS_BUY,
			  'cost' => $trafbalans,
			  'note' => "Покупка пакета реалов - {$tarifDetails}. Было реалов: {$trafbalans_before}, стало: {$userModel->trafbalans}",
			], false);

             //сохраняем лог
             Logs::AddTransactionLogs("Пользователь пополнил баланс на " .
               $trafbalans . " реалов, потратил {$balans} руб. Было рублей: {$balans_before}, стало: {$userModel->balans}. Было реалов: {$trafbalans_before}, стало: {$userModel->trafbalans}", $id);
           }

           return $this->redirect(['balans']);
       }

       //Пополнение рублёвого баланса
       $modelPay = new PayForm();
       if ($modelPay->load(Yii::$app->request->post()) && $modelPay->validate()) {
         return $modelPay->printForm();
       }

       //передаем переменные
       return $this->render('balans', [
           'model' => $model,
           'modelPay' => $modelPay,
           'userModel'=> $userModel,
           'tarif' => $tarif,
           'usd' => $usd,
           'day_delete' => $day_delete,
           'priceperone' => $priceperone,
           'min_pay_summ' => $min_pay_summ,
           'first_tarif' => $first_tarif,
           'coef_default' => $coef_default,
       ]);
   }



     /**
    * Payment.
    *
    * @type = payment system
    * @action = check or result
    */
    public function actionPay($type, $action)
   {
     if( $action == 'check' ){
       return PayForm::checkPayment($type);
     }

     if( $action == 'result' ){
       $result = PayForm::showResult();

       if( $result === false ){
         return $this->redirect(['pages', 'url'=>'fail']);
       }
     }
     return $this->redirect(['balans']);
   }






     /**
     * Displays Transactions page.
     *
     * @return string
     */

     public function actionTransactions()
    {

         //если юзер не авторизован
    if(!\Yii::$app->user->isGuest){

    $this->layout = 'user';

    //id юзера
    $id = Yii::$app->user->id;

    //берем платежи пользователя в системе
    $model = Paymentsinfo::find()->where(['userid' => $id]);

    //если платежей нет
    if(!$model){
           throw new \yii\web\HttpException(404);
           return $this->redirect('/site/error');
       }

    //пагинация
    $pages = new Pagination([
      'totalCount' => $model->count(),
      'pageSize' => Conf::getParams('number_transactions'),
    ]);
    $posts = $model->offset($pages->offset)->limit($pages->limit)->orderBy(['time' => SORT_DESC])->all();
    return $this->render('transactions', compact('posts', 'pages', 'model'));

    }

    //если юзер не авторизован
     else{
        return $this->redirect('/login');
    }


    }



      /**
     * Displays Pages page.
     *
     * @return string
     */
     public function actionNewpass()
    {

    $model = new NewpassForm;

    if ($model->load(\Yii::$app->request->post()) && $model->validate()) {

      $model = Users::find()->where([
        'login' => $model->login,
        'email' => $model->email
      ])->one();

      $token = $model->login."=".$model->pass;
      $resetLink = Url::to(['reset-pass', 'token'=>$token], true);

      Yii::$app->mailer->compose()
        ->setFrom([Yii::$app->params['sendFrom']['email'] => Yii::$app->params['sendFrom']['name']])
        ->setReplyTo(Yii::$app->params['sendFrom']['email'])
        ->setTo($model->email)
        ->setSubject('Сброс пароля')
        ->setTextBody(
          "Чтобы сбросить пароль, перейдите по ссылке: {$resetLink}\n"
        )
        ->send();

      Yii::$app->session->addFlash('info', 'На Вашу почту отправлена инструкция по восстановлению пароля.');

      Logs::AddLoginLogs("Пользователь запросил инструкцию по смене пароля");

      return $this->goHome();
    }

    return $this->render('newpass', [
      'model' => $model,
    ]);
  }



    /**
   * Displays Pages page.
   *
   * @return string
   */
   public function actionResetPass($token)
  {

    list($login, $pass) = explode("=", $token);

    $model = Users::find()->where([
      'login' => $login,
      'pass' => $pass
    ])->one();

    if( empty($model) ){
      return $this->goBack();
    }

    $key = Conf::getParams('solkey');
    $pass = mt_rand(11111, 99999);
    $pass_md5 = md5($pass . $key);

    // изменяем пароль на новый и сохраняем новый пароль
    $model->pass = $pass_md5;
    $model->clearpass = ''; //Затираем установленный админом пароль
    $model->save(false);

    Yii::$app->mailer->compose()
      ->setFrom([Yii::$app->params['sendFrom']['email'] => Yii::$app->params['sendFrom']['name']])
      ->setReplyTo(Yii::$app->params['sendFrom']['email'])
      ->setTo($model->email)
      ->setSubject('Новый пароль')
      ->setTextBody(
        "Логин: {$model->login}\n".
        "Новый пароль: {$pass}\n".
        "Возможно в целях безопасности Вас попросят ввести капчу при входе!\n".
        "Внимание, после входа в аккаунт обязательно смените пароль!"
      )
      ->send();

    Logs::AddLoginLogs("Пользователь получил новый пароль на почту");

    Yii::$app->session->addFlash('success', 'Новый пароль отправлен на Вашу почту.');
    return $this->goHome();
  }
}
