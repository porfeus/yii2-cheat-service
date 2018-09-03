<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\models\Conf;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\admin\models\AntifraudSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Антифрод';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="clearfix"></div>

<div class="row">
  <div class="col-md-12">
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>
  </div>
</div>

<div class="clearfix"></div>

<div class="antifraud-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Создать запись', ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('Очистить антифрод', ['clear'], [
          'class' => 'btn btn-danger',
          'style' => 'float: right',
          'onclick' => 'if(!confirm(\'Удалить все записи антифрода?\')) return false;',
        ]) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'rowOptions'=>function($model){
            if($model->cost >= Conf::getParams('suspicious_transaction_cost')){
                return ['class' => 'danger'];
            }
        },
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            [
              'attribute' => 'userid',
              'format'=>'html',
              'value' => function($model){
                return '<a class="blank" href="/admin/users?UsersSearch[search_value]='.$model->userid.'&UsersSearch[search_type]=user">'.$model->userid.'</a>';
              }
            ],
            [
              'attribute' => 'balance',
              'value' => function($model){
                return $model->balance.' '.$model->currency;
              }
            ],
            [
              'attribute' => 'cost',
              'format'=>'html',
              'value' => function($model){
                return '<b>'.$model->cost.' '.$model->currency.'</b>';
              }
            ],
            'date',
            [
              'attribute' => 'type',
              'filter' => $searchModel->typeVariants,
              'value' => function ($model, $key, $index){
                return $model->colouredTypeText;
              },
              'format'=>'html',
            ],
            'note:ntext',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
