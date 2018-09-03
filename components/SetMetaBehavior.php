<?php

namespace app\components;

use Yii;
use yii\base\Behavior;
use app\models\Pages;

class SetMetaBehavior extends Behavior
{
    /**
    * Устанавливаем title, keywords и description для функциональных страниц
    */
    public function setTitleAndMetatags()
    {
      $url = trim($_SERVER['REQUEST_URI'], '/');
      if( $url == '' ) $url = 'index';

      $model = Pages::find()->where(['url' => $url])->one();

      if( !empty($model) ){
        Yii::$app->view->title = $model['title'];
        Yii::$app->view->registerMetaTag(['name' => 'keywords', 'content' => $model['meta_key']]);
        Yii::$app->view->registerMetaTag(['name' => 'description', 'content' => $model['description']]);
      }
    }
}
