<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use app\widgets\Alert;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use app\components\ApiFunctions;
use app\models\Conf;
use app\models\Schedule;
use yii\helpers\Url;

$this->title = 'Панель управления';
$this->params['breadcrumbs'][] = $this->title;

$this->registerJsFile('/js/jquery.block-ui.js');
$this->registerJsFile('/js/loader.js');
$this->registerJsFile('/js/api.js');

//stat chart
$this->registerJsFile('//code.highcharts.com/stock/highstock.js');
$this->registerJsFile('//code.jquery.com/ui/1.12.1/jquery-ui.js');
$this->registerJsFile('/js/jquery.ui.datepicker-ru.js');
$this->registerJsFile('//code.highcharts.com/modules/exporting.js');
$this->registerCssFile('//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css');


//Проверяем, отключено ли API
if( Conf::getParams('api_disabled') ){
$api_disabled_message = Conf::getParams('api_disabled_message');
$script = <<< JS
$([
  //'.change_set',
  '.getips',
  '.stat_set',
  '.open-form',
  '.link_display_block',
  '.cred_ops',
  '.clear_stat',
  '.del_stat',
  '#refresh-stat'
].join(','))
.unbind("click")
.off("click")
.on('click', function(e){
  e.preventDefault();
  e.stopPropagation();
  alert("{$api_disabled_message}");
})
.attr('onclick', function(){
  return false;
});
JS;
$this->registerJs($script);
}
//Конец. Проверяем, отключено ли API
?>
<div id="myModalChart" class="reveal-modal large">
  <h1></h1>
  <br />
  <p></p>
  <a class="close-reveal-modal">&#215;</a>
</div>


<div class="contentblockall">
   <div class="contentblock">
      <div class="contentblockblock">
         <div class="contentblocktableblock">
            <div class="contentblocktable">
               <div class="contentblocktablebgtitle">
                  <div class="contentblocktabletitle">
                     <h1>Панель управления</h1>
                  </div>
               </div>
               <div class="contentblocktablebgcontblock">
                 <?= Alert::widget() ?>
                  <div class="contentblocktablebgcont2">
                    <?php
                    //если у юзера нет шаблонов
                    if( empty($userModel->settings) ){
                      echo '
                      <div style="width: 100%;">
                          <div class="alert-danger alert" style="cursor: default">
                           У Вас нет настроек в панели управления, рекомендуем <a href="/add-id">создать их прямо сейчас!</a>
                          </div>
                      </div>';
                    } ?>

                  <?=Conf::getParams('news_user')?>

                  <![CDATA[infos1]]>
				  <![CDATA[infos2]]>
				  <![CDATA[infos3]]>
				  <![CDATA[infos4]]>
                  <![CDATA[infos5]]>

                     <div class="main-error">
                        <div id="loading" style="display: none"></div>

                        <?php if ($userModel->trafbalans == '0'): ?>
                        <br>На балансе нет реалов, рекомендуем <a href="/balans">купить их сейчас</a><br>
                        <?php endif; ?>
                        <?php
                        /*
                        //если у юзера нет шаблонов
                        if( empty($userModel->settings) ){
                          echo '<br>У Вас нет настроек, рекомендуем <a href="/add-id">создать</a> их прямо сейчас!';
                        }
                        */
                        ?>
                     </div>
                     <table width="955" style="border: none;">
                        <tr>
                           <td>
                              <span class="hh_grey">Баланс реалов: <b><?=$userModel->trafbalans;?></b> реалов</span><br/>
                              <span class="hh_grey">Основной баланс: <b><?=$userModel->balans;?></b> руб.</span>
                           </td>
                        </tr>
                     </table>

                     <br>

                     <hr>

                     <br>
                     <?php if (!empty($userModel->settings)): ?>
                     <div class="table-container">
                       <table id="records_api" bgcolor="d6d6d6" border="0" cellpadding="5" cellspacing="1" width="987">
                          <tr>
                             <td bgcolor="white" rowspan="2" class="delete" width="20px;"><img title="Удалить настройку" src="/images/delete_id.png"/></td>
                             <td align="center" bgcolor="white" rowspan="2" width="15px;"><input type="checkbox" id="check_all"/></td>
                             <td bgcolor="white" rowspan="2" class="id" width="30px;"><span class="info_table" title="ID настройки это уникальный номер шаблона по всей системе, он необходим для уникализации всех пользователей"> ID </span></td>
                             <td bgcolor="white" rowspan="2" class="name" width="40px;"><span class="info_table" title="Название настройки можно изменить, оно не влияет на работоспособность,изменение производится на вкладке редактировать => изменить название сайта (вы можете прописать понятное Вам)"> Название </span></td>
                             <td bgcolor="white" rowspan="2" class="traf" width="70px;"><span class="info_table" title="Кол-во реалов на настройке отражает ее баланс, если реалы есть - трафик будет поступать, если они закончились - трафик идти не будет"> Реалы</span></td>
                             <td bgcolor="white" rowspan="2" class="ppok" width="120px;"><span class="info_table" title="Дата и время последней поступившей единицы трафика, Вы можете проверить идет ли к Вам трафик или нет по данному виду шаблона"> Последний показ</span></td>
                             <td bgcolor="white" rowspan="2" class="ppok" width="180px;"> Настройки</td>
                             <td bgcolor="white" colspan="3" class="t0" width="80px;" nowrap> Статистика показов настройки</td>
                             <td bgcolor="white" rowspan="2" width="130px;">
                             <span class="info_table" title="В данных полях можно отредактировать параметры трафика и пополнить/снять трафик с любого шаблона">Операции</span></td>
                          </tr>
                          <tr>
                             <td bgcolor="white" class="t1" width="40px;">
                             <span class="info_table" title="Статистика поступившего трафика на шаблон за текущий час, каждый час оно сбрасывается">Час</span></td>
                             <td bgcolor="white" class="t" width="60px;">
                             <span class="info_table" title="Статистика трафика за весь текущий день, отражает сумму всех показов по настройке за день">День</span></td>
                             <td bgcolor="white" class="t3" width="70px;">
                             <span class="info_table" title="Статистика поступившего трафика за все время работы шаблона, при условии что Вы его не обнуляли вручную">Всего</span></td>
                          </tr>
                          <?php foreach ($userModel->settings as $key => $item): ?>
                          <tr>

                             <td align="center" bgcolor="white" class="delete">
                             <a href="<?=Url::to(['site/delete', 'id' => Html::encode($key)])?>" style="text-decoration: none;">
                             <img title="Удалить настройку" class="delimage" src="/images/delete_id.png"/></a></td>

                             <td align="center" bgcolor="white" class="check">
                             <input type="checkbox" class="check_all" data-id="<?=Html::encode($key);?>" name="set[<?=Html::encode($key);?>]"/></td>

                            <td bgcolor="white" class="id"><?=Html::encode(trim($key));?></td>
                             <td bgcolor="white" class="name" width="40px;"><?=Html::encode($item->username); ?></td>
                             <td bgcolor="white" class="traf">
                                <?php if ($item->traf < 0): ?>
                                <?=Html::encode(round(($item->traf),2)); ?>
                                <span class="error"
                                   title="Знак минус обозначает, что система перекрутила показы на несколько единиц, такое могло случиться, если накрутка шла без ограничений по времени. Для возобновления накрутки, просто пополните баланс реалами, тем самым минусовой баланс будет компенсирован!">
                                <strong style="cursor:pointer">?</strong>
                                </span>
                                <?php else: ?>
                                <?=number_format($item->traf, 2, '.', ''); ?>
                                <?php endif; ?>
                             </td>
                             <td bgcolor="white" class="traf"><?php
                             //последний показ не равен нулю, выводим дату
                             if( $item->last != '0' ){
                               echo date("Y-m-d H:i:s", $item->last);
                             }
                             //иначе их нет
                             else{
                               echo 'показов не было';
                             }
                             ?></td>
                             <td bgcolor="white" align="center">
                                <?php
                                   if ($item->view == 1) {
                                   $shed_isset = Schedule::find()->where(['site_id' => $key, 'disabled' => 0])->one();
                                   ?>
                                <a href="javascript:void(0);" title="<?php

                                if ($item->set_pause + (3600 * Conf::getParams('set_pause')) > time()) {

                                  if( Conf::getParams('set_pause') >= 1 ){
                                    $timeLeft = ceil(($item->set_pause + (3600 * Conf::getParams('set_pause')) - time()) / 3600). " ч.";
                                  }else{
                                    $timeLeft = ceil(($item->set_pause + (3600 * Conf::getParams('set_pause')) - time()) / 60). " мин.";
                                  }

                                  echo "Заказ изменения настроек возможен через ~" . $timeLeft;
                                } else {
                                  echo 'Помощь от техподдержки в изменении настроек';
                                }

                                ?>" onclick="change_set(<?=Html::encode($key); ?>);" style="text-decoration: none;" class="change_set">
                                <img style="width: 30px;"src="/images/set<?=Html::encode((($item->set_pause + (3600 * Conf::getParams('set_pause'))) <= time() ? "" : "_off")); ?>.png"/></a>
                                <a href="<?=Url::to(['schedule', 'id'=>Html::encode($key)])?>" style="text-decoration: none;"
                                   title="Расписание <?=Html::encode($shed_isset ? "включено" : "выключено"); ?>">
                                <img style="width: 30px;"
                                   src="/images/sched<?=Html::encode($shed_isset ? "" : "_off"); ?>.png"/>
                                </a>
                                <a onclick="getips(<?=Html::encode($key);?>)" href="javascript:void(0);" style="text-decoration: none;" class="getips">
                                <img title="Статистика IP адресов" style="width: 30px;" src="/images/ip.png"/>
                                </a>
                                <a href="/robot/edit/<?=Html::encode($key);?>" style="text-decoration: none;">
                                <img title="Настройки шаблона" style="width: 30px;" src="/images/code_services.png"/>
                                </a>
                                <a onclick="if(!confirm('При копировании шаблона произойдет создание \nкопии шаблона, настроек и расписания. \nСоздать копию?'))return false;" href="copy/<?=Html::encode($key);?>" style="text-decoration: none;">
                                <img title="Скопировать настройку" style="width: 30px;" src="/images/id_copy.png"/>
                                </a>
                                <?php
                                   }
                                   else {
                                   	echo '<a href="archive/'.Html::encode(trim($key)).'" title="Восстановить настройку"><img src="/images/archive.png" align="absmiddle" />восстановить</a> <br/>';
                                   }
                                   ?>

                             </td>
                             <td bgcolor="white" class="t1"><?php
                             $hourly = Html::encode(abs($item->ch));
                             ?>
                             <a class="stat_set" href="javascript:void(0);"  onclick="stat_set('<?=Html::encode(trim($key))?>', 1)"><?=$hourly; ?></a>
                             </td>
                             <td bgcolor="white" class="t2"><?php
                             $daily = Html::encode(abs($item->d));
                             ?>
                             <a class="stat_set" href="javascript:void(0);"  onclick="stat_set('<?=Html::encode(trim($key))?>', 2)"><?=$daily; ?></a>
                             </td>
                             <td bgcolor="white" class="t3"><?=Html::encode(abs($item->oll)); ?></td>
                             <td bgcolor="white">

                                <?=Html::a('редактировать', ['edit', 'id'=>trim($key)])?> <br/>
                                <?php
                                   if ($item->view == 1) {
                                   ?>


                                <a href="#" class="open-form">пополнить реалами</a>
                                <form class="form">
                                   К-во <input size="4" class="count-number" value="100" type="number" min="10"/>
                                   <br/>
                                   <input type="submit" class="add-balance" value="Пополнить" data-id="<?=Html::encode(trim($key));?>"/><br/>
                                   <input type="button" class="close-form" value="Отменить"/>
                                   <br/><br/>
                                </form>

                                <a href="#" class="open-form">снять реалы</a>
                                <form class="form">
                                   К-во <input class="count-number" size="4" value="<?=Html::encode(floor($item->traf));?>" type="number" min="10"/>
                                   <br/>
                                   <input type="submit" class="dis-balance" value="Снять" data-id="<?=Html::encode(trim($key));?>"/><br/>
                                   <input type="button" class="close-form" value="Отменить"/>
                                </form>


                                <?php
                                   }
                                   ?>
                             </td>
                          </tr>
                          <?php endforeach; ?>
                       </table>
                     </div>
                     <?php endif; ?>

                     <?php if ( !empty($userModel->settings) ):?>
                     <br>
                     <a href="javascript:void(0);" class="link_display_block cred_ops" onclick="cred_ops();">Операции с реалами</a>	|
                     <a href="javascript:void(0);" class="link_display_block clear_stat" onclick="clear_stat();">Сброс статистики показов</a> |
                     <?php $can_request = ApiFunctions::checkTime("refresh_stat");?>

                     <?php if (!$can_request): ?>
                       <div id="refresh-stat-countdown-block" style="display:inline-block;">Ожидайте
                        <?php
                           $last_request = ApiFunctions::getLastRequest(Yii::$app->user->id, "refresh_stat");
                           $time_limit = ApiFunctions::getTimeLimit(Yii::$app->user->id, "refresh_stat");
                           $minutes = floor(abs($last_request + $time_limit - time()) / 60);
                           $seconds = abs($last_request + $time_limit - time()) % 60;
                           ?>
                        <span id="countdown-minutes"><?=Html::encode($minutes);?></span> мин. и <span id="countdown-seconds"><?=Html::encode(($seconds));?></span> сек.
                        <input id="stat-countdown" type="hidden" data-seconds="<?=Html::encode($minutes * 60 + $seconds);?>"/>
                     </div>
                     <?php endif; ?>

                    <div id="refresh-stat-block" style="display:<?php echo !$can_request ? 'none' : 'inline-block'; ?>;">
                        <a href="#" id="refresh-stat" style="color:green;">Обновить статистику</a>
                        <i class="loader" style="display: none"></i>
                        <span id="stat-success" style="display:none"><b>обновлено</b></span>
                        <span id="stat-error" style="display:none"><font color="red">ошибка попробуйте <a href="javascript:void(0);">еще</a></font>

                        </span>
                     </div>

                     | <a href="javascript:void(0);" class="link_display_block del_stat" style="color:red;" onclick="del_stat();">Удаление настройки</a>

                     <hr>
                     <?php endif;?>

                  <![CDATA[footer-api]]>

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
