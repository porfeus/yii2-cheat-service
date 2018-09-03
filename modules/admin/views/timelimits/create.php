<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\TimeLimits */

$this->title = Yii::t('app', 'Create Time Limits');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Time Limits'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="time-limits-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
