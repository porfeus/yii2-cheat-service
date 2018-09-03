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
<b><big>Фильтр:</big></b><br />
<?= $form->field($model, 'suspicious')->checkbox(['onclick'=>'this.form.submit()']) ?>

<?php ActiveForm::end(); ?>
