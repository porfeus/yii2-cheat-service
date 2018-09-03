<?php

namespace app\modules\admin\controllers;

use Yii;
use app\modules\admin\models\UsersForm;
use app\modules\admin\models\UsersBalansForm;
use app\modules\admin\models\UsersSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use app\models\UserIdentity;
use app\models\Jetid;
use app\models\DeleteSettingsForm;
use app\models\Schedule;
use app\models\ScheduleForm;
use app\models\ArchiveForm;
use app\models\Users;
use app\models\Logs;
use app\models\Templatesconf;

/**
 * UsersController implements the CRUD actions for Users model.
 */
class UsersController extends Controller
{

    public $layout = "/admin";

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'user' => Yii::$app->getModule('admin')->user,
                'rules' => [
                    [
                        'allow' => true,
                        //доступ открыт только авторизованным
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Отключаем запрет на POST-запросы у некоторых страниц
     */
    public function beforeAction($action) {

        if( in_array($action->id, ['updateset']) ){
          $this->enableCsrfValidation = false;
        }
        return parent::beforeAction($action);
    }

    /**
     * Login to user account.
     */
    public function actionLogin($id)
    {
      $user = UserIdentity::findIdentity($id);
      Yii::$app->user->login($user);

      return $this->redirect(['/site/balans']);
    }

    /**
     * Change balance
     */
    public function actionBalans($id)
    {
      $formModel = new UsersBalansForm();
      $userModel = $this->findUserModel($id);

      if ($formModel->load(Yii::$app->request->post()) && $formModel->validate() ) {

          if( $formModel->type == 2 && 
          (
            ($formModel->currency == 1 && $formModel->amount > $userModel->balans) ||
            ($formModel->currency == 2 && $formModel->amount > $userModel->trafbalans)
          )){
            $formModel->addError('amount', 'Недостаточно средств для списания');
          }else{
            Yii::$app->session->setFlash('success', 'Операция выполнена');
            $formModel->changeBalans($userModel);
          }
      }

      return $this->render('balans', [
          'userModel' => $userModel,
          'formModel' => $formModel,
      ]);
    }

    /**
     * Lists all Users models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UsersSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $all_balance_rub = round(UsersForm::find()->sum('balans'), 2);
        $all_balance_traf = round(UsersForm::find()->sum('trafbalans'), 2);
        $all_users = UsersForm::find()->count();
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'all_balance_rub' => $all_balance_rub,
            'all_balance_traf' => $all_balance_traf,
            'all_users' => $all_users,
            'templates' => Templatesconf::find()->asArray()->all(),
        ]);
    }

    /**
     * Displays a single Users model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Users model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new UsersForm();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Users model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Update jetid params
     */
    public function actionUpdateset($id)
    {
      $result = Jetid::prepareAdminOutputData($id);

      if( isset( $result['redirect'] ) ){
        return $this->redirect( $result['redirect'] );
      }

      return $this->render('updateset', $result);
    }


    /**
     * Robot add
     *
     */
    public function actionRobotAdd($type, $user_id)
    {
      $userModel = Users::findOne($user_id);
      if( !$userModel ){
        throw new NotFoundHttpException(Yii::t('app', 'Пользователь не найден.'));
      }
      $ID = $userModel->id;
      $rpay = '1'; //мы всегда "платили"

      $templateModel = Templatesconf::findOne(['url' => $type]);

      if( !empty($templateModel) ){
        $className = '\app\models\Add'.$type.'Form';
        extract( $className::loadForm( get_defined_vars() ) );
        $html = $this->renderPartial('//robot/add-'.$type, get_defined_vars());
      }else{
        throw new \yii\web\HttpException(404);
      }

      return $this->render('robot-add', get_defined_vars());
    }

    /**
     * Update jetid params
     */
    public function actionRobotEdit($id)
    {
      $userModel = Users::searchUserBySettingsId($id);
      if( !$userModel ){
        throw new NotFoundHttpException(Yii::t('app', 'Пользователь не найден.'));
      }
      $ID = $userModel->id;

      $html = '';

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

      if( !empty($templateModel) ){
        $out_types=$templateModel->name;
        $className = '\app\models\Edit'.$type.'Form';
        extract( $className::loadForm( get_defined_vars() ) );
        $html = $this->renderPartial('//robot/edit-'.$type, get_defined_vars());
      }else{
        $out_types='неопределен';
        $html = "Тип настройки <b>не определен</b>, редактирование невозможно!<br>
        Скорей всего Ваша настройка была создана в ручном режиме администратором, в таком случае свяжитесь с технической поддержкой!<br>
        Также вы можете удалить настройку и создать ее сами заново через <a href='/add-id'>мастер настройки</a>";

        Logs::AddSettingLogs("ID пользователя: $ID,  Не смог открыть шаблон редактирования (types не определился) Код: Edit-error-unknow", $ID);
      }

      return $this->render('robot-edit', get_defined_vars());
    }

    /**
     * Archive id
     */
    public function actionArchiveset($id, $user_id)
    {
      ArchiveForm::recovery($id, $user_id);
      return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * Delete jetid params
     */
    public function actionDeleteset($id, $user_id)
    {
      DeleteSettingsForm::delete($id, $user_id);

      return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * Open schedule
     * @id = setting id
     */
    public function actionSchedule($id)
    {

      //save schedule
      if( Yii::$app->request->post('dosave', 0) ){
        ScheduleForm::saveForm($id, true);
        return $this->refresh();
      }

      $result = ScheduleForm::prepareUserOutputData($id, true);
      return $this->render('//site/schedule', $result);
    }


    /**
     * Schedule delete
     * @id = schedule id
     */
    public function actionScheduleDelete($id){
      $model = Schedule::findOne(['id' => $id]);
      if( $model ){
        $model->delete();
      }
      return $this->redirect(Yii::$app->request->referrer);
    }


    /**
     * Schedule disable
     * @id = schedule id
     */
    public function actionScheduleDisable($id, $disable){
      $model = Schedule::findOne(['id' => $id]);
      if( $model ){
        $model->disable($disable);
      }
      return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * Deletes an existing Users model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Users model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Users the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = UsersForm::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
    protected function findUserModel($id)
    {
       if (($model = Users::findOne($id)) !== null) {
           return $model;
       }

       throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
