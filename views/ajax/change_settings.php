<?php

use app\widgets\Alert;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use app\models\Conf;
?>

<center>
    <?php $form = ActiveForm::begin([
      'action' => ['site/change-settings'],
      'options' => ['enctype' => 'multipart/form-data', 'id' => 'change_settings'],
      'enableClientValidation' => false,
      'fieldConfig' => [
          'template' => "{input}",
      ],
    ]); ?>
      <input type="hidden" name="siteid" value="<?=Html::encode($id)?>" />
      <p>
        Вы можете заказать платную помощь в редактировании шаблона. Сообщите, что Вы хотите изменить и техническая поддержка сделает это за Вас, заказывать помощь можно 1 раз в <?php
        if( Conf::getParams('set_pause') >= 1 ){
          echo Conf::getParams('set_pause').' ч';
        }else{
          echo ceil(Conf::getParams('set_pause') * 60).' мин';
        }
        ?><?php if( Conf::getParams('change_settings_price') > 0 )echo ', стоимость запроса '.Conf::getParams('change_settings_price').'р. Платный запрос обрабатывается быстрее, чем тикеты. Если изменения внесены не будут, средства вернутся на баланс.'; ?>
      </p><br />
         <textarea name="ChangeSettingsForm[message]" id="change_settings_message" style="width:500px; height:200px;border-radius:6px;" placeholder="Напишите здесь, что конкретно Вы хотите изменить в технической части настроек"></textarea><br />
         <p>При необходимости добавьте файлы:<br />
         jpg, png, zip, rar, doc, docx, xls, pdf, txt<br /><br />
         <input type="file" name="ChangeSettingsForm[filesList][]" /><br /><br />
         После отправки запроса на изменение, у Вас появится тикет, <br />при необходимости дополните информацию в нем<br /><br />
		 </p>
         <input class="new-2" type="submit" name="change_settings" value="Отправить"<?php if( Conf::getParams('change_settings_price') > 0 )echo ' onclick="if(!confirm(\'С вашего баланса будет списано '.Conf::getParams('change_settings_price').' руб, за платный запрос помощи\')) return false;"'; ?> />
   <?php ActiveForm::end() ?>
</center>
