<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $userModel app\models\Users */

$this->title = Yii::t('app', 'Изменение баланса: {nameAttribute}', [
    'nameAttribute' => $userModel->login,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Пользователи'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $userModel->login, 'url' => ['view', 'id' => $userModel->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Изменение баланса');


$script = <<< JS
  $('#usersbalansform-type').on('change', function(){
    if( $(this).val() == '1' ){
      $('#usersbalansform-comment').val('Возврат средств');
    }else{
      $('#usersbalansform-comment').val('Списание средств за настройку');
    }
  })
JS;
$this->registerJs($script);
?>
<div class="users-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="users-form">

        <?php $form = ActiveForm::begin(); ?>

        <?//= $form->errorSummary($formModel) ?>

        <div class="form-group">
            <b>Текущий баланс: </b> <?=$userModel->balans?> руб., <?=$userModel->trafbalans?> реал.
        </div>

        <?= $form->field($formModel, 'currency')->dropDownList($formModel->currencyVariants) ?>

        <?= $form->field($formModel, 'type')->dropDownList($formModel->typeVariants) ?>

        <?= $form->field($formModel, 'amount')->textInput(['maxlength' => true]) ?>

        <?= $form->field($formModel, 'comment')->textarea(['rows' => 1]) ?>

        <div class="form-group">
            <?= Html::submitButton(Yii::t('app', 'Выполнить'), ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>
