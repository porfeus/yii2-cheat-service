<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\models\UsersSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<?php $form = ActiveForm::begin([
  'action' => ['index'],
  'method' => 'get',
  'options' => ['class' => 'form-inline'],
  'enableClientValidation' => false,
  'fieldConfig' => [
      'template' => "{input}&nbsp;",
  ],
]); ?>
<?= $form->field($model, 'search_value')->textInput(['placeholder'=>'Найти']) ?>
<?= $form->field($model, 'search_type')->dropDownList([
  'id'=>'по ID юзера',
  'login'=>'по логину юзера',
  'email'=>'по e-mail юзера',
  'ip'=>'по IP-адресу',
]) ?>

<?= $form->field($model, 'search_from')->textInput(['placeholder'=>'Дата От', 'class' => 'form-control date-range-selector']) ?>
<?= $form->field($model, 'search_to')->textInput(['placeholder'=>'Дата До', 'class' => 'form-control date-range-selector']) ?>

<div class="form-group">
  <?= Html::submitButton('Поиск', ['class' => 'btn btn-default']) ?>
</div>

<?php ActiveForm::end(); ?>
