<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use app\widgets\Alert;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

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
h2{
  visibility: hidden;
  margin-bottom: -10px;
  line-height: 0;
}
");

$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>


<?=str_replace(array(
  '/robot/edit/'
), array(
  '/admin/users/robot-edit?id='
),
$html)?>
