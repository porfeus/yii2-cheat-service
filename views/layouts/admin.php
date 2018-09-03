<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AdminAsset;
use yii\web\YiiAsset;
use yii\bootstrap\BootstrapAsset;
use app\models\Materials;
use app\models\Tikets;

AdminAsset::register($this);

Yii::$app->language =  'ru';

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <?= Html::csrfMetaTags() ?>
    <meta charset="<?= Yii::$app->charset ?>">
    <link href="/images/favicon.ico" rel="shortcut icon" type="image/x-icon">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title>Admin: <?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>

    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <style type="text/css">
        .pagination_text{
            padding: 0px;
            margin: 0px;
            list-style-type: none;
            text-align: center;
            border-top: 1px dotted #b3b4b4;
        }
        .pagination_text li {
            display: inline-block;
            margin-top: 36px;
        }
        .pagination_text li a{
            background: #d8d7d7;
            color: #1a1b1b;
            font-size: 18px;
            border-radius: 4px;
            padding: 4px 13px;
            margin-right: 15px;
        }
        .pagination_text li a:hover{
            background: #d8d7d7;
            color: #1a1b1b;
            text-decoration: none;
        }
        .pagination_text li a.pagina_active{
            color: #fff;
            background: #0390c4;
            text-decoration: none;
        }
    </style>
</head>
<body>
<?php $this->beginBody() ?>


    <?php
    if( !Yii::$app->getModule('admin')->user->isGuest ){
      NavBar::begin([
          'brandLabel' => '',
          'brandUrl' => Yii::$app->homeUrl,
          'options' => [
              'class' => 'navbar-inverse ',
          ],
      ]);

          $tiketsLabel = 'Тикеты';
          $newTiketsNum = Tikets::countNewTikets();
          if( $newTiketsNum > 0 ){
            $tiketsLabel.= ' (<span style="color:red">'.$newTiketsNum.'</span>)';
          }

          echo Nav::widget([
              'options' => ['class' => 'navbar-nav navbar-right'],
              'items' => [
                  ['label' => 'Настройки', 'url' => ['/admin/conf']],
                  ['label' => 'Блог (материалы)', 'url' => ['/admin/materials']],
                  ['label' => 'Новости', 'url' => ['/admin/news']],
                  ['label' => 'Транзакции', 'url' => ['/admin/paymentsinfo']],
                  ['label' => 'Лог сайта', 'url' => ['/admin/logs']],
                  ['label' => 'Пользователи', 'url' => ['/admin/users']],
                  ['label' => $tiketsLabel, 'url' => ['/admin/tikets'], 'encode'=>false],
                  ['label' => 'Страницы сайта', 'url' => ['/admin/pages']],
                  ['label' => 'Тарифы реалов', 'url' => ['/admin/jettarif']],
                  ['label' => 'Лимиты к API', 'url' => ['/admin/timelimits']],
                  ['label' => 'Шаблоны', 'url' => ['/admin/templates']],
                  ['label' => 'Настройки шаблонов', 'url' => ['/admin/templatesconf']],
                  ['label' => 'Планировщик', 'url' => ['/admin/cronlinks']],
                  ['label' => 'Coderton', 'url' => ['/admin/coderton']],
                  ['label' => 'Антифрод', 'url' => ['/admin/antifraud']],
                  ['label' => 'Выйти', 'url' => ['/admin/site/logout']],
              ],
          ]);

      NavBar::end();
    }
    ?>

</br></br>
    <div class="container" <?php echo Yii::$app->controller->id=='order-product'?' style="margin-left: 0px;"':''; ?>>
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
    <?=\app\widgets\Alert::widget()?>
    <?php echo $content ?>
    </div>


<?php $this->endBody() ?>

<div style="height:100px"></div>

</body>
</html>
<?php $this->endPage() ?>
