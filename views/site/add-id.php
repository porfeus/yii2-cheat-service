<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use app\widgets\Alert;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Добавление шаблона';
$this->params['breadcrumbs'][] = $this->title;
?>


<div class="contentblockall">
   <div class="contentblock">
      <div class="contentblockblock">
         <div class="contentblocktableblock">
            <div class="contentblocktable">
               <div class="contentblocktablebgtitle">
                  <div class="contentblocktabletitle">
                     <h1>Создание нового шаблона</h1>
                  </div>
               </div>
               <div class="contentblocktablebgcontblock">
                  <div class="contentblocktablebgcont">
                    <?= Alert::widget() ?>
                     <p>
                        <br>На данной странице Вы можете создать определенного вида шаблон (выбрать из предложенных)<br>
                        После создания настройка будет автоматически добавлена на управление
                     </p>
                  <form method="post" name="add-id">

                 <select name="type">
                   <?php
                   foreach( $models as $model ){
                     echo '<option value="'.$model->url.'">'.$model->name.'</option>';
                   }
                   ?>
                 </select>
                <div id="add_type"></div>
<br/>
<p><b>Примечание:</b> шаблоны оптимизированны под общие, "шаблонные" задачи, которые наиболее четко можно в них реализовать. Некоторые задачи могут быть реализованы в разных шаблонах!</p>
<button class="new" type="submit">Создать настройку</button>
</form>
<script>

$('form[name="add-id"]').on('submit', function(event){
  event.preventDefault();
  location.href = '/robot/add-' + $(this).find('[name="type"]').val();
});

$(document).ready(function(){
	$('[name="type"]').on("change", function() {

    <?php
    foreach( $models as $model ){
echo <<< JS
      if (jQuery(this).val() == $model->url){
    		text = "{$model->value}";
      }
JS;
    }
    ?>

		$("#add_type").html(text);
	});

	$('[name="type"]').trigger("change");
});
</script>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
