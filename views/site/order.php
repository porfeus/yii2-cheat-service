<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use app\widgets\Alert;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use app\models\Conf;

$this->title = 'Создание настройки';
$this->params['breadcrumbs'][] = $this->title;

$script = <<< JS
  function more_file() {
      var file = jQuery('<input/>').attr("type", "file").attr("name", "OrderForm[filesList][]");
      jQuery("#otherfiles").append('<div>'+file[0].outerHTML+' <a href="javascript:void(0);" onclick="del_file(this);">Удалить</a></div>');
      checkMaxFileinputs();
  }

  function checkMaxFileinputs(){
    jQuery('#add_file').toggle( jQuery('[type="file"]').length < 10 );
  }

  function del_file(el) {
      jQuery(el).closest("div").remove();
      checkMaxFileinputs();
  }
JS;
$this->registerJs($script, yii\web\View::POS_BEGIN);

$this->registerCss("
.form-group, .form-group *{
  display: inline;
}
");
?>


<div class="contentblockall">
   <div class="contentblock">
      <div class="contentblockblock">
         <div class="contentblocktableblock">
            <div class="contentblocktable">
               <div class="contentblocktablebgtitle">
                  <div class="contentblocktabletitle">
                     <h1>Создать новую настройку</h1>
                  </div>
               </div>
               <div class="contentblocktablebgcontblock">
                  <div class="contentblocktablebgcont">
                      <?= Alert::widget() ?>


                      <h2>Бесплатное создание шаблона</h2>

                      <p>В системе Вам доступно бесплатное создание настройки самостоятельно, без обращения в техническую поддержку.<br/>
                      Создание доступно только по определенному виду шаблонов (наиболее популярных у 90% пользователей)<br/>
                      Шаблоны в данном разделе будут постоянно добавляться</p>
                        <input type="submit" class="new-3" onClick="window.location='/add-id'" value="Создать настройку бесплатно ">
                      <hr>


                     <h2>Платное создание шаблона</h2>

                     <p>Платное создание настройки осуществляется технической поддержкой сайта по индивидуальному техническому заданию<br/>
                       Техническое задание предоставляет пользователь в произвольной форме<br/></p>

                     <p style="margin-left:15px; font-size:16px;"><a href="tz/tex-zadanie-statistika.zip">Пример технического задания для статистики (я.метрика, li.ru и др)</a><br></p>

                     <p style="margin-left:15px; font-size:16px;"><a href="tz/tex-zadanie-golosovniya.zip">Пример технического задания для накрутки голосований</a><br></p>
                     <?php $form = ActiveForm::begin([
                       'options' => ['enctype' => 'multipart/form-data'],
                       'enableClientValidation' => false,
                       'fieldConfig' => [
                           'template' => "{input}",
                       ],
                     ]); ?>
                        <center>
                           <table>
                              <tbody>
                                 <tr>
                                    <td>
                                       <p>Информация для связи</p>
                                    </td>
                                    <td>
                                      <?= $form->field($model, 'info')->textInput(['class'=>'width_st_mob', 'size'=>40, 'placeholder' => 'E-mail, Skype, ICQ', 'required' => true]) ?>
                                    </td>
                                 </tr>
                                 <tr>
                                    <td>
                                       <p>Прикрепить файлы:</p>
                                       <br />
                                       <span style="font-size:16px;">jpg, png, zip, rar, doc, docx, xls, xlsx, pdf, txt</span><br />
                                       <span style="font-size:16px;">другие форматы не будут прикреплены</span>
                                    </td>
                                    <td>
                                       <input type="file" name="OrderForm[filesList][]"/> <a href="javascript:void(0);" onclick="more_file();" id="add_file">Добавить еще</a>
                                       <div id="otherfiles"></div>
                                    </td>
                                 </tr>
                                 <tr>
                                    <td>
                                       <p>Дополнительная информация:</p>
                                    </td>
                                    <td>
                                      <?= $form->field($model, 'message')->textarea(['required'=>true, 'style'=>'width: 720px; height: 160px; margin: 0px;', 'placeholder' => 'Опишите здесь что вы хотите реализовать в настройке, например клики или что-то другое, можно указывать ссылки.']) ?>
                                    </td>
                                 </tr>
                              </tbody>
                           </table>
                           <input type="submit" value="Заказать платное создание настройки" name="dosend" onclick="if(!confirm('С вашего баланса будет списано <?=Conf::getParams('order_price')?> рублей, за запрос на создание настройки. \nСумма войдет в счет оплаты работы. Списать средства?')) return false;" />
                        </center>
                     <?php ActiveForm::end() ?>
                     <hr>
                     <div class="info"></div>
                     <p style="margin-left:15px; font-size:100%;">Стоимость создания/изменения шаблона через тех.поддержку составляет от 500р/штука </p>
                     <div class="info"></div>
                     <p style="margin-left:15px; font-size:100%;">Список объектов, которые поддаются накрутке опубликован  <a href="/partner_list">здесь</a></p>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
