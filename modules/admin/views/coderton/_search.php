<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\models\CodertonSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="coderton-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'command') ?>

    <?= $form->field($model, 'pair') ?>

    <?= $form->field($model, 'code_before') ?>

    <?= $form->field($model, 'code_after') ?>

    <?php // echo $form->field($model, 'defaults') ?>

    <div class="form-group">
        <?= Html::submitButton('Поиск', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Сброс', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
