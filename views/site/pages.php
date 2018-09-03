<?php

/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\widgets\LinkPager;

$this->title = $model['title'];
$this->registerMetaTag(['name' => 'keywords', 'content' => $model['meta_key']]);
$this->registerMetaTag(['name' => 'description', 'content' => $model['description']]);
$this->params['breadcrumbs'][] = $this->title;

?>
                  
<div class="contentblockall">
   <div class="contentblock">
      <div class="contentblockblock">
         <div class="contentblocktableblock">
            <div class="contentblocktable">
               <div class="contentblocktablebgtitle">
                  <div class="contentblocktabletitle">
                     <h1><?=$model['title'] ?></h1>
                  </div>
               </div>
               <div class="contentblocktablebgcontblock">
                  <div class="contentblocktablebgcont">
                      
                 <?=stripslashes(htmlspecialchars_decode($model['full'] ? $model['full'] : $model['short']))?>
 
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>