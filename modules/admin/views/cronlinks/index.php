<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\admin\models\CronLinksSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Планировщик';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cron-links-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Создать ссылку', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
              'attribute' => 'link',
              'format' => 'html',
              'value' => function($model){
                return $model->htmlLink;
              }
            ],

            [
              'class' => 'yii\grid\ActionColumn',
              'headerOptions' => [
                'style' => 'width: 75px;',
              ],
            ],
        ],
    ]); ?>
</div>
