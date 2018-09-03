<?php

use app\widgets\Alert;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = "Удаление настройки";
$set_rus = 'настройки';
if( strpos($id, ',') ) $set_rus = 'настроек';
?>


<div class="contentblockall">
   <div class="contentblock">
      <div class="contentblockblock">
         <div class="contentblocktableblock">
            <div class="contentblocktable">
               <div class="contentblocktablebgtitle">
                  <div class="contentblocktabletitle">
                     <h1>Удаление <?=$set_rus?></h1>
                  </div>
               </div>
               <div class="contentblocktablebgcontblock">
                  <div class="contentblocktablebgcont">
                     <?= Alert::widget() ?>

                      <p>Удаление <?=$set_rus?> с id: <?=Html::encode(str_replace(',', ', ', $id))?>
                      <br /><br />
                      После удаления настройки будут потеряны <u>безвозвратно</u>:
                      <ul>
                      <li>шаблон и его параметры </li>
                      <li><b>баланс реалов</b> у настройки</li>
                      <li>статистика выполнения</li>
                      <li>возможность управлять настройкой</li>
                      </ul>
                      Мы рекомендуем перевести все реалы на основной баланс аккаунта, только после этого удалять настройку!<br /><br />
					  При удалении будет проведена проверка и корректировка баланса (остаток реалов будет зачислен на счет)
                      </p>
                      <?= Html::beginForm('', 'post'); ?>
                      <label><input type="checkbox" name="confirm" value="yes">согласен с удалением</label>
                      <input type="submit" name="deleteid" value="Удалить"/>
                      <?= Html::endForm(); ?>
                </div>
             </div>
          </div>
       </div>
    </div>
  </div>
</div>
