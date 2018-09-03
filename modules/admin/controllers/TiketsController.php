<?php

namespace app\modules\admin\controllers;

use Yii;
use app\models\Tikets;
use app\modules\admin\models\TiketsSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\Conf;
use yii\helpers\Url;
use yii\filters\AccessControl;

/**
 * TiketsController implements the CRUD actions for Tikets model.
 */
class TiketsController extends Controller
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
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Отключаем проверку свежести ключа авторизации, чтобы избежать ошибки 400
     */
    public function beforeAction($action) {

        $this->enableCsrfValidation = false;

        return parent::beforeAction($action);
    }

    /**
     * Lists all Tikets models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TiketsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @return null
     */
    public function onTiketAnswered($model)
    {
      $siteLink = Url::home(true);
      $tiketLink = Url::to(['/tikets/view', 'id'=>$model->id], true);

      Yii::$app->mailer->compose()
        ->setFrom([
          Yii::$app->params['sendFrom']['email'] => Yii::$app->params['sendFrom']['name']
        ])
        ->setReplyTo(Yii::$app->params['sendFrom']['email'])
        ->setTo( $model->userInfo->email )
        ->setSubject('Новое сообщение от администрации')
        ->setHtmlBody(
          "Для Вашего тикета был получен ответ от администрации <a href=\"$siteLink\">Go-IP.ru</a><br /> (для просмотра ответа зайдите в <a href=\"$tiketLink\">тикет систему сайта</a>)"
        )
        ->send();
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
            $model->is_support = 1;

            if( $model->save() ){
              $this->onTiketAnswered($tiket);
            }
            return $this->redirect(['view', 'id' => $id]);
        }
        $dataProvider = Tikets::getUserTiketMessages($id);

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

        $model->archived = 1;
        $model->save(false);

        return $this->redirect(['index']);
    }

    /**
     * Deletes an existing Users model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete()
    {
        $selection = Yii::$app->request->post('selection');

        if( !empty($selection) ){
          $ids = array_values($selection);
          $models = Tikets::find()->where(['IN', 'id', $ids])->all();
          foreach( $models as $model ){
            $model->delete();
          }
        }

        return $this->redirect(Yii::$app->request->referrer);
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
