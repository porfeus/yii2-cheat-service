<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\CronLinks */

$this->title = 'Просмотр ссылки';
$this->params['breadcrumbs'][] = ['label' => 'Планировщик', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cron-links-view">

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
            'id',
            'name',
            [
              'attribute' => 'link',
              'format' => 'html',
              'value' => function($model){
                return $model->htmlLinkWithoutName;
              }
            ],
        ],
    ]) ?>

</div>
