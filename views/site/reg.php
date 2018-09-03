<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;

//$this->title = 'Регистрация на сайте';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="contentblockall">
   <div class="contentblock">
      <div class="contentblockblock">
         <div class="contentblocktableblock">
            <div class="contentblocktable">
               <div class="contentblocktablebgtitle">
                  <div class="contentblocktabletitle">
                     <h1>Регистрация</h1>
                  </div>
               </div>
               <div class="contentblocktablebgcontblock">
                  <div class="contentblocktablebgcont">
                     <?php $form = ActiveForm::begin([
                       'id' => 'reg-form',
                       'class' => 'application',
                       'layout' => 'horizontal',
                       'enableClientValidation' => false,
                       'fieldConfig' => [
                           'template' => "{input}",
                       ],
                     ]); ?>
                     <div class="erorconttext"><?//= $form->errorSummary($model) ?> </div>
                     <table>
                           <tr>
                              <td style="width: 280px;">Логин: <font color="red">(*)</font></td>
                              <td>
                                <?= $form->field($model, 'login')->textInput(['autofocus' => true, 'placeholder' => 'Ваш логин в системе']) ?></td>
                           </tr>

						               <tr>
                              <td>E-mail: <font color="red">(*)</font></td>
                              <td>
                                <?= $form->field($model, 'email')->textInput(['placeholder' => 'например jet-s@bk.ru']) ?></td>
                           </tr>

                           <tr>
                              <td>Пароль: <font color="red">(*)</font></td>
                              <td>
                                <?= $form->field($model, 'pass')->passwordInput(['placeholder' => 'пароль 6-20 знаков']) ?></td>
                           </tr>
                           <tr>
                              <td>Повторите пароль: <font color="red">(*)</font></td>
                              <td>
                                <?= $form->field($model, 'pass2')->passwordInput(['placeholder' => 'пароль 6-20 знаков']) ?></td>
                           </tr>
                           <tr>
                              <td> Ваше имя и фамилия: <br/>
                              <font color="green"><b>(необязательное)</b></font></td>
                              <td>
                                <?= $form->field($model, 'fio')->textInput(['placeholder' => 'например Иван Иванов']) ?></td>
                           </tr>

                           <tr>
                              <td>Дополнительная информация для связи: <br/>
                              <font color="green"><b>(необязательное)</b></font></td>
                              <td>
                                <?= $form->field($model, 'info')->textarea([
                                    'cols' => 60,
                                    'rows' => 5,
                                    'placeholder' => 'Пожалуйста напишите дополнительные контактные данные, например ICQ, почту, Skype, или номер телефона.'
                                ]) ?></td>
                           </tr>
                           <tr>
                              <td>Решите пример <font color="red">(*)</font>:</td>
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
                              <td><font color="red">(*)</font> - обязательные поля для заполнения</td>
                           </tr>
                           <tr>
                              <td></td>
                              <td>
                                <?= Html::submitButton('Зарегистрироваться в системе', ['class' => 'fbutton2']) ?></td>
                           </tr>
                     </table>
                     <?php ActiveForm::end(); ?>
                     <div class="info"></div>
                     <p style="margin-left:15px; font-size:100%;">После регистрации Вам необходимо будет подтвердить свой аккаунт пополнением баланса в системе<br></p>
                     <div class="info"></div>
                     <p style="margin-left:15px; font-size:100%;">Вводите сложные пароли, для избежания их легкого угадывания злоумышленниками.<br></p>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
