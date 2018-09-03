<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Paymentsinfo */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Paymentsinfos'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="paymentsinfo-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Обновить'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Удалить'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Удалить?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'payment',
            [
              'attribute' => 'userid',
              'format'=>'html',
              'value' => function($model){
                return '<a class="blank" href="/admin/users?UsersSearch[search_value]='.$model->userid.'&UsersSearch[search_type]=user">'.$model->userid.'</a>';
              }
            ],
            [
              'attribute' => 'cost',
              'value' => function($model){
                return $model->cost.' '.$model->currency;
              }
            ],
            'time',
            //'metod:ntext',
            [
              'attribute' => 'type',
              'value' => function ($model){
                return $model->colouredTypeText;
              },
              'format'=>'html',
            ],
            'note:ntext',
            [
              'attribute' => 'status',
              'value' => function ($model){
                return $model->colouredStatusText;
              },
              'format'=>'html',
            ],
        ],
    ]) ?>

</div>
