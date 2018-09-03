<?php

/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\widgets\LinkPager;

$this->title = 'Новости';
$this->params['breadcrumbs'][] = $this->title;

?>

<meta name='keywords' content='новости сайта, новости, news'/>
<meta name='description' content='На данной странице отображаются новости проекта и различные акции'/>


<div class="contentblockall">
   <div class="contentblock">
      <div class="contentblockblock">
         <div class="contentblocktableblock">
            <div class="contentblocktable">
               <div class="contentblocktablebgtitle">
                  <div class="contentblocktabletitle">
                     <h1>Новости</h1>
                  </div>
               </div>
               <div class="contentblocktablebgcontblock">
                  <div class="contentblocktablebgcont">
                     <div class="erorconttext"></div>
                     
                     
                         <?= LinkPager::widget(['pagination' => $pages, 
                             'options' => ['class' => 'pagination'], 'activePageCssClass' => ['class' => 'current']]); ?>
                       
                     
                     <?php foreach ($posts as $item): ?>
                      
                    <div class="newsblock">
                      <div class="newsblockdata">
                        <div class="newsblockdatacont">
                        <?=$item['data'];?>
                        </div>
                      </div>
                      <div class="newsblocktext">
                        <div class="newsblocktextcont">
                        <?=stripslashes(htmlspecialchars_decode($item['news']));?>
                        </div>
                      </div>
                    </div>
                       
                     <?php endforeach;?>
                     
             
                         <?= LinkPager::widget(['pagination' => $pages, 
                             'options' => ['class' => 'pagination'], 'activePageCssClass' => ['class' => 'current']]); ?>
    
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>

                       
   
                