<?php

namespace app\controllers;

use Yii;
use app\models\Tikets;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\Conf;
use yii\helpers\Url;
use app\models\Logs;

/**
 * TiketsController implements the CRUD actions for Tikets model.
 */
class TiketsController extends Controller
{

    public $layout = "/user";

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            \app\components\SetMetaBehavior::className(),
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
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
        $this->enableCsrfValidation = false;

        return parent::beforeAction($action);
    }

    /**
     * @inheritdoc
     */
    public function onTiketCreated($model, $sendMessage = 1)
    {
      Yii::$app->session->addFlash('info',
      'Ваш тикет сохранен и передан администрации. Ожидайте...');

      $tiketLink = Url::to(['admin/tikets/view', 'id'=>$model->id], true);

      if( $sendMessage ){
        Yii::$app->mailer->compose()
          ->setFrom([
            Yii::$app->params['sendFrom']['email'] => 'Новый тикет в системе'
          ])
          ->setReplyTo(Yii::$app->params['sendFrom']['email'])
          ->setTo( Conf::getParams('adminmail') )
          ->setSubject('Новый ответ в тикете')
          ->setHtmlBody(
            "Был добавлен <a href=\"$tiketLink\">новый тикет</a> от пользователя {$model->user} ({$model->userInfo->login})"
          )
          ->send();
      }

      Logs::AddOtherLogs("Пользователь создал новый тикет #{$model->id}");
    }

    /**
     * Lists all Tikets models.
     * @return mixed
     */
    public function actionIndex()
    {
        $model = new Tikets();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
          //добавляем новый тикет, если не превышено количество открытых
          if( Tikets::countUserNewTikets() < Conf::getParams('max_open_tikets_num') ){
            $model->save();
            $this->onTiketCreated($model);
            return $this->redirect(['index']);
          }else{
            Yii::$app->session->setFlash('error', "Превышено допустимое количество открытых тикетов: не более ".Conf::getParams('max_open_tikets_num')." шт.");
          }
        }
        $dataProvider = Tikets::getUserTikets();

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'model' => $model,
        ]);
    }

    /**
     * Lists all Tikets models.
     * @return mixed
     */
    public function actionArchive()
    {
        $model = new Tikets();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
          //добавляем новый тикет, если не превышено количество открытых
          if( Tikets::countUserNewTikets() < Conf::getParams('max_open_tikets_num') ){
            $model->save();
            $this->onTiketCreated($model);
            return $this->redirect(['index']);
          }else{
            Yii::$app->session->setFlash('error', "Превышено допустимое количество открытых тикетов: не более ".Conf::getParams('max_open_tikets_num')." шт.");
          }
        }
        $dataProvider = Tikets::getUserTikets($archive = true);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'model' => $model,
        ]);
    }

    /**
     * Displays a single Tikets model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = new Tikets();
        $tiket = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            $model->parent_id = $id;
            $model->title = $tiket->title;

            //Определяем, ответил ли админ на тикет (если нет - не отправляем письмо админу о новом сообщении)
            $adminIsAnswered = $tiket->answered;

            if( $model->save() ){ //Здесь уже статус "админ ответил" меняется
              $this->onTiketCreated($tiket, $adminIsAnswered);
            }
            return $this->redirect(['view', 'id' => $id]);
        }
        $dataProvider = Tikets::getUserTiketMessages($id);

        // check access
        if( $tiket->user != Yii::$app->user->getId() ){
          return $this->redirect(['index']);
        }

        // set readed
        if( !$tiket->readed ){
          $tiket->readed = 1;
          $tiket->save(false);
        }

        return $this->render('view', [
            'dataProvider' => $dataProvider,
            'tiket' => $tiket,
            'model' => $model,
        ]);
    }

    /**
     * Close tiket
     */
    public function actionClose($id)
    {
        $model = $this->findModel($id);

        if( $model->user != Yii::$app->user->getId() || $model->parent_id ){
          return $this->redirect(['index']);
        }

        $model->readed = 1;
        $model->archived = 1;
        $model->save(false);

        Logs::AddOtherLogs("Пользователь закрыл тикет #{$model->id}");

        return $this->redirect(['index']);
    }

    /**
     * Download file
     */
     public function actionFile($_)
     {
         $storagePath = Yii::getAlias('@webroot/files');

         if (!preg_match('/^[a-z0-9]+\.[a-z0-9]+$/i', $_) || !is_file("$storagePath/$_")) {
             throw new \yii\web\NotFoundHttpException('The file does not exists.');
         }
         return Yii::$app->response->sendFile("$storagePath/$_", $_);
     }

    /**
     * Finds the Tikets model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Tikets the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Tikets::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
