<?php

use yii\helpers\Html;
use yii\widgets\ListView;
use yii\bootstrap\ActiveForm;

$this->title = Html::encode($tiket->title);
$this->params['breadcrumbs'][] = ['label' => 'Тикет система', 'url' => ['index']];
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
                     <table width="100%">
                        <tr>
                           <td>

                             <?= ListView::widget([
                                'dataProvider' => $dataProvider,
                                'itemView' => '_messages',
                                'layout'=>'{items}',
                                'options'=>[
                                  'style'=>''
                                ],
                                'itemOptions'=>[
                                  'class'=>'',
                                ],
                             ]);
                             ?>
                              <br>
                              <table>
                                <?php $form = ActiveForm::begin([
                                   'class' => 'application',
                                   'fieldConfig' => [
                                       'template' => "{input}",
                                   ],
                                ]); ?>
                                    <tr>
                                       <td>
                                         <?= $form->field($model, 'message')->textarea(['rows' => 5, 'cols'=>80, 'class'=>'validate']) ?>
                                       </td>
                                    </tr>
                                    <tr>
                                       <td>
                                          <?= Html::submitButton('Отправить сообщение', ['class' => 'fbutton2']) ?>
                                       </td>
                                    </tr>
                                 <?php ActiveForm::end(); ?>
                                 </td>
                                 </tr>
                              </table>
                     </table>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
