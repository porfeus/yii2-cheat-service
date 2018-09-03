<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Coderton */

$this->title = 'Редактирование команды: '.$model->command.'';
$this->params['breadcrumbs'][] = ['label' => 'Coderton', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->command, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Редактирование';
?>
<div class="coderton-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
