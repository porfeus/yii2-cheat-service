<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Antifraud */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Антифрод', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="antifraud-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Изменить', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Удалить?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
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
              'value' => function ($model){
                return $model->colouredTypeText;
              },
              'format'=>'html',
            ],
            'note:ntext',
        ],
    ]) ?>

</div>
