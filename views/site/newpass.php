<?php
/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;

$this->title = 'Восстановление пароля';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="contentblockall">
   <div class="contentblock">
      <div class="contentblockblock">
         <div class="contentblocktableblock">
            <div class="contentblocktable">
               <div class="contentblocktablebgtitle">
                  <div class="contentblocktabletitle">
                     <h1>Восстановление пароля</h1>
                  </div>
               </div>
               <div class="contentblocktablebgcontblock">
                  <div class="contentblocktablebgcont">
                    <?php $form = ActiveForm::begin([
                       'id' => 'newpass-form',
                       'enableClientValidation' => false,
                       'fieldConfig' => [
                           'template' => "{input}",

                       ],
                    ]);?>
                     <div class="erorconttext"><?//= $form->errorSummary($model) ?></div>

                     Введите данные (логин и e-mail) и новый пароль будет выслан на Вашу почту. <br/>Если вы не помните Ваши данные, свяжитесь с администрацией для уточнения деталей.
                     <table>


                           <tr>
                              <td style="width: 280px;">Ваш email: <font color="red">(*)</font></td>
                              <td>
                                <?= $form->field($model, 'email')->textInput(['autofocus' => true])->label('Ваш E-mail ') ?>
                              </td>
                           </tr>


                           <tr>
                              <td>Ваш логин в системе: <font color="red">(*)</font></td>
                              <td>
                                <?= $form->field($model, 'login')->textInput(['autofocus' => true])->label('Ваш логин в системе') ?>
                              </td>
                           </tr>
                           <tr>
                              <td>Решите пример: <font color="red">(*)</font></td>
                              <td>
                                <a href="#" onclick="document.getElementById('captcha').src = '/captcha?' + Math.random(); return false"><img style="width:180px;" id="captcha" src="/captcha" alt="Нажмите для смены примера" title="Нажмите для смены примера"></a><br />
                                (нажмите на картинку для обновления примера)<br>
                                <?= $form->field($model, 'verifyCode')->textInput([
                                  'style'=>'width:170px;'
                                  ]) ?>
                              </td>
                           </tr>
                           <tr>
                              <td></td>
                              <td> <?= Html::submitButton('Сменить пароль', ['class' => 'fbutton2']) ?></td>
                           </tr>
                     </table>
                     <?php ActiveForm::end(); ?>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
