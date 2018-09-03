<?php
/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\widgets\LinkPager;

$this->title = 'Транзакции';
$this->params['breadcrumbs'][] = $this->title;
?>

<meta name='keywords' content='История платежей, платежи, пополнения баланса'/>
<meta name='description' content='История платежей аккаунта в системе Go-Ip.ru'/>


<title>История платежей</title>

<div class="contentblockall">
   <div class="contentblock">
      <div class="contentblockblock">
         <div class="contentblocktableblock">
            <div class="contentblocktable">
               <div class="contentblocktablebgtitle">
                  <div class="contentblocktabletitle">
                     <h1>История платежей</h1>
                  </div>
               </div>
               <div class="contentblocktablebgcontblock">
                  <div class="contentblocktablebgcont2">

<div style="width:100%; overflow: auto">
     <table border="1" bordercolor="#e0e0e0"  width="100%" class="tablepay">
     <thead>
     <tr>
     <th width="10%">Номер счета</th>
	 <th width="10%">Сумма</th>
	 <th width="8%">Время операции</th>
	 <th width="12%">Платежная система</th>
	 <th width="18%">Операция</th>
     <th width="7%">Статус</th>
     <th width="35%">Примечание</th>
     </tr>
     </thead>

     <tbody>


  <?php

      foreach ($posts as $data) {

         $payment = $data->payment;
         $cost = round($data->cost, 2);
         $metod = $data->metod;

         if ($data->status == 'OK-PAY'){
             $status = "<font color='green'>Оплачен</font>";
         }
         else {
           $status = "<font color='red'>Ожидает</font>";
         }

         $note = $data->note;
         if( empty($note) ) $note = 'нет информации';

       echo "<tr>";
           echo "<td> # $payment </td>";
	   echo "<td> $cost ".$data->currency."</td>";
	   echo "<td> {$data->time} </td>";
	   echo "<td> $metod  </td>";
	   echo "<td> ".$data->typeText."  </td>";
           echo "<td> $status </td>";
           echo "<td> {$note} </td>";
       echo "</tr>";
     }
   ?>

    </tbody>
    </table>
    </div>
    <?php if (!$posts) {
               echo "<br/><center>Операций не было, они появятся после пополнения баланса!</center>";}
     ?>
     <br/>
      <?= LinkPager::widget(['pagination' => $pages, 'options' => ['class' => 'pagination'], 'activePageCssClass' => ['class' => 'current']]); ?>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
