<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Users */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="users-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->errorSummary($model) ?>

    <?= $form->field($model, 'login')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'pass')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'clearpass')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'fio')->textarea(['rows' => 1]) ?>

    <?= $form->field($model, 'v')->dropDownList(['0'=>'Да', '1'=>'Нет']) ?>

    <?= $form->field($model, 'email')->textarea(['rows' => 1]) ?>

    <?= $form->field($model, 'r')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'coef1')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'balans')->textInput() ?>

    <?= $form->field($model, 'trafbalans')->textInput(['maxlength' => true]) ?>

    <?//= $form->field($model, 'type')->textInput() ?>

    <?= $form->field($model, 'koef')->textInput() ?>

    <?= $form->field($model, 'procent')->textInput() ?>

    <?= $form->field($model, 'date')->textInput() ?>

    <?//= $form->field($model, 'idjet')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'info')->textarea(['rows' => 1]) ?>

    <?= $form->field($model, 'ses')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'ip')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'unicses')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'news')->dropDownList(['1'=>'Да', '0'=>'Нет']) ?>

    <?= $form->field($model, 'pay')->dropDownList(['1'=>'Да', '0'=>'Нет']) ?>

    <?= $form->field($model, 'pv')->textInput() ?>

    <?= $form->field($model, 'lastdate')->textInput() ?>

    <?//= $form->field($model, 's1i2')->textInput() ?>

    <?= $form->field($model, 'notify')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'notify_send')->textInput() ?>

    <b style="font-size: 125%;">Лимиты юзера (секунды)</b><br />
    <div style="padding: 10px; background-color: #f9f9f9; border: 1px solid #ddd;" class="form-group">

      <?= $form->field($model, 'defaultLimits')->checkbox([
        'onclick'=>"$('#limits_station').css('opacity', this.checked? 0.5:1)"
        ]) ?>

      <div id="limits_station">
      <?php
      foreach( $model->limits as $key=>$item ):
      ?>
      <?= $form->field($model, $key)->textInput() ?>
      <?php
      endforeach;
      ?>
      </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
