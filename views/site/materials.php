<?php

/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\widgets\LinkPager;

$this->title = 'Блог';
$this->params['breadcrumbs'][] = $this->title;
$this->registerMetaTag(['name' => 'keywords', 'content' => 'Раздел блог, блог, статьи']);
$this->registerMetaTag(['name' => 'description', 'content' => 'В данном разделе содержатся записи и статьи, блог поможет решить много вопросов.']);

?>

                  
<div class="contentblockall">
   <div class="contentblock">
      <div class="contentblockblock">
         <div class="contentblocktableblock">
            <div class="contentblocktable">
               <div class="contentblocktablebgtitle">
                  <div class="contentblocktabletitle">
                     <h1>Материалы</h1>
                  </div>
               </div>
               <div class="contentblocktablebgcontblock">
                  <div class="contentblocktablebgcont2">
             
                    <?php foreach ($posts as $item): ?>
                      
                      
                   <h3><a href="/materials/<?php echo $item->url; ?>"><?php echo $item->title; ?></a></h3>
                     
                     <?php echo htmlspecialchars_decode(stripslashes($item->short));?>
                     

                     <hr />
                     <?php endforeach;?>
                     
              <?= LinkPager::widget(['pagination' => $pages, 'options' => ['class' => 'pagination'], 'activePageCssClass' => ['class' => 'current']]); ?>
                    
            
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>