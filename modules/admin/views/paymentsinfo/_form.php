<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Paymentsinfo */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="paymentsinfo-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'payment')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'userid')->textInput() ?>

    <?= $form->field($model, 'cost')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'time')->textInput() ?>

    <?= $form->field($model, 'type')->dropDownList($model->typeVariants) ?>

    <?= $form->field($model, 'note')->textInput() ?>

    <?= $form->field($model, 'metod')->textarea(['rows' => 1]) ?>

    <?= $form->field($model, 'status')->dropDownList([
      "OK-PAY"=>"Оплачен",
      "NO"=>"Ожидает",
    ]) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
