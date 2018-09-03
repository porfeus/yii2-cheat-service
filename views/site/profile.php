<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use app\widgets\Alert;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use app\models\Conf;

$this->title = 'Профиль пользователя';
$this->params['breadcrumbs'][] = $this->title;

$script = <<< JS
  jQuery(".myphone").mask("+7 (999) 999-99-99");
JS;
$this->registerJs($script);
$this->registerJsFile('/js/mask.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
?>


<div class="contentblockall">
   <div class="contentblock">
      <div class="contentblockblock">
         <div class="contentblocktableblock">
            <div class="contentblocktable">
               <div class="contentblocktablebgtitle">
                  <div class="contentblocktabletitle">
                     <h1>Профиль</h1>
                  </div>
               </div>
               <div class="contentblocktablebgcontblock">
                  <div class="contentblocktablebgcont">
                     <?= Alert::widget() ?>
                     <?php $form = ActiveForm::begin([
                       'enableClientValidation' => false,
                       'options' => [
                         'class' =>'application'
                       ],
                       'layout' => 'horizontal',
                       'fieldConfig' => [
                           'template' => "{input}",
                           'options' => [
                            'tag' => false,
                        ],
                       ],
                     ]); ?>

                        <table>
                           <p>Регистрационные данные (кошелек и ФИО) изменяются только через администрацию.<br>Изменению подлежат контактные данные и Ваш пароль</p>
                           <tr>
                              <td height="35">
                                 <span class="text-p">Логин: <?=Html::encode(Yii::$app->user->identity->login);?></span>
                              </td>
                              <td height="35"><span class="text-p">Дата регистрации:</span> <?=Html::encode(Yii::$app->user->identity->date);?></td>
                           </tr>
                           <tr>
                              <td height="35"><span class="text-p">Фио:</span></td>
                              <td height="35">
                                <?= Html::textInput('', Yii::$app->user->identity->fio, ['disabled' => true, 'class'=>'validate width_st_mob', 'style'=>'cursor:not-allowed', 'title'=>'Недоступно для редактирования, обратитесь в администрацию', 'size'=>'50']) ?>
                              </td>
                           </tr>
                           <tr>
                              <td><span class="text-p">E-mail:</span></td>
                              <td>
                                <?= $form->field($model, 'email')->textInput(['class'=>'validate width_st_mob', 'size'=>'50']) ?>
                              </td>
                           </tr>
                           <tr>
                              <td><span class="text-p">Доп. информация для связи:</span></td>
                              <td>
                                <?= $form->field($model, 'info')->textarea(['class'=>'validate', 'style'=>'margin: 0px; width: 378px; height: 82px;']) ?>
                              </td>
                           </tr>
                           <tr>
                              <td colspan="2">
                                Уведомлять меня <label for="notifymail">по почте</label>
                                <?= $form->field($model, 'onmail1')->checkbox(['id'=>'notifymail', 'value'=>true, 'template' => '{input}']) ?>
                                <?= $form->field($model, 'mail1')->textInput(['class' => '']) ?>

                                <label for="notifysms">и смс</label>
                                <?= $form->field($model, 'ontel1')->checkbox(['id'=>'notifysms', 'value'=>true, 'template' => '{input}']) ?>
                                <?= $form->field($model, 'tel1')->textInput(['placeholder'=>'+7 (912) 345-67-89', 'class'=>'myphone']) ?>

                                когда общий баланс реалов меньше
                                <?= $form->field($model, 'traf1')->textInput(['style'=>'width: 50px;', 'class' => '']) ?>
                              </td>

                           </tr>
                           <tr>
                              <td colspan="2">
                                Уведомлять меня <label for="notifymail2">по почте</label>
                                <?= $form->field($model, 'onmail2')->checkbox(['id'=>'notifymail2', 'value'=>true, 'template' => '{input}']) ?>
                                <?= $form->field($model, 'mail2')->textInput(['class' => '']) ?>

                                <label for="notifysms2">и смс</label>
                                <?= $form->field($model, 'ontel2')->checkbox(['id'=>'notifysms2', 'value'=>true, 'template' => '{input}']) ?>
                                <?= $form->field($model, 'tel2')->textInput(['placeholder'=>'+7 (912) 345-67-89', 'class'=>'myphone']) ?>

                                когда на одной из настроек реалов находится меньше
                                <?= $form->field($model, 'traf2')->textInput(['style'=>'width: 50px;', 'class' => '']) ?>
                              </td>
                           </tr>
                           <p>Письма отправляются с адреса <b><?=Yii::$app->params['sendFrom']['email']?></b>, добавьте этот адрес в белый список/контакты, чтобы всегда получать письма. </p>

                           <tr>
                              <td colspan="2">
                                 Уведомлять не чаще
                                 <?= $form->field($model, 'limit')->textInput(['style'=>'width: 50px;', 'class' => '']) ?>
                                 раз в сутки
								<br /><br/>
								<font color="red">Уведомления будут приходить не более <?=Conf::getParams('onlineday')?> дней (если в вашем профиле нет активности, если есть - будут приходить всегда). Уведомления запускаются каждый час, максимальное кол-во СМС на 1 номер в сутки - 5 шт, E-mail - без ограничений. Смс уведомления приходят на большинство российских операторов.</font>
                              </td>
                           </tr>
                           <tr>
                              <td colspan="2">
                                <?= Html::submitButton('Сохранить данные', ['name' => 'change_data', 'class' => 'fbutton2']) ?>
                              </td>
                           </tr>

                        </table>
                        <br />
                        <table>
                           <tr>
                              <td height="35"><span class="text-p">Старый пароль:</span></td>
                              <td height="35">
                                <?= $form->field($model, 'pass')->textInput(['class'=>'validate']) ?>
                              </td>
                           </tr>
                           <tr>
                              <td height="35"><span class="text-p">Новый пароль:</span></td>
                              <td height="35">
                                <?= $form->field($model, 'pass1')->textInput(['class'=>'validate']) ?>
                              </td>
                           </tr>
                           <tr>
                              <td><span class="text-p">Повтор нового пароля:</span></td>
                              <td>
                                <?= $form->field($model, 'pass2')->textInput(['class'=>'validate']) ?>
                              </td>
                           </tr>
                           <tr>
                              <td colspan="2">
                                <?= Html::submitButton('Изменить пароль', ['name' => 'change_password', 'class' => 'fbutton2']) ?>
                              </td>
                           </tr>
                        </table>
                     <?php ActiveForm::end(); ?>

                     <hr>
                     <div class="info"></div>

                     <p style="margin-left:15px; font-size:100%;">Уведомления по смс не работают на некоторых операторах<br></p>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
