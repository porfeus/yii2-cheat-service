<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use app\widgets\Alert;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use app\models\Conf;

$this->registerCssFile("/css/style.css");
$this->registerCssFile("/css/media.css");
$this->registerCss("
.tooltip {
  display: inline !important;
  opacity: 1 !important;
  z-index: auto !important;
  position: relative !important;
}
html, body, .contentblocktablebgtitle, .contentblock {
  background: none !important;
  font-family: \"Helvetica Neue\", Helvetica, Arial, sans-serif !important;
  color: #333 !important;
}
.contentblockall, .contentblockblock, .contentblocktableblock, .contentblocktable{
  padding-top: 0 !important;
}
label{
  font-weight: normal !important;
  text-indent: 2px !important;
}
a:hover, a:focus {
  color: #23527c !important;
  text-decoration: underline !important;
}
h1{
  font-size: 1.5em !important;
}
");

$this->title = 'Редактирование настройки';
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>


<div class="contentblockall">
   <div class="contentblock">
      <div class="contentblockblock">
         <div class="contentblocktableblock">
            <div class="contentblocktable">
               <div class="contentblocktablebgtitle">
                  <div class="contentblocktabletitle">
                   <a target='_blank' href='<?=Conf::setIdUrl($id_user)?>'>Файл настройки JS</a><br/>
                   <h1>Редактирование настройки ID: <?=htmlspecialchars($id)?>, тип шаблона: <?=htmlspecialchars($out_types)?></h1>
                  </div>
               </div>
               <div class="contentblocktablebgcontblock">
                  <div class="contentblocktablebgcont">
                    <?php if( Yii::$app->controller->module->id != 'admin' ){ ?>
                    <a href='/api'><< ВЕРНУТЬСЯ НАЗАД</a><br/>
                    <?php } ?>
                    <br/>

                    <?=$formHtml??''?>
                    <?=$codertonHtml??''?>
                    <?=$html?>

               </div>
            </div>
         </div>
      </div>
   </div>
</div>
