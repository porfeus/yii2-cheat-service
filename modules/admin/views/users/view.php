<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Users */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="users-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => array_merge([
            'id',
            'login',
            'pass',
            'clearpass',
            'fio:ntext',
            'v',
            'email:ntext',
            'coef1',
            'r',
            'balans',
            'trafbalans',
          //  'type',
            'koef',
            'procent',
          //  'idjet',
            'info:ntext',
            'ses',
            'ip',
            'unicses',
            'news',
            'pay',
            'pv',
            'lastdate',
          //  's1i2',
            'notify:ntext',
            'notify_send',
        ], array_keys($model->limits)),
    ]) ?>

</div>
