<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use app\widgets\Alert;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use app\components\Coderton;

$this->registerJsFile('/js/robot-edit.js');
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
<p>
Таймаут прогрузки загрузки страницы при первом заходе от
<input type="number" min="1" max="100" name="time1" value="<?=htmlspecialchars($time1[1]??'');?>" required placeholder="25"/> и до:
<input type="number" min="1" max="100" name="time2" value="<?=htmlspecialchars($time2[1]??'');?>" required placeholder="30"/> секунд
<a class="tooltip" href="#">[?]<span class="custom help"><img src="/images/faq/Help.png" alt="Помощь" height="48" width="48" /><em>Подсказка</em>Таймаут прогрузки страницы является ограничением при ожидании загрузки страницы. Например если время = 20 сек, то только в течении этого времени робот будет ожидать полной загрузки сайта, после - он приступит к выполнению следующих действий. Оптимально выставлять 20 - 25 сек и больше! Первая прогрузка сайта, напрямую влияет на учет посетителей счетчиками.</span></a>

 <br />

<b>Загрузка изображений сайта</b>:
<input name="img" type="radio" id="img-1" required value="0" <?php if (($img[1]??'') != '1') {echo 'checked';}?>>
<label for="img-1"> отключить</label>
<input name="img" type="radio" id="img-2" value="1" <?php if (($img[1]??'') == '1') {echo 'checked';}?>>
<label for="img-2"> включить</label>

<a class="tooltip" href="#">[?]<span class="custom help"><img src="/images/faq/Help.png" alt="Помощь" height="48" width="48" /><em>Подсказка</em>Изображения которые находятся в CSS стилях и любые картинки будут отключены. Данная опция ускоряет загрузку сайта примерно на 30-50% (в зависимости от кол-ва картинок). Рекомендуется выключать!</span></a> <br />


<b>Очистка cookies при загрузке</b>:
<input name="cookies" type="radio" id="cookies-1" required value="0" <?php if (($cookies[1]??'') != '1') {echo 'checked';}?>>
<label for="cookies-1"> не чистить</label>
<input name="cookies" type="radio" id="cookies-2" value="1" <?php if (($cookies[1]??'') == '1') {echo 'checked';}?>>
<label for="cookies-2"> очищать</label>

<a class="tooltip" href="#">[?]<span class="custom help"><img src="/images/faq/Help.png" alt="Помощь" height="48" width="48" /><em>Подсказка</em>Очистка cookies позволяет увеличить число просмотров на счетчиках, т.к очищается вся история поиска перед заходом на сайт. При очистке cookies вход на сайт осуществляется всегда, как в "в первый раз"</span></a> <br />

<b>Включить эмулятор JS/Flesh</b>:
<input name="js" type="radio" id="js-1" required  value="0" <?php if (($js[1]??'') != '1') {echo 'checked';}?>>
<label for="js-1"> отключить</label>
<input name="js" type="radio" id="js-2" value="1" <?php if (($js[1]??'') == '1') {echo 'checked';}?>>
<label for="js-2"> включить</label>

<a class="tooltip" href="#">[?]<span class="custom critical"><img src="/images/faq/Critical.png" alt="Ошибка" height="48" width="48" /><em>Важное сообщение</em>Данная опция может полностью запретить показ Вашего сайта, отключать только в крайних случаях! При работе настройки используются сторонние JavaScript  - их отключение приведет к отключению этих функций! Также "отключение" этой функции приведет к тому, что все известные счетчики перестанут считать наш трафик - т.к. сами счетчики не загрузятся! Мы рекомендуем ее включать!</span></a> <br />




<b>Управление Content Security Policy (CSP)</b>:
<input name="csp" type="radio" id="csp-1" required  value="0" <?php if (($csp[1]??'') != '1') {echo 'checked';}?>>
<label for="csp-1"> отключить</label>
<input name="csp" type="radio" id="csp-2"  value="1" <?php if (($csp[1]??'') == '1') {echo 'checked';}?>>
<label for="csp-2"> включить</label>

<a class="tooltip" href="#">[?]<span class="custom critical"><img src="/images/faq/Critical.png" alt="Ошибка" height="48" width="48" /><em>Важное сообщение</em>Данная настройка включает или выключает Content Security Policy. Можно применять, например в случае проблем со вставкой некоторых команд на странице (античитов). Если к примеру, на странице имеется античит или ловушка для робота, то отключение данной опции поможет их просто выключить. Если вы хотите чтобы робот заходил на сайт под обычным браузером и не отключал данную опцию - поставьте в положение включено. По умолчанию данная опция выключена</span></a> <br />



<b>Имитация реальной мышки</b>:
<input name="mouse" type="radio" id="mouse-1" required  value="0" <?php if (($mouse[1]??'') != '1') {echo 'checked';}?>>
<label for="mouse-1"> отключить</label>
<input name="mouse" type="radio" id="mouse-2"  value="1" <?php if (($mouse[1]??'') == '1') {echo 'checked';}?>>
<label for="mouse-2"> включить</label>

<a class="tooltip" href="#">[?]<span class="custom critical"><img src="/images/faq/Critical.png" alt="Ошибка" height="48" width="48" /><em>Важное сообщение</em>Включение реальной мышки - полностью перехватывает все системные нажатия клавиш, включая клавиатуру, при активации данного поля необходимо также включить данную настройку и в параметрах настройки "редактировать у нужной настройки" => "Разрешить использование реальной мышки", при ошибочном включении показы могут вообще не засчитываться!</span></a> <br />
<font color="red">Внимательно ознакомьтесь с данным пунктом, его включение влияет на настройку</font><br />
</p>

<label for="codert">Уникальный код, после перехода по сайту</label><br />
<input type="radio" name="coderton" id="coderton" value="1" <?php if (($coderton[1]??'') == '1') {echo 'checked';}?>> -
<label for="coderton">использовать вставку</label>

<a class="tooltip" href="#">[?]<span class="custom critical"><img src="/images/faq/Critical.png" alt="Ошибка" height="48" width="48" /><em>Важное сообщение</em>Уникальный код - используется для выполнения определенной задачи. Он срабатывает через 10-15 секунд после загрузки сайта по вышеуказанному таймауту. Подробную инструкцию по настройке можно прочитать по ссылке ниже! </span></a> <br />

<input type="radio" name="coderton" id="codertoff" value="0" <?php if (($coderton[1]??'') != '1') {echo 'checked';}?>> -
<label for="codertoff">НЕ использовать</label><br />

<textarea type="text" cols="130" rows="10" class="codert area-1 resize-vertical" name="codert" id="codert" placeholder="{realclick}a;link;http://site.com/;click{/realclick}"/>
<?php if(!is_array($comment_code[1])){echo Coderton::commentsDecode(htmlspecialchars_decode(($comment_code[1]??'')));} else {echo '';}?>
</textarea>
<br /><a target="_blank" href="/faq-code" title="открыть инструкцию в новой вкладке">подробная инструкция по уникальному коду</a>
<br /><br />



<p><b>Процент использования браузеров (всего 100%)</b>
<a class="tooltip" href="#">[?]<span class="custom help"><img src="/images/faq/Help.png" alt="Помощь" height="48" width="48" /><em>Подсказка</em>Вы можете задать распределение браузеров с точностью до 1%, которые будут использоваться. Данная опция помогает увеличить или уменьшить в статистике кол-во определенных посетителей с нужных устройств. При определении нужного браузера используется равновероятный генератор случайных чисел. Для того чтобы заполнить поля случайным образом нажмите кнопку "заполнить"</span></a>
</p>
<p>
<div class="search2">
<input class="good" type="number" min="0" max="100" name="brouser[0]" id="brouser" value="<?=htmlspecialchars($IE[1]??'');?>"> %
<label for="brouser-1">Internet Explorer</label><br />
<input class="good" type="number" min="0" max="100" name="brouser[1]" id="brouser" value="<?=htmlspecialchars($FF[1]??'');?>"> %
<label for="brouser-2">FireFox</label><br />
<input class="good" type="number" min="0" max="100" name="brouser[2]" id="brouser" value="<?=htmlspecialchars($Chrome[1]??'');?>"> %
<label for="brouser-3">Google Chrome</label><br />
<input class="good" type="number" min="0" max="100" name="brouser[3]" id="brouser" value="<?=htmlspecialchars($Opera[1]??'');?>"> %
<label for="brouser-4">Opera</label><br />
<input class="good" type="number" min="0" max="100" name="brouser[4]" id="brouser" value="<?=htmlspecialchars($Safari[1]??'');?>"> %
<label for="brouser-5">Safari</label><br />
<input class="good" type="number" min="0" max="100" name="brouser[5]" id="brouser" value="<?=htmlspecialchars($Mobile[1]??'');?>"> %
<label for="brouser-6">Mobile</label><br />
<input class="good" type="number" min="0" max="100" name="brouser[6]" id="brouser" value="<?=htmlspecialchars($Other[1]??'');?>"> %
<label for="brouser-7">Прочие браузеры</label><br />
<span class="warning2">Сумма должна = 100!</span>
</div>
</p>
<input onclick="rnd()" value="заполнить &#9998;" type="button" title="Автоматически подобрать нужные значения"><br /> <br />



<label for="urlvhoda">URL точек входа на сайт</label>

<a class="tooltip" href="#">[?]<span class="custom help"><img src="/images/faq/Help.png" alt="Помощь" height="48" width="48" /><em>Подсказка</em>URL точки входа на сайт - это список тех страниц, по которым робот "войдет" на ваш сайт. Для каждого посетителя они выбираются случайно из списка заданных. Разрешено использовать любые ссылки (http и https), максимальное кол-во задаваемых ссылок 500 шт (остальные будут удалены), каждая ссылка задается с новой строки, пустые строки будут удалены!</span></a>

<br />
<textarea type="text" class="area-1 resize-vertical" cols="130" rows="10" name="urlvhoda" id="urlvhoda" placeholder="Ссылки для входа, например http://site.com, каждая с новой строки" required/><?=htmlspecialchars($out_url_vhoda??'');?></textarea><br />
<ol>
<li>Использовать ссылки с http:// или https://</li>
<li>Каждая ссылка с новой строки, запрещено использовать ковычки</li>
<li>Максимум 500 ссылок</li>
</ol>
<br />

<div id="block1">
<div id="bl">
Использовать на <input type="number" id="procent-1" name="procent-1" value="<?=htmlspecialchars($procent1[1]??'');?>" min="0" max="100" step="1">
% от общего кол-ва (от 0 до 100)<br />
Ссылочный реферер (любые ссылки)

<a class="tooltip" href="#">[?]<span class="custom help"><img src="/images/faq/Help.png" alt="Помощь" height="48" width="48" /><em>Подсказка</em>Ссылочный referer - это список страниц (url ссылок), с которых "якобы" перешел пользователь на Ваш сайт. Реального перехода с этих адресов нет, используется подмена на уровне сервера (любые счетчики статистики и анализаторы не могут уловить эту подмену). Максимальное кол-во задаваемых ссылок: 500 шт (берутся случайно при каждом первоначальном заходе на сайт). Для имитации пустого реферера ("закладки") используйте ключевое слово about:blank.<br>
В нашей системе можно задать распределение между ссылочным реферером и поисковым в процентном соотношении (поле выше)</span></a>
<br />
<textarea type="text" cols="70" class="area-2 resize-vertical" rows="16" name="refer1" id="refer1"  placeholder="Cсылочный referer (любые ссылки с http://), либо слово about:blank для закладок"/><?=htmlspecialchars($out_refer1??'');?></textarea>
<p>
<ol>
<li>Использовать ссылки с http:// или https://</li>
<li>Каждая ссылка с новой строки, запрещено использовать ковычки</li>
<li>Максимум 500 ссылок</li>
<li>Для имитации переходов с закладок использовать ключевое слово <b>about:blank</b></li>
</ol>
</p></div></div>

<div id="block2">
<div id="bl">
Использовать на <input type="number" name="procent-2" id="procent-2" value="<?=htmlspecialchars($procent2??'');?>" min="0" max="100" step="1">
от общего кол-ва % (от 0 до 100)<br />
Поисковые запросы (каждый запрос с новой строки)
<a class="tooltip" href="#">[?]<span class="custom help"><img src="/images/faq/Help.png" alt="Помощь" height="48" width="48" /><em>Подсказка</em>Поисковой referer - это "поисковые запросы", по которым на Ваш сайт якобы пришли пользователи. В нашей системе используется собственный алгоритм генерации поискового реферера. Можно лишь задать нужные запросы и выбрать поисковую машину, система сама сделает нужные ссылки для подмены. Кол-во запросов ограничено 500 шт, пустые строки буду удалены. Запрещено использовать ссылки только обычные слова!<br>
В нашей системе можно задать распределение между ссылочным реферером и поисковым в процентном соотношении (поле выше)</span></a>
<br />
<div class="poisk2 border-off" align="right">
<a class="tooltip" href="#">[?]<span class="custom help"><em>Подсказка</em>Вы можете задать приоритет использования нужных Вам поисковых машин, или же заполнить их случайно кнопкой "заполнить". При определении нужной поисковой машины используется равновероятный генератор случайных чисел! Для того чтобы задать например только "закладки" поставьте значение 100%, у остальных - 0%</span></a>
<u>Процент использования</u>
<br>
<div class="search">
Закладки            <input class="good" type="number" name="check[0]" id="check-0" min="0" max="100" value="<?=htmlspecialchars($param0[1]??'');?>"> %<br />
Yandex.ru           <input class="good" type="number" name="check[1]" id="check-1" min="0" max="100" value="<?=htmlspecialchars($param1[1]??'');?>"> %<br />
Google.ru           <input class="good" type="number" name="check[2]" id="check-2" min="0" max="100" value="<?=htmlspecialchars($param2[1]??'');?>"> %<br />
Nigma .ru           <input class="good" type="number" name="check[3]" id="check-3" min="0" max="100" value="<?=htmlspecialchars($param3[1]??'');?>"> %<br />
Qip.ru              <input class="good" type="number" name="check[4]" id="check-4" min="0" max="100" value="<?=htmlspecialchars($param4[1]??'');?>"> %<br />
Go.mail.ru          <input class="good" type="number" name="check[5]" id="check-5" min="0" max="100" value="<?=htmlspecialchars($param5[1]??'');?>"> %<br />
Rambler.ru          <input class="good" type="number" name="check[6]" id="check-6" min="0" max="100" value="<?=htmlspecialchars($param6[1]??'');?>"> %<br />
Google.com.ua       <input class="good" type="number" name="check[7]" id="check-7" min="0" max="100" value="<?=htmlspecialchars($param7[1]??'');?>"> %<br />
Meta.ua             <input class="good" type="number" name="check[8]" id="check-8" min="0" max="100" value="<?=htmlspecialchars($param8[1]??'');?>"> %<br />
Yandex.ua           <input class="good" type="number" name="check[9]" id="check-9" min="0" max="100" value="<?=htmlspecialchars($param9[1]??'');?>"> %<br />
Bigmir.net          <input class="good" type="number" name="check[10]" id="check-10" min="0" max="100" value="<?=htmlspecialchars($param10[1]??'');?>"> %<br />
Bing.com            <input class="good" type="number" name="check[11]" id="check-11" min="0" max="100" value="<?=htmlspecialchars($param11[1]??'');?>"> %<br />
Yandex.com          <input class="good" type="number" name="check[12]" id="check-12" min="0" max="100" value="<?=htmlspecialchars($param12[1]??'');?>"> %<br />
Google.com          <input class="good" type="number" name="check[13]" id="check-13" min="0" max="100" value="<?=htmlspecialchars($param13[1]??'');?>"> %<br />
        <span class="warning">Сумма должна = 100!</span>
    </div><br />
    <input type="button" onclick="rnd2()" value="заполнить &#9998;" title="Автоматически подобрать нужные значения">
</div>
<div class="poisk" align="left">
<textarea type="text" class="area-3 resize-vertical" cols="60" rows="16" name="zaprospoisk" id="zaprospoisk" placeholder="Ключевые запросы, каждый с новой строки. Для использования "закладок" поставьте галочку у поисковиков"/><?=htmlspecialchars($out_words??'');?></textarea>
</div>
<p>
<ol>
<li>Закладки - прямые переходы из браузера</li>
<li>Ключевые слова не должны содержать спецсимволов</li>
<li>Максимум 500 запросов, запрещено использовать ковычки</li>
<li>Ключевые слова преобразуются в поисковый referer автоматически</li>
<li>Запрещено использовать ссылки</li>
</ol>
</p>
</div></div>

<center><button class="new-2" type="submit" name="generate"/>Сохранить параметры</button></center>

<?= Html::endForm(); ?>
<?php endif;?>
