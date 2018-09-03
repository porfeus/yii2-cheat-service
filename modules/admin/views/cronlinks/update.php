<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\CronLinks */

$this->title = 'Изменить ссылку: '.$model->name;
$this->params['breadcrumbs'][] = ['label' => 'Планировщик', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Изменить';
?>
<div class="cron-links-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
