<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Авторизация на сайте';
$this->params['breadcrumbs'][] = $this->title;

$this->registerCss("
#login-form div.form-group{
  display: inline;
}
");
?>

<div class="contentblockall">
   <div class="contentblock">
      <div class="contentblockblock">
         <div class="contentblocktableblock">
            <div class="contentblocktable">
               <div class="contentblocktablebgtitle">
                  <div class="contentblocktabletitle">
                     <h1>Вход на сайт</h1>
                  </div>
               </div>
               <div class="contentblocktablebgcontblock">
                  <div class="contentblocktablebgcont">
 <br/>
                    <?php $form = ActiveForm::begin([
                      'id' => 'login-form',
                      'options' => [
                        'class' =>'application'
                      ],
                      'layout' => 'horizontal',
                      'enableClientValidation' => false,
                      'fieldConfig' => [
                          'template' => "{input}",
                      ],
                    ]); ?>
                    <div class="erorconttext"><?//= $form->errorSummary($model) ?></div>

                    Логин:
                    <?= $form->field($model, 'username')->textInput(['autofocus' => true, 'style'=>'margin-right: 10px;']) ?>

                    Пароль:
                    <?= $form->field($model, 'password')->passwordInput() ?>

                    <?= Html::submitButton('Вход на сайт', ['class' => 'fbutton2', 'name' => 'login-button']) ?>
                    <?php ActiveForm::end(); ?>


                     <a href="/newpass">Забыли пароль? Восстановите его!</a><br /><br />
                     <div class="info"></div>
                     <p style="margin-left:15px; font-size:100%;">Текст с картинки вводится в нижнем регистре<br></p>
                     <div class="info"></div>
                     <p style="margin-left:15px; font-size:100%;">Если вы забыли пароль воспользуйтесь ссылкой восстановления<br>
                     </p>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
