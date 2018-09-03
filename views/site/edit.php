<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use app\widgets\Alert;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use app\components\ApiFunctions;
use app\models\Conf;

$this->title = 'Редактирование';
$this->params['breadcrumbs'][] = $this->title;

$this->registerJsFile('/js/jquery.block-ui.js');
$this->registerJsFile('/js/loader.js');
$this->registerJsFile('/js/edit.js');
$this->registerJsFile('/js/site.js');


?>



<div class="contentblockall">
   <div class="contentblock">
      <div class="contentblockblock">
         <div class="contentblocktableblock">
            <div class="contentblocktable">
               <div class="contentblocktablebgtitle">
                  <div class="contentblocktabletitle">
                     <h1>Настройки ID: <span id="nom"><?=Html::encode($jetid);?></span></h1>
                  </div>
               </div>
               <div class="contentblocktablebgcontblock">
                  <div class="contentblocktablebgcont2">
                  <a href="/api" title="ВЕРНУТЬСЯ НАЗАД"><<< ВЕРНУТЬСЯ НАЗАД</a>
                     <div class="error">
                        <?= Alert::widget() ?>
                     </div><br/>
                     <form id="edit-form" class="table-container">
                        <table cellpadding="3" class="edit_table_style" cellspacing="1" bgcolor="gray" border="0" width="100%">
                           <tbody>
                              <tr align="center" bgcolor="#AFE5C6">
                                 <td>
                                    <b>Настройки показов (основное влияние на качество трафика)</b>
                                 </td>
                              </tr>
                              <tr bgcolor="white">
                                 <td>
                                    <center>
                                       <br>
                                       Выполнений в день <input name="pkm" value="<?=Html::encode($output2['pkm'] ?? '') ?>" size="3" class="norm" type="text" >
                                       Выполнений в час <input name="pkh" value="<?=Html::encode($output2['pkh'] ?? '') ?>" size="3" class="norm" type="text" >
                                       <br>
                                       <table>
                                          <tbody>
                                             <tr>
                                                <td>
                                                   <i>
                                                      <li>0 — без ограничений</li>
                                                      <li>положительное число — количество выполнений</li>
                                                   </i>
                                                   <li><i>отрицательное число — не показывать</i></li>
                                                </td>
                                             </tr>
                                          </tbody>
                                       </table>
                        Время показа : <input name="pkt" value="<?=Html::encode($output2['pkt'] ?? '')?>" class="norm" size="3" type="text"> -
                        <input name="pkt2" value="<?php if (isset($output2['pkt2']) && $output2['pkt2'] == '0'){echo Html::encode($output2['pkt'] ?? '');}
                        else {echo Html::encode($output2['pkt2'] ?? '');}?>" class="norm" size="3" type="text"> сек. (30-900сек)<br>
                        <font color="green">Оптимальное время от 80 сек и выше!</font><br>

                        Интервал между показами сейчас: <b>0±10%</b><br>

                        Интервал мин: <input name="tml1" value="<?=Html::encode($output2['tml1'] ?? '') ?>" class="norm" size="5" type="text" >
                        макс: <input name="tml2" value="<?=Html::encode($output2['tml2'] ?? '') ?>" class="norm" size="5" type="text" > сек
                        <input name="tmlrefresh" <?php if (isset($output2['tmlrefresh']) && $output2['tmlrefresh'] == '1') {echo 'checked';}?>   value="1" id="tmlrefresh" type="checkbox">
                        <label for="tmlrefresh">Изменить сейчас</label><br>

                                       Изменение интервала в течение часа: мин: <input name="tmlc1" value="<?=Html::encode($output2['tmlc1'] ?? '') ?>" class="norm" size="5" type="text" > макс: <input name="tmlc2" value="<?=Html::encode($output2['tmlc2'] ?? '') ?>" class="norm" size="5" type="text" ><br>
                                       <a href="javascript:calctm();" title="Интервалы рассчитываются на основе данных цифр в полях день и час">Рассчитать интервалы</a>
                                       <?php
                                          echo '<div class="errorNull" style="display:none">У Вас написаны отрицательные числа в полях день/час - настройка не будет показываться!</div>';
                                          echo '<div class="error" style="display:none">Если интервалы равны нулю, показы будут выполняться очень быстро!</div>';
                                          ?>
                                       <p></p>
                                       <p style="color:red">Стоимость 1 выполнения шаблона <span id="sts"></span> реалов</p>
                                       <p></p>
                                       <?php
                                          if ($block_field) {
                                            echo '<div class="error">У вас <u>ВКЛЮЧЕНО</u> расписание показов, данные показываются из настроек расписания!</div>';
                                            echo '<div class="error">Если вы видите пустые поля, значит они не заданы в расписании</div>';
                                          }
                                          ?>
                                       <div  style="display: none">
                                          <span id="ipexnac">200</span>
                                          <input name="ssf" <?=Html::encode((isset ($output2['ssf']) && $output2['ssf'] == 1 ? "CHECKED" : ""));?>  value="1" id="ssf"  type="checkbox">
                                          <input name="v2" <?=Html::encode((isset ($output2['v2']) && $output2['v2'] == 1 ? "CHECKED" : ""));?> value="1" id="v2id"  type="checkbox">
                                          <input name="v3" <?=Html::encode((isset ($output2['v3']) && $output2['v3'] == 1 ? "CHECKED" : ""));?> value="1" id="v3id" type="checkbox">
                                          <input name="v4" <?=Html::encode((isset ($output2['v4']) && $output2['v4'] == 1 ? "CHECKED" : ""));?> value="1" id="v4id" type="checkbox">
                                          <input name="v5" <?=Html::encode((isset ($output2['v5']) && $output2['v5'] == 1 ? "CHECKED" : ""));?> value="1" id="v5id" type="checkbox">
                                          <input name="prs" <?=Html::encode((isset ($output2['prs']) && $output2['prs'] == 1 ? "CHECKED" : ""));?>  value="1" id="prs1" onclick="pcp(1);"  type="radio">
                                          <input name="prs" <?=Html::encode((isset ($output2['prs']) && $output2['prs'] == 2 ? "CHECKED" : ""));?> value="2" id="prs2" onclick="pcp(2);" type="radio">
                                          <input name="msf"  <?=Html::encode((isset ($output2['msf']) && $output2['msf'] == 1 ? "CHECKED" : ""));?> value="1" id="msf" type="checkbox">
                                          <select name="hsf" id="hsf">
                                             <option <?=Html::encode((isset ($output2['hsf']) && $output2['hsf'] == 0 ? "selected" : ""));?> value="0">Нет</option>
                                             <option  value="1" <?=Html::encode((isset ($output2['hsf']) && $output2['hsf'] == 1 ? "selected" : ""));?>>Да</option>
                                             <option  value="2" <?=Html::encode((isset ($output2['hsf']) && $output2['hsf'] == 2 ? "selected" : ""));?>>Только в скрытом</option>
                                          </select>
                                          <input name="prtab" id="prtab" value="<?=Html::encode((isset ($output2['prtab']) ? $output2['prtab'] : "0"));?>" size="2">
                                          <input name="ipex" value="<?=Html::encode((isset ($output2['ipex']) ? $output2['ipex'] : "0"));?>" id="ipex" size="10" class="norm">
                                          <input id="gnac" value="<?=Html::encode(Conf::getParams('gnac'));?>" type="hidden" />
                                          <input id="gnaci" value="<?=Html::encode(Conf::getParams('gnaci'));?>" type="hidden" />
                                          <input id="rnac" value="<?=Html::encode(Conf::getParams('rnac'));?>" type="hidden" />
                                          <input id="rnaci" value="<?=Html::encode(Conf::getParams('rnaci'));?>" type="hidden" />
                                       </div>
                                    </center>
                                 </td>
                              </tr>
                              <tr align="center" bgcolor="#AFE5C6">
                                 <td><b>Дополнительные параметры настроек (влияют частично на качество трафика)</b></td>
                              </tr>
                              <tr bgcolor="white">
                                 <td>
                                    <table align="center" width="95%">
                                       <tbody>
                                          <tr>
                                             <td><input name="ipc" <?php if (isset($output2['ipc']) && $output2['ipc'] == '1') {echo 'checked';}?> value="1" id="ipc"  type="checkbox"></td>
                                             <td><label for="ipc">Показывать сайт только уникальным посетителям</label><br><i>Отметьте данное поле для фильтрации по IP (за 24ч). Это увеличит процент посетителей на счетчиках, голосованиях</i></td>
                                          </tr>
                                          <tr>
                                             <td><input name="second" <?php if (isset($output2['second']) && $output2['second'] == '1') {echo 'checked';}?>  value="1" id="second"  type="checkbox"></td>
                                             <td><label for="second">Контролировать маски IP адресов</label><br><i>Исключает возможность показа близким и схожим IP, фильтрование идет на стороне сервера. Возможно замедление скорости накрутки.</i></td>
                                          </tr>
                                          <tr>
                                             <td><input name="proxy" <?php if (isset($output2['proxy']) && $output2['proxy'] == '1') {echo 'checked';}?>  value="1" id="proxy" type="checkbox"></td>
                                             <td><label for="proxy">Фильтровать прокси-сервера и впн-соединения</label><br><i>Уменьшает процент доступных людей, улучшает качество трафика, уменьшает скорость накрутки.</i></td>
                                          </tr>
                                          <tr>
                                             <td><input name="exact" <?php if (isset($output2['exact']) && $output2['exact'] == '1') {echo 'checked';}?>  value="1" id="exact" type="checkbox"></td>
                                             <td><label for="exact">Точное соблюдение настроек</label><br><i>Исключение превышения лимитов показов или нарушения заданных интервалов.</i></td>
                                          </tr>
                                          <tr>
                                             <td><input name="li" <?php if (isset($output2['li']) && $output2['li'] == '1') {echo 'checked';}?>  value="1" id="li" type="checkbox"></td>
                                             <td><label for="li">Фильтрация людей не учитываемых в Li.ru</label><br><i>Фильтрация посетителей, не учитываемых статистикой LiveInternet (база собранная за 6 мес.)</i></td>
                                          </tr>
                                          <tr>
                                             <td>&nbsp;</td>
                                             <td>Время уникальности IP-адресов: от <input name="iphl" id="iphl" value="<?=Html::encode($output2['iphl'] ?? '') ?>" class="norm" size="3" type="text"> до <input name="iph" id="iph" value="<?=Html::encode($output2['iph'] ?? '') ?>" class="norm" size="3" type="text"> ч. <i>1-240 часов (сильно влияет на качество трафика, исключает повторный показ с у людей в заданный период времени)</i></td>
                                          </tr>
                                          <tr>
                                             <td><input name="dayunickon" <?php if (isset($output2['dayunick']) && $output2['dayunick'] != '0') {echo 'checked';}?>   value="1" id="dayunickon" onclick="if(!this.checked)document.forms[0].elements['dayunick'].value=0;" type="checkbox"></td>
                                             <td><label for="dayunickon">Посуточная уникальность IP адресов: отклонение от московского времени</label><input name="dayunick" value="<?=Html::encode($output2['dayunick'] ?? '') ?>" class="norm" size="3" type="text"> часов <i>±23</i><br><i>от московского времени.</i><br></td>
                                          </tr>

                                             <td><input name="dontstop"  <?php if (isset($output2['dontstop']) && $output2['dontstop'] == '1') {echo 'checked';}?>   value="1" id="dontstop" type="checkbox"></td>
                                             <td><label for="dontstop">Продолжать работу если сайт недоступен</label><br>
                                                <i>Уменьшает процент успешных выполнений, расходует Ваш трафик быстрее. Возможно зависание сайтов, при больших скоростях.</i>
                                                <i>Рекомендуем выставлять данную опцию если это действительно необходимо, может вызвать сильную нагрузку на сайт.</i><br>
                                             </td>
                                          <tr>
                                             <td>&nbsp;</td>
                                             <td>
                                                <p><b><u>Внимание</u></b>: система может продолжать работу в автоматическом режиме при обнаружении на сайте вирусов, сплоитов, редиректов, различных видео и всплываюших окон, рекламирование порно, наличия звуковых сообщений однако стоимость за показ (в реалах) для таких настроек автоматически изменяется на более дорогую, расходуя Ваш баланс. Внимательно проверяйте свои сайты на данные виды объектов. Дополнительно качество будет изменено для сайтов: содержащих мат, лохотронство, паразитные iframe.</p>
                                                <table align="center" border="0">
                                                   <tbody>
                                                      <tr>
                                                         <td>Название настройки</td>
                                                         <td><input class="norm" name="name" value="<?=Html::encode($jetidModel->username); ?>" size="40" maxlength="64" type="text"></td>
                                                         <td rowspan="2"><font size="1">отображается в списке настроек<br>вводить не обязательно</font></td>
                                                      </tr>
                                                      <tr>
                                                         <td></td>
                                                         <td>
                                                            <input type="hidden" name="fid" value="<?=Html::encode($output2['fid'] ?? ''); ?>" >
                                                            <input type="hidden" name="id" value="<?=Html::encode($jetid);?>" >
                                                            <input type="hidden" name="user_id" value="<?=Html::encode(Yii::$app->user->id);?>" >
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
                              <tr bgcolor="#AFE5C6" align="center">
                                 <td><b>Ограничение скорости посетителей</b></td>
                              </tr>
                              <tr bgcolor="white">
                                 <td>
                                    <table align="center" border="0">
                                       <tr>
                                          <td colspan="2">Функция контролирует скорость загрузки страниц сайтов<BR>
                                          Скорость загрузки страниц <u>всегда</u> значительно меньше максимальной скорости канала.</td>
                                       </tr>
                                       <tr>
                                          <td>Минимальная скорость загрузки страниц</td>
                                          <td><input class="norm" name="speed" value="<?=Html::encode($output2['speed'] ?? '') ?>" size="3" id="speed"> <i>0.1-25.5 МБит/c, 0 - отключено</i></td>
                                       </tr>
                                       <tr>
                                          <td>Минимальная скорость загрузки больших файлов</td>
                                          <td><input class="norm" name="highspeed" value="<?=Html::encode($output2['highspeed'] ?? '') ?>" size="3" id="highspeed"> <i>0.1-25.5 МБит/c, 0 - отключено</i></td>
                                       </tr>
                                   <p>Функция является тестовой, использование больших ограничений может привести к сокращению доступного трафика. Рекомендуем
                                      вводить малые ограничения, например минимального ограничения скорости в 0.1 Мбит/сек
                                      достаточно для качественной загрузки большинства сайтов. Настройки индивидуальны!</p>
                                    </table>

                                   </td>
                                   </tr>




                              <tr bgcolor="#AFE5C6" align="center">
                              <td><b>Использование реальной мышки</b></td>
                              </tr>
                              <tr bgcolor="white">
                                 <td>
                                <p>Для того, чтобы мышка начала работать необходимо также включить эту функцию и в настройках шаблона!</p>
                                    <table align="center" border="0">
                                       <tr>
                                          <td colspan="2">Функция включает возможность использования реальных движения мышки<BR>
                                          Она резко уменьшает кол-во доступного трафика до 2000-3000 уников/в сутки</td>
                                       </tr>
                                    <tr>
                                    <td><input name="mouse" <?php if (isset($output2['mouse']) && $output2['mouse'] == '1') {echo 'checked';}?>  value="1" id="mouse" type="checkbox"></td>
                                    <td><label for="mouse">Разрешить использование реальной мышки</label></td>
                                    </tr>
                                   </table>
                                   <div id="mouse_error" style="color: red; display:none; text-align: center;">При активации мышки, пожалуйста активируйте дополнительно эту опцию в параметрах вашего шаблона, <br />а также добавьте команды, которые эту опцию будут использовать!</div>
                                   <script>
                                   $('[name="mouse"]').on('change', function(){
                                     if( $(this).prop('checked') ){
                                       $('#mouse_error').show();
                                     }else{
                                       $('#mouse_error').hide();
                                     }
                                   }).triggerHandler('change');
                                   </script>
                                   </td>
                                   </tr>




                              <tr align="center" bgcolor="#AFE5C6">
                                 <td><b>Геотаргетинг посетителей (ведется по гео базе IP адресов)</b></td>
                              </tr>
                              <tr bgcolor="white">
                                 <td>
                                    Геотаргетинг позволяет получать посетителей из нужных стран и городов. Выборка по областям возможна для Украины и России.<br>
                                    <input name="ggeo" value="<?=Html::encode($output2['ggeo'] ?? '') ?>" type="hidden"><input name="rgeo" value="<?=Html::encode($output2['rgeo'] ?? '') ?>" type="hidden">
                                    <center><input name="geo" <?php if (isset($output2['geo']) && $output2['geo'] == '1') {echo 'checked';}?>  value="1" id="geochk" onclick="geoclick(1);alert('Изменение гео влияет на стоимость показа шаблона, после изменения проверьте новую стоимость показа!');"  type="checkbox"> <label for="geochk">Использовать геотаргетинг</label></center>
                                    <div id="geoset" style="display: block;">
                                       <p>Показать: <a href="javascript:doall(0);">Только активные</a> <a href="javascript:doall(1);">Все</a><br>
                                          Сортировка: <a href="javascript:dosort(0);">Количество посетителей</a> <a href="javascript:dosort(1);">Название</a>
                                       </p>
                                       <center>
                                          <div id="geo1"><a href="javascript:doupdate();alert('Изменение гео влияет на стоимость показа шаблона, после изменения проверьте новую стоимость показа!');">Изменить</a></div>
                                       </center>
                                    </div>
                                 </td>
                              </tr>
                              <tr bgcolor="white">
                                 <td>
                                    <p>Если вы хотите изменить технические параметры (например ссылки, браузеры, ключевые слова и тп.) воспользуйтесь
                  <?=Html::a('редактированием', ['robot-edit', 'id'=>$jetid], ['title' => 'редактировать шаблон']) ?>
									технических параметров ( или иконкой
                  <?=Html::a('<img src="/images/code_services.png" alt="редактирование технических параметров" />', ['robot-edit', 'id'=>$jetid], ['title' => 'редактировать шаблон']) ?>  у нужной настройки). Обращаем Ваше внимание, что не все настройки доступны для редактирования, только те настройки, которые создавались через генератор шаблонов!</p>
                                    <center><button<?=($block_field? ' data-need-confirm="1"':'')?> class="new-2" type="submit" id="save">Сохранить параметры</button></center>
                                 </td>
                              </tr>
                           </tbody>
                        </table>
                     </form>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>

<div class="loading-overlay">
   <div id="loader-image" class="loading"></div>
   <span id="loader-text">Загрузка...</span>
   <a href="#" id="loader-close">закрыть</a>
</div>
