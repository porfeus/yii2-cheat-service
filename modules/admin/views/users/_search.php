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
  'email'=>'по e-mail',
  'login'=>'по логину',
  'user'=>'по id юзера',
  'set'=>'по id настройки',
  'openset'=>'открыть id настройки',
  'open_schedule'=>'открыть расписание',
  'open_template'=>'открыть шаблон',
  ]) ?>
<div class="form-group">
  <?= Html::submitButton('Поиск', ['class' => 'btn btn-default']) ?>
</div>

<?php ActiveForm::end(); ?>
