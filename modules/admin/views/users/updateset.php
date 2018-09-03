<?php

use yii\helpers\Html;
use app\models\Conf;

/* @var $this yii\web\View */
/* @var $model app\models\Users */

$this->title = Yii::t('app', 'Изменить настройку', [
    'nameAttribute' => Yii::$app->request->get('id'),
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Пользователи'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('app', 'Изменить настройку');


$this->registerCss("
#pwms > span{
  margin: 1px;
  display: block;
}
.tableInputTop tbody td:first-child{
  vertical-align: top;
  width: 20px;
}
");
?>
<div class="users-update">

<h1><?= Html::encode($this->title) ?></h1>


<br />
id настройки:<span id="nom"><?=Html::encode($jetid)?></span>
<br />
<input id="gnac" value="<?=Html::encode(Conf::getParams('gnac'));?>" type="hidden" />
<input id="gnaci" value="<?=Html::encode(Conf::getParams('gnaci'));?>" type="hidden" />
<input id="rnac" value="<?=Html::encode(Conf::getParams('rnac'));?>" type="hidden" />
<input id="rnaci" value="<?=Html::encode(Conf::getParams('rnaci'));?>" type="hidden" />
Название шаблона:<?=$usernameq;?><br />
ID юзера: <?=Html::a($IDuser, ['view', 'id' => $IDuser], ['target' => '_blank'])?><br />
<?php if( empty($_REQUEST['vup']) ){ ?><br />
  <?=Html::a('Обновить настройки после изменения на Jetswap', ['updateset', 'id' => Html::encode($jetid), 'upd' => 1])?>
<?php }elseif($_REQUEST['vup']=='ok'){ ?><br /><font color="green">обновлено</font><?php } ?>
<br/>
<?php
if (file_exists($dir)) {echo "<a target='_blank' href='".Conf::setIdUrl($jetid, $rnd)."'>Файл настройки JS</a><br/>";}
if (file_exists($dir)) {echo "<a target='_blank' href='/admin/users/robot-edit?id=$jetid'>Настройки шаблона</a>";}
?>
<?php if( ($_REQUEST['edit'] ?? '') =='ok'){ ?><br /><font color="green">Сохранено</font><?php } ?>
<form method="post" action="">
   <table cellpadding="3" cellspacing="1" bgcolor="gray" border="0" width="100%">
      <tbody>
         <tr align="center" bgcolor="#FFCC00">
            <td><b>Настройки показов</b></td>
         </tr>
         <tr bgcolor="white">
            <td>
               <center>
                  <span id="idsites">Адрес сайта <input name="site" size="70" value="<?=Html::encode($output2['url'] ?? '')?>" class="norm" type="text">
                  <br><br>

                  Показов в день <input name="pkm" value="<?=Html::encode($output2['pkm'] ?? '')?>" size="3" class="norm" type="text">
				  Показов в час <input name="pkh" size="3" value="<?=Html::encode($output2['pkh'] ?? '')?>" class="norm"><br>
                  <table>
                     <tbody>
                        <tr>
                           <td>
                              <i>
                                 <li>0 — без ограничений</li>
                                 <li>положительное число — количество показов</li>
                              </i>
                              <li><i>отрицательное число — не показывать</i></li>
                           </td>
                        </tr>
                     </tbody>
                  </table>
                  Время показа :
				  <input name="pkt" value="<?=Html::encode($output2['pkt'] ?? '')?>" class="norm" size="3">
				  -
				  <input name="pkt2" value="<?php if(isset($output2['pkt2']) && $output2['pkt2']=='0'){echo Html::encode($output2['pkt']);}else{echo Html::encode($output2['pkt2'] ?? '');}?>" class="norm" size="3"> сек. (30-900)

				  <br>Интервал между показами сейчас: <b>0±10%</b>

				  <br>
				  Интервал мин: <input name="tml1" value="<?=Html::encode($output2['tml1'] ?? '')?>" class="norm" size="5">
				  макс: <input name="tml2" value="<?=Html::encode($output2['tml2'] ?? '')?>" class="norm" size="5"> <a href="javascript:calctm();">сек</a>

				  <input name="tmlrefresh" <?php if(isset($output2['tmlrefresh']) && $output2['tmlrefresh']=='1'){echo'checked';}?>   value="1" id="tmlrefresh" type="checkbox">
				  <label for="tmlrefresh">Изменить сейчас</label><br>

                  Изменение интервала в течение часа: мин: <input name="tmlc1" value="<?=Html::encode($output2['tmlc1'] ?? '')?>" class="norm" size="5">
				  макс: <input name="tmlc2" value="<?=Html::encode($output2['tmlc2'] ?? '')?>" class="norm" size="5">

                  <p><b>Цена одного показа: <i><span id="sts">3.35 - 4.47</span></i> кред.</b></p>
               </center>
            </td>
         </tr>
         <tr align="center" bgcolor="#FFCC00">
            <td><b>Дополнительные параметры</b></td>
         </tr>
         <tr bgcolor="white">
            <td>
               <table align="center" width="95%" class="tableInputTop">
                  <tbody>
                     <tr>
                        <td><input name="ssf" <?php if(isset($output2['ssf']) && $output2['ssf']=='1'){echo'checked';}?>  value="1" id="ssf"  type="checkbox"></td>
                        <td>Показывать сайт только в <a href="http://www.jetswap.com/safesurf.htm">SafeSurf</a><br>
                           При включенном показе только в SafeSurf разрешено нарушение пунктов <a href="http://www.jetswap.com/rules.htm" target="_blank">правил</a>, отмеченных <b style="color:red">[*]</b><br>
                        </td>
                     </tr>
                     <tr>
                        <td><input name="ipc" <?php if(isset($output2['ipc']) && $output2['ipc']=='1'){echo'checked';}?>   value="1" id="ipc"  type="checkbox"></td>
                        <td><label for="ipc">Показывать сайт только уникальным посетителям</label><br><i>Реклама сайтов только для уникальных посетителей стоит на 10% дороже, но они имеют больший приоритет показа и показываются одному пользователю один раз в течение заданного ниже времени уникальности IP.</i></td>
                     </tr>
                     <tr>
                        <td><input name="second" <?php if(isset($output2['second']) && $output2['second']=='1'){echo'checked';}?>  value="1" id="second"  type="checkbox"></td>
                        <td><label for="second">Не показывать сайт посетителям с близкими IP-адресами</label><br><i>Поставьте эту опцию при большом количестве показов сайта, чтобы исключить подозрения в накрутке.</i></td>
                     </tr>
                     <tr>
                        <td><input name="proxy" <?php if(isset($output2['proxy']) && $output2['proxy']=='1'){echo'checked';}?>  value="1" id="proxy" type="checkbox"></td>
                        <td><label for="proxy">Не показывать сайт посетителям с прокси-серверов</label><br><i>Поставьте эту опцию для увеличения процента зафиксированных счетчиками показов.</i></td>
                     </tr>
                     <tr>
                        <td><input name="exact" <?php if(isset($output2['exact']) && $output2['exact']=='1'){echo'checked';}?>  value="1" id="exact" type="checkbox"></td>
                        <td><label for="exact">Точное соблюдение лимитов показов</label><br><i>Поставьте эту опцию для 100% исключения превышения лимитов показов и нарушения заданного интервала между показами.</i></td>
                     </tr>
                     <tr>
                        <td>&nbsp;</td>
                        <td>Время уникальности IP-адресов: от <input name="iphl" id="iphl" value="<?=Html::encode($output2['iphl'] ?? '')?>" class="norm" size="3"> до <input name="iph" id="iph" value="<?=Html::encode($output2['iph'] ?? '')?>" class="norm" size="3"> ч. <i>1-240 часов</i></td>
                     </tr>
                     <tr>
                        <td>
                           <input name="li" <?php if (isset($output2['li']) && $output2['li'] == '1') {echo 'checked';}?>  value="1" id="li" type="checkbox">
                        </td>
                        <td>
                           <label for="li">Фильтрация людей не учитываемых в Li.ru</label>
                           <br>
                           <i>Фильтрация посетителей, не учитываемых статистикой LiveInternet (база собранная за 6 мес.)</i>
                        </td>
                     </tr>
                     <tr>
                        <td><input name="dayunickon" <?php if(($output2['dayunick'] ?? '')!='0'){echo'checked';}?>   value="1" id="dayunickon" onclick="if(!this.checked)document.forms[0].elements['dayunick'].value=0;" type="checkbox"></td>
                        <td>Посуточная уникальность IP: отклонение от московского времени <input name="dayunick" value="<?=Html::encode($output2['dayunick'] ?? '')?>" class="norm" size="3"> часов <i>±23</i><br><i>от московского времени.</i></td>
                     </tr>
                     <tr>
                        <td><input name="msf" <?php if(isset($output2['msf']) && $output2['msf']=='1'){echo'checked';}?>   value="1" id="msf" type="checkbox"></td>
                        <td><label for="msf">Показывать сайт только в ручном серфинге</label></td>
                     </tr>
                     <tr>
                        <td>&nbsp;</td>
                        <td>Эксклюзивные IP-адреса: <input name="ipex" value="<?=Html::encode($output2['ipex'] ?? '')?>" id="ipex" size="3" class="norm"> посещение сайта <i>(1-15)</i><br>
                           <i>Показывать сайт только IP-адресам, с которых не было посещений как минимум 48 часов.<br>Текущая наценка за первое посещение - <span id="ipexnac">200</span>%</i>
                        </td>
                     </tr>
                     <tr>
                        <td><input name="hideref" <?php if(isset($output2['hideref']) && $output2['hideref']=='1'){echo'checked';}?>  value="1"  type="checkbox"></td>
                        <td>Скрывать источник посещений — не передавать HTTP_REFERER(только для обычного серфинга) <a href="http://www.jetswap.com/noreferer.htm" target="_blank"><b>(?)</b></a></td>
                     </tr>
                     <tr>
                        <td>&nbsp;</td>
                        <td>
                           Показывать сайт в <a href="http://www.jetswap.com/hidden.htm">скрытом серфинге</a>
                           <select name="hsf" id="hsf">
                              <option <?php if(isset($output2['hsf']) && $output2['hsf']=='0'){echo'selected';}?>  value="0">Нет</option>
                              <option <?php if(isset($output2['hsf']) && $output2['hsf']=='1'){echo'selected';}?> value="1">Да</option>
                              <option <?php if(isset($output2['hsf']) && $output2['hsf']=='2'){echo'selected';}?> value="2">Только в скрытом</option>
                           </select>
                           <br>
                           <i>Разрешите показ сайта в скрытом серфинге, чтобы получать больше трафика</i>
                        </td>
                     </tr>
                     <tr>
                        <td><input name="uh" <?php if(isset($output2['uh']) && $output2['uh']=='1'){echo'checked';}?>  value="1" id="uh"  type="checkbox"></td>
                        <td><label for="uh">Скрывать URL сайта</label><br>
                           <i>URL сайта не будет показываться в SafeSurf, списке просмотренных сайтов и сообщении о нарушителе.</i>
                        </td>
                     </tr>
                     <tr>
                        <td><input name="dontstop"  <?php if(isset($output2['dontstop']) && $output2['dontstop']=='1'){echo'checked';}?>   value="1" id="dontstop" type="checkbox"></td>
                        <td><label for="dontstop">Не отключать показ сайта при его неработоспособности</label><br>
                           <i>При этом пользователям будут засчитываться показы, даже если сайт не загрузился.</i>
                        </td>
                     </tr>
                     <tr>
                        <td>&nbsp;</td>
                        <td>
                           <table align="center" border="0">
                              <tbody>
                                 <tr>
                                    <td>Имя сайта</td>
                                    <td><input class="norm" name="name" value="<?=Html::encode($output2['name'] ?? '')?>" size="40" maxlength="64"></td>
                                    <td rowspan="2"><font size="1">отображается в списке сайтов<br>вводить не обязательно</font></td>
                                 </tr>
                                 <tr>
                                    <td>
                                    </td>
                                    <td>

                                       <input type="hidden" name="fid" value="<?=Html::encode($output2['fid'] ?? '')?>" >
                                    </td>
                                 </tr>
                                 <tr>
                                    <td>Имя сайта (пользовательское)</td>
                                    <td><input class="norm" name="username_site" value="<?=Html::encode($usernameq)?>" size="40" maxlength="64"></td>
                                    <td rowspan="2"><font size="1">отображается в списке сайтов<br>вводить не обязательно</font></td>
                                 </tr>
                                 <tr>
                                    <td>
                                    </td>
                                    <td>
                                    </td>
                                 </tr>
                              </tbody>
                           </table>
                        </td>
                     </tr>
                  </tbody>
               </table>
            </td>
         </tr>


                              <tr bgcolor=#FFCC00 align=center>
                                 <td><b>Ограничение скорости посетителей (тестовая версия)</b>
                              <tr bgcolor=white>
                                 <td>
                                    <table align=center border=0>
                                       <tr>
                                          <td colspan=2>Функция контролирует скорость загрузки страниц сайтов<BR>
                                          Скорость загрузки страниц <u>всегда</u> значительно меньше максимальной скорости канала.</td>
                                       </tr>
                                       <tr>
                                          <td>Минимальная скорость загрузки страниц</td>
                                          <td><input class=norm name=speed value="<?=Html::encode($output2['speed'] ?? '') ?>" size=3 id=speed> <i>0.1-25.5 МБит/c, 0 - отключено</i></td>
                                       </tr>
                                       <tr>
                                          <td>Минимальная скорость загрузки больших файлов</td>
                                          <td><input class=norm name=highspeed value="<?=Html::encode($output2['highspeed'] ?? '') ?>" size=3 id=highspeed> <i>0.1-25.5 МБит/c, 0 - отключено</i></td>
                                       </tr>
                                   <p>Функция является тестовой, использование больших ограничений может привести к сокращению доступного трафика. Рекомендуем
                                      вводить малые ограничения, например минимального ограничения скорости в 0.1 Мбит/сек
                                      достаточно для качественной загрузки большинства сайтов. Настройки индивидуальны!</p>
                                    </table>


         <tr align="center" bgcolor="#FFCC00">
            <td><b>Геотаргетинг</b></td>
         </tr>
         <tr bgcolor="white">
            <td>
               <a href="http://jetswap.com/h?geo" target="_blank">Геотаргетинг</a> позволяет получать посетителей из нужных стран, а для России и Украины также и из нужных городов, округов и регионов.<br>
               <input name="ggeo" value="<?=Html::encode($output2['ggeo'] ?? '')?>" type="hidden"><input name="rgeo" value="<?=Html::encode($output2['rgeo'] ?? '')?>" type="hidden">
               <center><input name="geo" <?php if(isset($output2['geo']) && $output2['geo']=='1'){echo'checked';}?>  value="1" id="geochk" onclick="geoclick(1);"  type="checkbox"> <label for="geochk">Использовать геотаргетинг</label></center>
               <div id="geoset" style="display: block;">
                  <p>Показать: <a href="javascript:doall(0);">Только активные</a> <a href="javascript:doall(1);">Все</a><br>
                     Сортировка: <a href="javascript:dosort(0);">Количество посетителей</a> <a href="javascript:dosort(1);">Название</a>
                  </p>
                  <center>
                     <div id="geo1"><a href="javascript:doupdate();">Изменить</a></div>
                  </center>
               </div>
            </td>
         </tr>


         <tr align="center" bgcolor="#FFCC00">
            <td><b>Режим презентации</b></td>
         </tr>
         <tr bgcolor="white">
            <td>
               <center>
                  Позволяет показывать несколько страниц сайта последовательно,<br>а также осуществлять множество различных операций с сайтом. <a href="http://jetswap.com/h?pr"><b>(?)</b></a><br>
                  Действует только при показе в SafeSurf, в обычном серфинге настройки игнорируются.
                  <br>
                  <table border="0">
                     <tbody>
                        <tr>
                           <td><input <?php if(isset($output2['prs']) && $output2['prs']=='0'){echo'checked';}?> name="prs" value="0" id="prs0" onclick="pcp(0);" type="radio"> <label for="prs0">Отключить</label><br>
                              <input name="prs" <?php if(isset($output2['prs']) && $output2['prs']=='1'){echo'checked';}?>  value="1" id="prs1" onclick="pcp(1);"  type="radio"> <label for="prs1">Показ страниц одного сайта</label><br>
                              <input name="prs" <?php if(isset($output2['prs']) && $output2['prs']=='2'){echo'checked';}?>  value="2" id="prs2" onclick="pcp(2);" type="radio"> <label for="prs2">Показ страниц разных сайтов</label>
                           </td>
                        </tr>
                     </tbody>
                  </table>
               </center>
               <span style="display: block;" id="prsmain">
                  <br>
                  Перед установкой следующих параметров обязательно <a href="http://www.jetswap.com/pr.htm">ознакомьтесь с инструкцией</a>!<br>
                  <table align="center" border="0">
                     <tbody>
                        <tr>
                           <td>Время показа первого сайта</td>
                           <td><input name="prstime" value="<?=Html::encode($outputist['0'] ?? '')?>" size="2"> секунд</td>
                        </tr>
                        <tr>
                           <td>Случайное отклонение от заданного времени, +/-<br><font size="1">для каждой страницы, включая первую</font></td>
                           <td><input name="prstime1" value="<?=Html::encode($outputist[1] ?? '')?>" size="2"> секунд</td>
                        </tr>
                        <tr>
                           <td>Минимум страниц для просмотра<br><font size="1">не считая первой, введите 0 для показа всех</font></td>
                           <td><input name="prsmin" value="<?=Html::encode($outputist[2] ?? '')?>" size="2"></td>
                        </tr>
                        <tr>
                           <td>Максимум страниц для просмотра<br><font size="1">не считая первой, введите 0 для показа всех</font></td>
                           <td><input name="prsmax" value="<?=Html::encode($outputist[3] ?? '')?>" size="2"></td>
                        </tr>
                        <tr>
                           <td>Дополнительных вкладок браузера<br><font size="1">не более трех</font></td>
                           <td><input name="prtab" id="prtab" value="<?=Html::encode($output2['prtab'] ?? '')?>" size="2"></td>
                        </tr>
                     </tbody>
                  </table>
                  <table align="center" border="0" class="tableInputTop">
                     <tbody>
                        <tr>
                           <td><input name="prsref"<?php if(isset($outputist[4]) && $outputist[4]=='0'){echo'checked';}?> value="0" id="prsref0" type="radio"></td>
                           <td><label for="prsref0">Передавать реферер по заказу на каждую страницу</label></td>
                        </tr>
                        <tr>
                           <td><input name="prsref" <?php if(isset($outputist[4]) && $outputist[4]=='1'){echo'checked';}?>  value="1"  id="prsref1" type="radio"></td>
                           <td><label for="prsref1">Передавать реферер по заказу на первую страницу, на остальные - текущую страницу в браузере как реферер</label></td>
                        </tr>
                        <tr>
                           <td><input name="prsref" <?php if(isset($outputist[4]) && $outputist[4]=='2'){echo'checked';}?>  value="2" id="prsref2" type="radio"></td>
                           <td><label for="prsref2">Передавать реферер по заказу на первую страницу, на остальные - предыдущую заданную вами страницу как реферер</label></td>
                        </tr>
                        <tr>
                           <td><input name="prstime2" <?php if(isset($outputist[5]) && $outputist[5]=='1'){echo'checked';}?>  value="1" id="prstime2" type="checkbox"></td>
                           <td><label for="prstime2">Завершать показ раньше общего времени показа(случайно, до 14 секунд)</label></td>
                        </tr>
                        <tr>
                           <td><input name="prsrnd" <?php if(isset($outputist[6]) && $outputist[6]=='1'){echo'checked';}?> value="1"  id="prsrnd" type="checkbox"></td>
                           <td><label for="prsrnd">Случайный порядок показа страниц</label></td>
                        </tr>
                        <tr>
                           <td><input name="mouse" <?php if(isset($output2['mouse']) && $output2['mouse']=='1'){echo'checked';}?> value="1"  id="mouse" type="checkbox"></td>
                           <td><label for="mouse">Разрешить использование мыши</label></td>
                        </tr>
                     </tbody>
                  </table>
                  <br>
                  <p>Список сайтов: <span id="spdsc">вводите <b>только адреса страниц</b>, без домена сайта, например <u>index.html, folder/subfolder/page.php?a=b</u>. неправильно: <s>http://www.site.ru/page.html</s></span><br></p>
                  <center>
                     Время показа&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                     Команда&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                     Адрес сайта или параметры команды&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                     <span id="pwms" style="display: table">
                        <?php
                           foreach ($ptm2 as $key=>$value) {
                           $g=$key+1;
                           ?>
                        <span id="hsp<?=Html::encode($g)?>">
                           <input name="tms[]" id="tms<?=Html::encode($g)?>" size="3" value="<?=Html::encode($ptm2[$key])?>">
                           <select name="cmds[]" id="cmds<?=Html::encode($g)?>">
                              <option value="0" <?php if($pac2[$key]=='0'){ echo'selected=""';} ?>>Переход</option>
                              <option value="1" <?php if($pac2[$key]=='1'){ echo'selected=""';} ?>>Поиск ссылки</option>
                              <option value="2" <?php if($pac2[$key]=='2'){ echo'selected=""';} ?>>Ввод текста</option>
                              <option value="3" <?php if($pac2[$key]=='3'){ echo'selected=""';} ?>>Отметить флажок</option>
                              <option value="4" <?php if($pac2[$key]=='4'){ echo'selected=""';} ?>>Отправить форму</option>
                              <option value="5" <?php if($pac2[$key]=='5'){ echo'selected=""';} ?>>Отправить событие</option>
                              <option value="6" <?php if($pac2[$key]=='6'){ echo'selected=""';} ?>>Очистить Cookies</option>
                              <option value="7" <?php if($pac2[$key]=='7'){ echo'selected=""';} ?>>Вставить скрипт</option>
                              <option value="8" <?php if($pac2[$key]=='8'){ echo'selected=""';} ?>>Переход POST</option>
                              <option value="9" <?php if($pac2[$key]=='9'){ echo'selected=""';} ?>>Повысить привилегии</option>
                              <option value="10" <?php if($pac2[$key]=='10'){ echo'selected=""';} ?>>Клик</option>
                           </select>
                           <input name="urls[]" value="<?=Html::encode($purl2[$key])?>" size="40" id="urls<?=Html::encode($g)?>" onkeyup="pck(<?=Html::encode($g)?>,this.value);" onchange="pck(<?=Html::encode($g)?>,this.value);">
                           <a href="javascript:insert(<?=Html::encode($g)?>);"><img src="/images/i/icon_add2.png" alt="Добавить" title="Добавить" border="0"></a>
                           <a href="javascript:move_up(<?=Html::encode($g)?>);"><img src="/images/i/icon_up.png" alt="Вверх" title="Вверх" border="0"></a>
                           <a href="javascript:move_down(<?=Html::encode($g)?>);"><img src="/images/i/icon_down.png" alt="Вниз" title="Вниз" border="0"></a>
                           <a href="javascript:copy(<?=Html::encode($g)?>);"><img src="/images/i/icon_copy2.png" alt="Копировать" title="Копировать" border="0"></a>
                           <a href="javascript:pcd(<?=Html::encode($g)?>);"><img src="/images/i/icon_del.png" alt="Удалить" title="Удалить" border="0"></a>
                        </span>
                        <?php }
                           ?>
                        <span id="xxx"></span>
                     </span>
                  </center>
                  <script language="javascript" src="/js/prs.js"></script><script language="javascript">
                     var pc=<?=Html::encode($g)?>;
                     pcc();
                     pcp(<?=Html::encode($g)?>);
                     function pcp(v)
                     {
                     if(v==0)document.getElementById("prsmain").style.display="none";
                     else document.getElementById("prsmain").style.display="block";
                     if(v==1)document.getElementById("spdsc").innerHTML="вводите <b>только адреса страниц</b>, без домена сайта, например <u>index.html, folder/subfolder/page.php?a=b</u>. неправильно: <s>http://www.site.ru/page.html</s>";
                     else document.getElementById("spdsc").innerHTML="вводите <b>полные адреса страниц</b>, например <u>http://www.site.ru/page.html</u>. неправильно: <s>www.site.ru/page.html</s>";
                     }
                     function pcc_gettext()
                     {
                     return "<input name=tms[] id=tms" + pc + " size=3> <select name=cmds[] id=cmds" + pc + "><option value=0>Переход<option value=1>Поиск ссылки<option value=2>Ввод текста<option value=3>Отметить флажок<option value=4>Отправить форму<option value=5>Отправить событие<option value=6>Очистить Cookies<option value=7>Вставить скрипт<option value=8>Переход POST<option value=9>Повысить привилегии<option value=10>Клик</select> <input name=urls[] size=40 id=urls" + pc + " onkeyup=pck(" + pc + ",this.value); onchange=pck(" + pc + ",this.value);> <a href=javascript:insert("+ pc+");><img border=0 src=/images/i/icon_add2.png alt=Добавить title=Добавить></a> <a href=javascript:move_up("+pc+");><img border=0 src=/images/i/icon_up.png alt=Вверх title=Вверх></a> <a href=javascript:move_down("+pc+");><img border=0 src=/images/i/icon_down.png alt=Вниз title=Вниз></a> <a href=javascript:copy("+pc+");><img border=0 src=/images/i/icon_copy2.png alt=Копировать title=Копировать></a> <a href=javascript:pcd(" + pc + ");><img border=0 src=/images/i/icon_del.png alt=Удалить title=Удалить></a>";
                     }
                  </script>
               </span>
            </td>
         </tr>
         <tr align="center" bgcolor="#FFCC00">
            <td><b>Особые отметки</b></td>
         </tr>
         <tr bgcolor="white">
            <td>
               Отметьте следующие галочки, если ваш сайт нарушает правила системы, выделенные специальными отметками. Вам следует ознакомиться с <a href="http://www.jetswap.com/rules.htm">полным текстом правил</a>, чтобы определить необходимость выставления отметки. Сайты, нарушающие указанные правила, могут рекламироваться, только если соответствующие отметки выставлены. Отметка данных опций влияет на стоимость показа сайта. <a href="http://www.jetswap.com/special.htm"><b>Подробное описание настроек</b></a>.
               <ul>
                  <li><input name="v2" <?php if(isset($output2['v2']) && $output2['v2']=='1'){echo'checked';}?> value="1" id="v2id"  type="checkbox"> <label for="v2id"><b>Эротика/порно/лохотроны/мат/без содержания и т.п.</b></label><br>
                     Сайт нарушает пункты <a href="http://www.jetswap.com/rules.htm" target="_blank">правил</a>, отмеченные <b style="color:red">[**]</b><br>
                  </li>
                  <li><input name="v3" <?php if(isset($output2['v3']) && $output2['v3']=='1'){echo'checked';}?>  value="1" id="v3id" type="checkbox"> <label for="v3id"><b>Загрузка других сайтов во Frame/IFrame/обмен трафиком/вирусы</b></label><br>
                     Сайт нарушает пункты <a href="http://www.jetswap.com/rules.htm" target="_blank">правил</a>, отмеченные <b style="color:red">[***]</b><br>
                  </li>
                  <li><input name="v4" <?php if(isset($output2['v4']) && $output2['v4']=='1'){echo'checked';}?>  value="1" id="v4id" type="checkbox"> <label for="v4id"><b>Звуки/видео на сайте</b></label><br>
                     Сайт нарушает пункты <a href="http://www.jetswap.com/rules.htm" target="_blank">правил</a>, отмеченные <b style="color:red">[****]</b><br>
                  </li>
                  <li><input name="v5" <?php if(isset($output2['v5']) && $output2['v5']=='1'){echo'checked';}?> value="1" id="v5id" type="checkbox"> <label for="v5id"><b>Перенаправления на другие сайты (без разрушения фрейма серфинга)</b></label><br>
                     Сайт нарушает пункты <a href="http://www.jetswap.com/rules.htm" target="_blank">правил</a>, отмеченные <b style="color:red">[*****]</b><br>
                  </li>
               </ul>
            </td>
         </tr>
         <tr bgcolor="white">
            <td>
               <p></p>
               <center><input <?php if( $jetidModel->view != 1 ){ ?>onclick="if(!confirm('Шаблон архивный. Восстановить шаблон?')) return false;"<?php } ?> value="Сохранить" class="buttonNormal" type="submit"> <input value="Отменить" class="buttonNormal" type="reset"></center>
            </td>
         </tr>
      </tbody>
   </table>
   <script language=javascript src="/js/site.js"></script>
</form>

</div>
