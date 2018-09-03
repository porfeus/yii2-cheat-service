<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use app\widgets\Alert;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Создание настроек';

$this->registerJsFile('/js/robot-edit.js');
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



<?=$limitHtml??''?>
<?=$codertonHtml??''?>


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
<p>
<?php if (\Yii::$app->controller->module->id == 'admin') { echo  "ID пользователя <input type='number' name='ID' value='".$user_id."' ><br/>";}?>

Таймаут прогрузки загрузки страницы при первом заходе от
<input type="number" min="10" max="100" name="time1" value="25" required placeholder="25"/> и до:
<input type="number" min="10" max="100" name="time2" value="30" required placeholder="30"/> секунд
<a class="tooltip" href="#">[?]<span class="custom help"><img src="/images/faq/Help.png" alt="Помощь" height="48" width="48" /><em>Подсказка</em>Таймаут прогрузки страницы является ограничением при ожидании загрузки страницы. Например если время = 20 сек, то только в течении этого времени робот будет ожидать полной загрузки сайта, после - он приступит к выполнению следующих действий. Оптимально выставлять 20 - 25 сек и больше! Первая прогрузка сайта, напрямую влияет на учет посетителей счетчиками.</span></a>
 <br />

Таймаут ввода ключевого слова в поисковик
<input type="number" min="1" max="100" name="time3" value="20" required placeholder="20"/> секунд

<a class="tooltip" href="#">[?]<span class="custom help"><img src="/images/faq/Help.png" alt="Помощь" height="48" width="48" /><em>Подсказка</em>Таймаут ввода ключевого слова в строку поиска. Вляет на то, сколько времени будет выделено роботу на ввод слова. Слова вводятся достаточно медленно, имитируя ввода человека. Рекомендуется выставлять порядка 15-20 сек. Если у вас очень длинные слова, параметр можно сделать больше.</span></a> <br />


Таймаут клика и открытия результов поиска
<input type="number" min="1" max="100" name="time4" value="25" required placeholder="20"/> секунд

<a class="tooltip" href="#">[?]<span class="custom help"><img src="/images/faq/Help.png" alt="Помощь" height="48" width="48" /><em>Подсказка</em>Таймаут клика по и открытие результатов поиска, это время выделяемое роботу на клик по кнопке поиска и просмотр результата выдачи поисковика на странице. Оптимально выставлять 20 - 25 сек и больше!</span></a> <br />




<b>Загрузка изображений сайта</b>:
<input name="img" type="radio" id="img-1" required value="0"><label for="img-1"> отключить</label>
<input name="img" type="radio" id="img-2" value="1" checked><label for="img-2"> включить</label>

<a class="tooltip" href="#">[?]<span class="custom help"><img src="/images/faq/Help.png" alt="Помощь" height="48" width="48" /><em>Подсказка</em>Изображения которые находятся в CSS стилях и любые картинки будут отключены. Данная опция ускоряет загрузку сайта примерно на 30-50% (в зависимости от кол-ва картинок). Рекомендуется выключать!</span></a> <br />


<b>Очистка cookies при загрузке</b>:
<input name="cookies" type="radio" id="cookies-1" required value="0"><label for="cookies-1"> не чистить</label>
<input name="cookies" type="radio" id="cookies-2" value="1" checked><label for="cookies-2"> очищать</label>

<a class="tooltip" href="#">[?]<span class="custom help"><img src="/images/faq/Help.png" alt="Помощь" height="48" width="48" /><em>Подсказка</em>Очистка cookies позволяет увеличить число просмотров на счетчиках, т.к очищается вся история поиска перед заходом на сайт. При очистке cookies вход на сайт осуществляется всегда, как в "в первый раз"</span></a> <br />

<b>Включить эмулятор JS/Flesh</b>:
<input name="js" type="radio" id="js-1" required  value="0"><label for="js-1"> отключить</label>
<input name="js" type="radio" id="js-2" value="1" checked><label for="js-2"> включить</label>

<a class="tooltip" href="#">[?]<span class="custom critical"><img src="/images/faq/Critical.png" alt="Ошибка" height="48" width="48" /><em>Важное сообщение</em>Данная опция может полностью запретить показ Вашего сайта, отключать только в крайних случаях! При работе настройки используются сторонние JavaScript  - их отключение приведет к отключению этих функций! Также "отключение" этой функции приведет к тому, что все известные счетчики перестанут считать наш трафик - т.к. сами счетчики не загрузятся! Мы рекомендуем ее включать!</span></a> <br />



<b>Управление Content Security Policy (CSP)</b>:
<input name="csp" type="radio" id="csp-1" required  value="0" checked><label for="csp-1"> отключить</label>
<input name="csp" type="radio" id="csp-2"  value="1"><label for="csp-2"> включить</label>
<a class="tooltip" href="#">[?]<span class="custom critical"><img src="/images/faq/Critical.png" alt="Ошибка" height="48" width="48" /><em>Важное сообщение</em>Данная настройка включает или выключает Content Security Policy. Можно применять, например в случае проблем со вставкой некоторых команд на странице (античитов). Если к примеру, на странице имеется античит или ловушка для робота, то отключение данной опции поможет их просто выключить. Если вы хотите чтобы робот заходил на сайт под обычным браузером и не отключал данную опцию - поставьте в положение включено. По умолчанию данная опция выключена</span></a> <br />



<b>Имитация реальной мышки</b>:
<input name="mouse" type="radio" id="mouse-1" required  value="0" checked><label for="mouse-1"> отключить</label>
<input name="mouse" type="radio" id="mouse-2"  value="1"><label for="mouse-2"> включить</label>
<a class="tooltip" href="#">[?]<span class="custom critical"><img src="/images/faq/Critical.png" alt="Ошибка" height="48" width="48" /><em>Важное сообщение</em>Включение реальной мышки - полностью перехватывает все системные нажатия клавиш, включая клавиатуру, при активации данного поля необходимо также включить данную настройку и в параметрах настройки "редактировать у нужной настройки" => "Разрешить использование реальной мышки", при ошибочном включении показы могут вообще не засчитываться!</span></a> <br />
<font color="red">Внимательно ознакомьтесь с данным пунктом, его включение влияет на настройку</font><br />
</p>

<label for="codert">Кликать в ТОП - 10 найденный сайт</label><br />
<input type="radio" name="coderton" id="coderton" value="1"  checked> -
<label for="coderton">Использовать команду клика</label>

<a class="tooltip" href="#">[?]<span class="custom help"><img src="/images/faq/Help.png" alt="Помощь" height="48" width="48" /><em>Подсказка</em>Уникальный код - используется для выполнения определенной задачи. Он срабатывает через 10-15 секунд после загрузки сайта по вышеуказанному таймауту. Подробную инструкцию по настройке можно прочитать по ссылке ниже! </span></a> <br />

<input type="radio" name="coderton" id="codertoff" value="0"> -
<label for="codertoff">НЕ использовать</label><br />

<textarea type="text" cols="130" rows="10" name="codert" class="codert area-1 resize-vertical" id="codert" placeholder="код сервис"/>{realclick}a;link;http://site.com/;click{/realclick}</textarea>
<br /><a target="_blank" href="/faq-code" title="открыть инструкцию в новой вкладке">подробная инструкция по уникальному коду</a>
<br />
Команда: <font color=red>{realclick}a;link;http://site.com/;click{/realclick}</font>   кликнет по сайту http://site.com/ в выдаче топ-10<br /> <br />  <br />



<p><b>Процент использования браузеров (всего 100%)</b>
<a class="tooltip" href="#">[?]<span class="custom help"><img src="/images/faq/Help.png" alt="Помощь" height="48" width="48" /><em>Подсказка</em>Вы можете задать распределение браузеров с точностью до 1%, которые будут использоваться. Данная опция помогает увеличить или уменьшить в статистике кол-во определенных посетителей с нужных устройств. При определении нужного браузера используется равновероятный генератор случайных чисел. Для того чтобы заполнить поля случайным образом нажмите кнопку "заполнить"</span></a>
</p>
<p>
<div class="search2">
<input class="good" type="number" min="0" max="100" name="brouser[0]" id="brouser" value="5"> % <label for="brouser-1">Internet Explorer</label><br />
<input class="good" type="number" min="0" max="100" name="brouser[1]" id="brouser" value="30"> % <label for="brouser-2">FireFox</label><br />
<input class="good" type="number" min="0" max="100" name="brouser[2]" id="brouser" value="20"> % <label for="brouser-3">Google Chrome</label><br />
<input class="good" type="number" min="0" max="100" name="brouser[3]" id="brouser" value="30"> % <label for="brouser-4">Opera</label><br />
<input class="good" type="number" min="0" max="100" name="brouser[4]" id="brouser" value="5"> % <label for="brouser-5">Safari</label><br />
<input class="good" type="number" min="0" max="100" name="brouser[5]" id="brouser" value="3"> % <label for="brouser-6">Mobile</label><br />
<input class="good" type="number" min="0" max="100" name="brouser[6]" id="brouser" value="7"> % <label for="brouser-7">Прочие браузеры</label><br />
<span class="warning2">Сумма должна = 100!</span>
</div>
</p>
<input onclick="rnd()" value="заполнить &#9998;" type="button" title="Автоматически подобрать нужные значения"><br /> <br />
<br />
<div id="block1">
<div id="bl">
<font color=red>Я</font>ндекс запросы <input type="number" id="procent-1" name="procent-1" value="50" min="0" max="100" step="1"> % от общего кол-ва (от 0 до 100)

<a class="tooltip" href="#">[?]<span class="custom help"><img src="/images/faq/Help.png" alt="Помощь" height="48" width="48" /><em>Подсказка</em>Яндекс запросы - это список запросов, которые будут введены в поисковую форму системы Яндекс, и будут использованы для накрутки поисковых подсказок именно в этой системе. Вход осуществляется на адрес https://www.yandex.ru</span></a>
<br />
<textarea type="text" cols="70" class="area-2 resize-vertical" rows="16" name="zaprospoisk" id="zaprospoisk"  placeholder="Поисковые запросы Яндекс"/>
запрос №1
запрос №2
запрос №3
запрос №4
запрос №5
</textarea>
<p>
<ol>
<li>Желательно использовать запросы, которые являются НЧ</li>
<li>Вдумчиво подбирайте запросы для продвижения (желательно из ТОП-10)</li>
<li>Каждый запрос с новой строки, запрещено использовать ковычки!</li>
<li>Не более 500 запросов</li>
</ol>
</p></div></div>

<div id="block1">
<div id="bl">
<font color=blue>G</font>oogle запросы<input type="number" name="procent-2" id="procent-2" value="50" min="0" max="100" step="1"> от общего кол-ва % (от 0 до 100)
<a class="tooltip" href="#">[?]<span class="custom help"><img src="/images/faq/Help.png" alt="Помощь" height="48" width="48" /><em>Подсказка</em>Google запросы - это список запросов, которые будут введены в поисковую форму системы Google, и будут использованы для накрутки поисковых подсказок именно в этой системе. Вход осуществляется на адрес https://www.google.ru</span></a>
<br />
<textarea type="text" class="area-4 resize-vertical" cols="70" rows="16" name="zaprospoisk2" id="zaprospoisk2" placeholder="Поисковые запросы Google"/>
запрос №1
запрос №2
запрос №3
запрос №4
запрос №5
</textarea>
<p>
<ol>
<li>Желательно использовать запросы, которые являются НЧ</li>
<li>Вдумчиво подбирайте запросы для продвижения (желательно из ТОП-10)</li>
<li>Каждый запрос с новой строки, запрещено использовать ковычки!</li>
<li>Не более 500 запросов</li>
</ol>
</p>
</div></div>

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
