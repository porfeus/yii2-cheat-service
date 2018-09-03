<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use app\widgets\Alert;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Создание настроек';
?>

<div class="contentblockall">
   <div class="contentblock">
      <div class="contentblockblock">
         <div class="contentblocktableblock">
            <div class="contentblocktable">
               <div class="contentblocktablebgtitle">
                  <div class="contentblocktabletitle">
                     <h1><?=$templateModel->name?></h1>
                  </div>
               </div>
               <div class="contentblocktablebgcontblock">
                  <div class="contentblocktablebgcont">
                    <?= Alert::widget() ?>
<h2>Ничего не понимаете? Смотрите видео на странице <a target="_blank" href="/faq">FAQ</a></h2>

<?=$limitHtml?>

<?php if(isset($errors)) : ?>

 <ul>

 <?php foreach($errors as $er):?>

  <li class="red"><?=$er;?></li>

 <?php endforeach;?>

 </ul>

<?php endif;?>

<?=isset($success) ? $success : '';?>


<?= Html::beginForm('', 'post', ['id'=>'robotForm']); ?>
<?php if($rpay=='1' || true):?>
<?php if (\Yii::$app->controller->module->id == 'admin') { echo  "ID пользователя <input type='number' name='ID' value='".$user_id."' ><br/>";}?>
Кол-во просмотров страниц сайта после загрузки
<input type="number" min="1" max="20" name="prosmotr" value="2" required placeholder="1"/> шт
<a class="tooltip" href="#">[?]<span class="custom help"><img src="/images/faq/Help.png" alt="Помощь" height="48" width="48" /><em>Подсказка</em>Кол-во просмотров страниц после первоначального входа на сайт. Каждый просмотр занимает порядка 25 секунд!</span></a> <br />


Таймаут прогрузки загрузки страницы при первом заходе от
<input type="number" min="10" max="100" name="time" value="15" required placeholder="15"/> сек
<a class="tooltip" href="#">[?]<span class="custom help"><img src="/images/faq/Help.png" alt="Помощь" height="48" width="48" /><em>Подсказка</em>Таймаут прогрузки страницы является ограничением при ожидании загрузки страницы. Например если время = 20 сек, то только в течении этого времени робот будет ожидать полной загрузки сайта. Первая прогрузка сайта, напрямую влияет на учет посетителей счетчиками.</span></a> <br />


Адрес страницы для входа
<input type="text" name="urls" size="50" required placeholder="http://site.com"/>
<a class="tooltip" href="#">[?]<span class="custom help"><img src="/images/faq/Help.png" alt="Помощь" height="48" width="48" /><em>Подсказка</em>Адрес страницы для входа, это адрес куда будут выполняться заходы (визиты). Адрес страницы может быть любым и начаться либо с http, либо https</span></a> <br />


Маска ссылки для переходов по сайту
<input type="text" name="linkmask" size="50" required value="http" placeholder="http"/>
<a class="tooltip" href="#">[?]<span class="custom help"><img src="/images/faq/Help.png" alt="Помощь" height="48" width="48" /><em>Подсказка</em>Введите адрес маски сайта для переходов, по умолчанию используется http, т.к. это присутствует в любой ссылке. По вашему желанию вы можете его изменить.</span></a> <br />


Время на прогрузку страницы между переходами
<input type="number" min="10" max="100" name="loadtime" value="20" required placeholder="20"/> сек
<a class="tooltip" href="#">[?]<span class="custom help"><img src="/images/faq/Help.png" alt="Помощь" height="48" width="48" /><em>Подсказка</em>Время прогрузки страницы используется для ожидания между переходами. По умолчанию достаточно 20 сек. По вашему усмотрению время можно увеличить или уменьшить.</span></a> <br />



<label for="referer">Источники переходов (referer)</label>
<a class="tooltip" href="#">[?]<span class="custom help"><img src="/images/faq/Help.png" alt="Помощь" height="48" width="48" /><em>Подсказка</em>Referer - это источник перехода. Например если задать ссылки из вконтакте - они будут отражаться в качестве источника посетителей.</span></a>
<br />
<textarea type="text" class="area-1 resize-vertical" cols="100" rows="10" name="referer" id="referer" placeholder="Ссылки для входа, например http://site.com, каждая с новой строки" required/>
http://vk.com
http://ya.ru
http://jet-s.ru
</textarea><br />
<ol>
<li>Использовать ссылки с http:// или https://</li>
<li>Каждая ссылка с новой строки, запрещено использовать ковычки</li>
<li>Максимум 500 ссылок</li>
</ol>
<br />

<center><button class="new-2" type="submit" name="generate"/>Сгенерировать настройку</button></center>

<?= Html::endForm(); ?>
<?php elseif($rpay!='1' && false):?>
<p>Для того чтобы создать настройку, Вы должны активировать свой аккаунт<br/>
Активация производится 1 раз - для этого просто <a href="/balans">пополните баланс</a> на любую сумму в нашей системе.</p>
<?php endif;?>

<?php

echo "
              </div>
            </div>
         </div>
      </div>
   </div>
</div>
";
