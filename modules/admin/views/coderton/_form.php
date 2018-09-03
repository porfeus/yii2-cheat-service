<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Coderton */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="coderton-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'command')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'pair')->dropdownList(['1'=>'Да', '0'=>'Нет']) ?>

    <?= $form->field($model, 'code_before')->textarea(['rows' => 10]) ?>

    <?= $form->field($model, 'code_after')->textarea(['rows' => 10]) ?>

    <?= $form->field($model, 'defaults')->textInput(['maxlength' => true])->label('Значения по умолчанию (через запятую). Например, значения "1,2" в код можно подставить через шаблоны {v1} и {v2}') ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<script>
$('[name="Coderton[pair]"]').on('change', function(){
  if( $(this).val() == '0' ){
    $('.field-coderton-code_after').hide();
  }else{
    $('.field-coderton-code_after').show();
  }
}).triggerHandler('change');
</script>
