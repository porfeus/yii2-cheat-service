<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Templatesconf */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Настройки шаблонов', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="templatesconf-view">

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
            'value',
            'url:url',
        ],
    ]) ?>

</div>
