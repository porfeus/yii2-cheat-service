<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use app\widgets\Alert;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

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
                   <h1>Редактирование ID: <?=htmlspecialchars($id)?>, <?=htmlspecialchars($out_types)?></h1>
                  </div>
               </div>
               <div class="contentblocktablebgcontblock">
                  <div class="contentblocktablebgcont">
                    <?= Alert::widget() ?>
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
