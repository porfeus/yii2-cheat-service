<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use app\widgets\Alert;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
?>


<?php if($status_edit=="true"):?>

<?php if(isset($errors)) : ?>

 <ul>

 <?php foreach($errors as $er):?>

  <li class="red"><?=$er;?></li>

 <?php endforeach;?>

 </ul>

<?php endif;?>

<?=isset($success) ? $success : '';?>
<?= Html::beginForm('', 'post', ['id'=>'robotForm']); ?>
Кол-во просмотров страниц сайта после загрузки
<input type="number" min="1" max="20" name="prosmotr" value="<?php echo ($out_prosmotr[1]??'');?>" required placeholder="1"/> шт
<a class="tooltip" href="#">[?]<span class="custom help"><img src="/images/faq/Help.png" alt="Помощь" height="48" width="48" /><em>Подсказка</em>Кол-во просмотров страниц после первоначального входа на сайт. Каждый просмотр занимает порядка 25 секунд, просмотры выполняются один за одним на открывшемся сайте.</span></a> <br />


Таймаут прогрузки загрузки страницы при первом заходе от
<input type="number" min="1" max="100" name="time" value="<?php echo ($out_time[1]??'');?>" required placeholder="15"/> сек
<a class="tooltip" href="#">[?]<span class="custom help"><img src="/images/faq/Help.png" alt="Помощь" height="48" width="48" /><em>Подсказка</em>Таймаут прогрузки страницы является ограничением при ожидании загрузки страницы. Например если время = 20 сек, то только в течении этого времени робот будет ожидать полной загрузки сайта. Первая прогрузка сайта, напрямую влияет на учет посетителей счетчиками.</span></a> <br />


Адрес страницы для входа
<input type="text" name="urls" size="50" value="<?php echo ($out_urls[1]??'');?>" required placeholder="http://site.com"/>
<a class="tooltip" href="#">[?]<span class="custom help"><img src="/images/faq/Help.png" alt="Помощь" height="48" width="48" /><em>Подсказка</em>Адрес страницы для входа, это адрес куда будут выполняться заходы (визиты). Адрес страницы может быть любым и начаться либо с http, либо https</span></a> <br />


Маска ссылки для переходов по сайту
<input type="text" name="linkmask" size="50" value="<?php echo ($out_linkmask[1]??'');?>" required placeholder="http"/>
<a class="tooltip" href="#">[?]<span class="custom help"><img src="/images/faq/Help.png" alt="Помощь" height="48" width="48" /><em>Подсказка</em>Введите адрес маски сайта для переходов, по умолчанию используется http, т.к. это присутствует в любой ссылке. По вашему желанию вы можете его изменить.</span></a> <br />


Время на прогрузку страницы между переходами
<input type="number" min="1" max="100" name="loadtime" value="<?php echo ($out_loadtime[1]??'');?>" required placeholder="20"/> сек <a class="tooltip" href="#">[?]<span class="custom help"><img src="/images/faq/Help.png" alt="Помощь" height="48" width="48" /><em>Подсказка</em>Время прогрузки страницы используется для ожидания между переходами. По умолчанию достаточно 20 сек. По вашему усмотрению время можно увеличить или уменьшить.</span></a><br />



<label for="referer">Источники переходов (referer)</label>
<a class="tooltip" href="#">[?]<span class="custom help"><img src="/images/faq/Help.png" alt="Помощь" height="48" width="48" /><em>Подсказка</em>Referer - это источник перехода. Например если задать ссылки из вконтакте - они будут отражаться в качестве источника посетителей.</span></a>
<br />
<textarea type="text" class="area-1 resize-vertical" cols="100" rows="10" name="referer" id="referer" placeholder="Ссылки для входа, например http://site.com, каждая с новой строки" required/><?php echo ($out_referer??'');?></textarea><br />
<ol>
<li>Использовать ссылки с http:// или https://</li>
<li>Каждая ссылка с новой строки, запрещено использовать ковычки</li>
<li><b>about:blank</b> - для передачи закладки браузера</li>
<li>Максимум 500 ссылок</li>
</ol>
<br />

<center><button class="new-2" type="submit" name="generate"/>Сохранить параметры</button></center>

<?= Html::endForm(); ?>
<?php endif;?>
