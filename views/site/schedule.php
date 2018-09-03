<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use app\widgets\Alert;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use app\components\ApiFunctions;
use app\models\Conf;
use yii\helpers\Url;

$this->title = 'Расписание показов';
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$this->registerJsFile('/js/shedule.js');
$this->registerJsFile('/js/charts/highcharts.js');
$this->registerJsFile('/js/charts/highcharts-more.js');
$this->registerJsFile('/js/charts/draggable-points.js');

$this->registerCss('
.bt th { text-align: center; background-color: #EFEFEF; padding: 5px !important; font-weight: bold;border: 1px #ccc solid; }
.bt td{ border: 1px #ccc solid; padding: 2px !important; }
.nobt td { border: 0px !important; }
.clear_td { color: red !important; text-decoration: none !important }
.do_week_copy_result { color: green; font-weight: bold; display: none; }
.close1 { width: 16px; vertical-align: middle; cursor: pointer; }
.close2 { width: 16px; vertical-align: middle; cursor: pointer; }
');


?>
<div class="contentblockall">
    <div class="contentblock">
        <div class="contentblockblock">
            <div class="contentblocktableblock">
                <div class="contentblocktable">
                    <div class="contentblocktablebgtitle">
                        <div class="contentblocktabletitle"><h1>Расписание для ID:<?=Html::encode($id)?></h1></div></div>
                    <div class="contentblocktablebgcontblock">
                    <div class="contentblocktablebgcont2">

<?php if(Yii::$app->controller->id == 'site'){ ?>
<![CDATA[shedule]]>
<?php }else{ $this->registerJsFile('/js/charts/chart.js');
} ?>


    <script>
        var datas = [<?=$datas_start;?>];

        $(function() {
            create_chart(datas, "Понедельник");
            setTimeout(function() {
                recalc();
            }, 100);
        });
    </script>

    <div class="table-container">
      <center>
        <div id="chart" style="width: 950px"></div>
      </center>
    </div>
        <?php
        if ( !empty($site) ) {
        ?>
    <hr />
        <center>
          <?php
          if ( !$site['disabled'] ) {
          ?>
            <a href="javascript:void(0);" style="color: orange; font-weight: bold;" onclick=" document.location.href='<?=Url::to(['schedule-disable', 'disable' => 1, 'id' => $site['id']])?>';">Отключить расписание</a>
          <?php
          }else{
          ?>
          <a href="javascript:void(0);" style="color: green; font-weight: bold;" onclick=" document.location.href='<?=Url::to(['schedule-disable', 'disable' => 0, 'id' => $site['id']])?>';">Включить расписание</a>
          <?php
          }
          ?>
        </center>
    <hr />
        <center>
            <a href="javascript:void(0);" style="color: red; font-weight: bold;" onclick="if(confirm('Удалить расписание?')) document.location.href='<?=Url::to(['schedule-delete', 'id' => $site['id']])?>';">Удалить расписание</a>
        </center>
        <?php
        }
        ?>

    <hr />
    <center>
        День недели:
        <select class="week_day_select">
            <option value="1">Понедельник</option>
            <option value="2">Вторник</option>
            <option value="3">Среда</option>
            <option value="4">Четверг</option>
            <option value="5">Пятница</option>
            <option value="6">Суббота</option>
            <option value="0">Воскресенье</option>
        </select>

        <hr />
        <strong>Автозаполнение:</strong>
        <a href="javascript:void(0);" class="link_display_block" onclick="autofill(1);">День 24ч</a> |
        <a href="javascript:void(0);" class="link_display_block" onclick="autofill(2);">Утренняя активность</a> |
        <a href="javascript:void(0);" class="link_display_block" onclick="autofill(3);">Вечерняя активность</a>

        <hr />
        Копировать <b>эти настройки</b> в:

        <select class="week_day_copy">
            <option value="-1">Все дни</option>
            <option value="1">Понедельник</option>
            <option value="2">Вторник</option>
            <option value="3">Среда</option>
            <option value="4">Четверг</option>
            <option value="5">Пятница</option>
            <option value="6">Суббота</option>
            <option value="0">Воскресенье</option>
        </select>
        <input type="button" class="do_week_day_copy" value="Ok" /> <span class="do_week_copy_result">Выполнено!</span>

        <hr />
        <?= Html::beginForm('', 'post', ['id'=>'editForm']); ?>
        <?php
        for ($ii=0; $ii<7; $ii++) {
        ?>
            <div id="table_<?=Html::encode($ii)?>" class="tablediv table-container" style="<?=Html::encode($ii != 1 ? "display: none;" : "");?>">
            <table width="100%" cellpadding="0" cellspacing="0" class="bt scheduletable">
                <thead>
                    <tr>
                        <td style="font-size: 14px">
                            <b title="Данное число берется из графика, и отражает сумму всех выставленных полей">Всего показов в день:</b> <span class="all_pokaz"><?=Html::encode(intval($all_pokaz[$ii] ?? 0));?></span> <br/>(примерно из графика)
                        </td>
                        <td align="center" colspan="5">
                            Показов в день не более <input required style="width: 50px;" type="text" class="pkd" value="<?=Html::encode($_pkhr[$ii][24] ?? '')?>"  name="pkd[<?=Html::encode($ii)?>]" /><br />
                            <a href="javascript:void(0);" onclick="fill_intervals();">Заполнить интервалы</a><br />
                            Изменение интервалов: <input required style="width: 40px;" value="0" type="text" class="percent" />%<br />
                            Изменение внутри интервалов: <input required style="width: 40px;" type="text" class="percent_inside" value="0" />%<br />
                        </td>
                    </tr>
                    <tr>
                        <th>Время</th>
                        <th>Показов в час (<a class="clear_td" href="javascript:void(0);" onclick="clear_td(1, this);" title="Очистить поля">X</a>)</th>
                        <!--<th>Время показа(<a class="clear_td" href="javascript:void(0);" onclick="clear_td(2, this);" title="Очистить поля">X</a>)</th>-->
                        <th>Интервал (секунды) (<a class="clear_td" href="javascript:void(0);" onclick="clear_td(2, this);" title="Очистить поля">X</a>)</th>
                        <th>Интервалы в часе (сек.)(<a class="clear_td" href="javascript:void(0);" onclick="clear_td(3, this);" title="Очистить поля">X</a>)</th>
                        <!--изменена функция очистки полей с 2-ая на 2-ую)-->
                    </tr>
                </thead>
                <tbody>
                    <?php
                        for ($i=0; $i<24; $i++) {
                            ?>
                                <tr data-hour="<?=Html::encode($i)?>" class="tbltr">
                                    <td class="tbltd" align="center"><?=Html::encode($i." - ".($i+1))?> ч.</td>
                                    <td class="tbltd" align="center">
                                        <input required type="text"  style="width: 50px;" value="<?=Html::encode($_pkhr[$ii][$i] ?? '');?>" class="<?=Html::encode($i)?>_pkhr" data-type="pkhr" name="pkhr[<?=Html::encode($ii)?>][<?=Html::encode($i)?>]" style="width: 30px;" />
                                        <img title="Не показывать в этот час" class="close1" src="/images/close1.png" onclick="$(this).prev().val('-1');recalc();" />

                                    </td>
                                    <!--<td class="tbltd" align="center">
                                    <input type="text" style="width: 40px;" class="<?=Html::encode($i)?>_pktm_0" value="<?=Html::encode($_pktm[$ii][$i][0] ?? '');?>" name="pktm[<?=Html::encode($ii)?>][<?=Html::encode($i)?>][0]" /> - <input  style="width: 40px;" value="<?=Html::encode($_pktm[$ii][$i][1] ?? '');?>" class="<?=Html::encode($i)?>_pktm_1" type="text" name="pktm[<?=Html::encode($ii)?>][<?=Html::encode($i)?>][1]" style="width: 30px;" />
                                    </td>-->
                                    <td class="tbltd" align="center">
                                        <table class="nobt" style="border: 0px;" width="90%" border="0" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td align="right">Мин-ый:</td>
                                                <td align="center"><input required style="width: 40px;" value="<?=$tmlmin[$ii][$i][0] ?? '';?>" data-type="tmlmin" type="text" class="<?=Html::encode($i)?>_tmlmin_0" name="tmlmin[<?=Html::encode($ii)?>][<?=Html::encode($i)?>][0]" style="width: 40px;" /> - <input required value="<?=Html::encode($tmlmin[$ii][$i][1] ?? '');?>" data-type="tmlmin_1" class="<?=Html::encode($i)?>_tmlmin_1" type="text" name="tmlmin[<?=Html::encode($ii)?>][<?=Html::encode($i)?>][1]" style="width: 40px;" /></td>
                                                <td rowspan="2"><input type="checkbox" class="<?=Html::encode($i)?>_tmlrefresh" <?=$tmlrefresh[$ii][$i] ?? '' ? 'checked="checked"' : '';?> name="tmlrefresh[<?=Html::encode($ii)?>][<?=Html::encode($i)?>]" value="1" id="tml_<?=Html::encode($i)?>" /> <label for="tml_<?=Html::encode($i)?>">Изм.сейчас</label></td>
                                            </tr>
                                            <tr>
                                                <td align="right">Макс-ый:</td>
                                                <td align="center"><input required value="<?=Html::encode($tmlmax[$ii][$i][0] ?? '');?>" data-type="tmlmax" class="<?=Html::encode($i)?>_tmlmax_0" type="text" name="tmlmax[<?=Html::encode($ii)?>][<?=Html::encode($i)?>][0]" style="width: 40px;" /> - <input required style="width: 40px;" value="<?=Html::encode($tmlmax[$ii][$i][1] ?? '');?>" data-type="tmlmax_1" class="<?=Html::encode($i)?>_tmlmax_1" type="text" name="tmlmax[<?=Html::encode($ii)?>][<?=Html::encode($i)?>][1]" style="width: 40px;" /></td>
                                                <td rowspan="2"></td>
                                            </tr>
                                        </table>
                                    </td>
                                    <td class="tbltd" align="center">
                                        <table class="nobt" style="border: 0px;" width="90%" border="0" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td align="right">Мин-ый:</td>
                                                <td align="center"><input value="<?=Html::encode($tmlminc[$ii][$i][0] ?? '0');?>" class="<?=Html::encode($i)?>_tmlminc_0" type="text" name="tmlminc[<?=Html::encode($ii)?>][<?=Html::encode($i)?>][0]" style="width: 30px;" /> - <input value="<?=Html::encode($tmlminc[$ii][$i][1] ?? '0');?>" class="<?=Html::encode($i)?>_tmlminc_1" type="text" name="tmlminc[<?=Html::encode($ii)?>][<?=Html::encode($i)?>][1]" style="width: 30px;" /></td>
                                            </tr>
                                            <tr>
                                                <td align="right">Макс-ый:</td>
                                                <td align="center"><input value="<?=Html::encode($tmlmaxc[$ii][$i][0] ?? '0');?>" class="<?=Html::encode($i)?>_tmlmaxc_0" type="text" name="tmlmaxc[<?=Html::encode($ii)?>][<?=Html::encode($i)?>][0]" style="width: 30px;" /> - <input value="<?=Html::encode($tmlmaxc[$ii][$i][1] ?? '0');?>" class="<?=Html::encode($i)?>_tmlmaxc_1" type="text" name="tmlmaxc[<?=Html::encode($ii)?>][<?=Html::encode($i)?>][1]" style="width: 30px;" /></td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            <?php
                        }
                    ?>
                </tbody>
            </table>
            </div>
        <?php
        }
        ?>
        <hr />
        День недели:
        <select class="week_day_select">
            <option value="1">Понедельник</option>
            <option value="2">Вторник</option>
            <option value="3">Среда</option>
            <option value="4">Четверг</option>
            <option value="5">Пятница</option>
            <option value="6">Суббота</option>
            <option value="0">Воскресенье</option>
        </select>
        <hr />
        Копировать <b>эти настройки</b> в:

        <select class="week_day_copy">
            <option value="-1">Все дни</option>
            <option value="1">Понедельник</option>
            <option value="2">Вторник</option>
            <option value="3">Среда</option>
            <option value="4">Четверг</option>
            <option value="5">Пятница</option>
            <option value="6">Суббота</option>
            <option value="0">Воскресенье</option>
        </select>
        <input type="button" class="do_week_day_copy" value="Ok" /> <span class="do_week_copy_result">Выполнено!</span>
        <hr />
        <?php
            if ( !empty($mysites) ) {
        ?>
        <fieldset style="width: 50%"> <legend>Скопировать это расписание в:</legend>
        <?php

            foreach ($mysites as $row) {
                ?>
                <input type="checkbox" id="mysite<?=Html::encode($row['id']);?>" name="copy[<?=Html::encode($row['id']);?>]" value="1" />
                <label for="mysite<?=Html::encode($row['id']);?>"><?=$row['username'];?> (<?=Html::encode($row['id']);?>)</label><br />
                <?
            }
        ?>
        </fieldset>
        <hr />
        <?php
            }
        ?>

        <button class="new-2" type="submit" id="dodsave" name="dosave" value="1">Сохранить</button>
        <?= Html::endForm(); ?>
    </center>

</div>
</div>

</div>
</div>
</div>
</div>
</div>
