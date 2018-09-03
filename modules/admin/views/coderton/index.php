<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\admin\models\CodertonSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Coderton';
$this->params['breadcrumbs'][] = $this->title;
$this->registerCss("
.break-all{
  word-break: break-all !important;
  width:300px;
  white-space: pre-wrap;
}
");
?>
<div class="coderton-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Создание команды', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'command',
            [
              'attribute' => 'pair',
              'value' => function($model){
                if( $model->pair == 1 ){
                  return 'Да';
                }else{
                  return 'Нет';
                }
              }
            ],
            [
              'attribute' => 'code_before',
              'format' => 'html',
              'value' => function($model){
                return '<div class="break-all">'.$model->code_before.'</div>';
              }
            ],
            [
              'attribute' => 'code_after',
              'format' => 'html',
              'value' => function($model){
                return '<div class="break-all">'.$model->code_after.'</div>';
              }
            ],
            'defaults',

            ['class' => 'yii\grid\ActionColumn'],
        ],
        'tableOptions' =>[
          'class' => 'table table-striped table-bordered',
          'style' => 'width: 1140px; max-width: none;',
        ]
    ]); ?>
</div>
