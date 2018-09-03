<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Coderton */

$this->title = $model->command;
$this->params['breadcrumbs'][] = ['label' => 'Coderton', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="coderton-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
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
            'code_before:ntext',
            'code_after:ntext',
            'defaults',
        ],
    ]) ?>

</div>
