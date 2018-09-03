<?php

use app\widgets\Alert;
use yii\helpers\Html;
use yii\widgets\ListView;
use yii\widgets\ActiveForm;

$this->title = 'Тикет система';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="contentblockall">
   <div class="contentblock">
      <div class="contentblockblock">
         <div class="contentblocktableblock">
            <div class="contentblocktable">
               <div class="contentblocktablebgtitle">
                  <div class="contentblocktabletitle">
                     <h1>Тикеты</h1>
                  </div>
               </div>
               <div class="contentblocktablebgcontblock">
                  <div class="contentblocktablebgcont">
                    <?= Alert::widget() ?>
                     <div class="erorconttext"></div>

                     <?= ListView::widget([
                        'dataProvider' => $dataProvider,
                        'itemView' => '_views',
                        'layout'=>'{items}',
                        'emptyText'=> '',
                        'options'=>[
                          'style'=>'margin-bottom:35px; float: left; width:100%;'
                        ],
                        'itemOptions'=>[
                          'class'=>'tiketblock',
                        ],
                     ]);
                     ?>

                     <div class="tiketblockcontform">
                        <p>Если у Вас появились вопросы, которые требуют разъяснения, пожалуйста свяжитесь со службой поддержки через тикет систему</p>
                        <p>Перед созданием тикета ознакомьтесь с <a href="/faq">FAQ</a>, большинство ответов Вы найдете там.</p>
                        <table border=0>
                          <?php $form = ActiveForm::begin([
                             'class' => 'application',
                             'fieldConfig' => [
                                 'template' => "{input}",
                             ],
                          ]); ?>
                              <p>Выберите тему сообщения</p>
                              <tr>
                                 <td>
                                    <p>Или введите свою</p>

                                    <br>Тема: <?= $form->field($model, 'title')->textInput(['class'=>'validate tikets_input', 'size'=>40]) ?>
                                 </td>
                              </tr>
                              <tr>
                                 <span title="Выберите данную тему если у Вас возникли пробелемы с оплатой, в работе системы или другие" style="text-decoration: underline;cursor: help;padding-left:10px;" onclick="$('input.validate').eq(0).val('Проблема');">Проблема</span>
                                 <span title="Выберите данную тему если у Вас какой-либо вопрос не описанный в разделе FAQ" style="text-decoration: underline;cursor: help;padding-left:10px;" onclick="$('input.validate').eq(0).val('Вопрос');">Вопрос</span>
                                 <span title="Прочие вопросы" style="text-decoration: underline;cursor: help;padding-left:10px;" onclick="$('input.validate').eq(0).val('Прочее');">Прочее</span>
                                 <span title="Вопросы по правильной настройке ID, расписания, запуска накрутки, пополнению баланса" style="text-decoration: underline;cursor: help;padding-left:10px;" onclick="$('input.validate').eq(0).val('Вопрос по настройкам');">Вопрос по настройкам</span>
                                 <td>
                                   <?= $form->field($model, 'message')->textarea(['rows' => 5, 'cols'=>80, 'class'=>'validate tikets_textarea']) ?></td>
                              </tr>
                              <tr>
                                 <td>
                                    <?= Html::submitButton('Создать тикет', ['class' => 'fbutton2']) ?>
                                 </td>
                              </tr>


                           <?php ActiveForm::end(); ?>
                     </div>
                     </table>
                     <br />
                     <?
                     if( Yii::$app->controller->action->id == 'archive' ){
                        echo Html::a('Вернуться к актуальным тикетам', ['index']);
                     }else{
                       echo Html::a('Показать весь архив Ваших тикетов', ['archive']);
                     }
                     ?>
                     <br /><br />
                     <div class="info"></div>
                     <p style="margin-left:15px; font-size:100%;">Тикеты обрабатываются в течении 48ч с момента поступления, дождитесь ответа перед подачей нового тикета.<br></p>
                     <div class="info"></div>
                     <p style="margin-left:15px; font-size:100%;">При отправке сообщения убедитесь в корректности вопроса,
                        администрация не отвечает на сообщения содержащие мат, брань, ругательства и угрозы, будьте вежливы.<br>
                     </p>
                     <div class="info"></div>
                     <p style="margin-left:15px; font-size:100%;">Текст сообщения должен быть на русском языке, сообщения на других языках ответа не получат<br></p>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
