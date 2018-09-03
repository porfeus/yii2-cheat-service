<?php

namespace app\modules\admin\controllers;

use Yii;
use yii\web\Controller;
use app\modules\admin\models\LoginForm;

/**
 * UsersController implements the CRUD actions for Users model.
 */
class SiteController extends Controller
{

    public $layout = "/admin";

    /**
     * Login action.
     *
     * @return Response|string
     */
     /**
      * Login action.
      *
      * @return Response|string
      */
     public function actionLogin()
     {
         if (!Yii::$app->getModule('admin')->user->isGuest) {
             return $this->redirect(['users/index']);
         }

         $model = new LoginForm();
         if ($model->load(Yii::$app->request->post()) && $model->login()) {
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
         if( !Yii::$app->getModule('admin')->user->isGuest ){
           Yii::$app->getModule('admin')->user->logout();
         }

         return $this->redirect(['login']);
     }
}
