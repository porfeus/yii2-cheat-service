<?php
/* @var $this yii\web\View */

use app\widgets\Alert;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;


$this->title = 'Баланс';
$this->params['breadcrumbs'][] = $this->title;

$this->registerCss("
.form-group, .form-group *{
  display: inline;
}
");

//jQuery(document).ready
$script = <<< JS
  rezpay();
  fpay();

  var seltarValue = "$model->tarch";
  if( seltarValue ){
    jQuery('#seltar').val(seltarValue).triggerHandler( "change" );
  }
JS;
$this->registerJs($script);
?>



<div class="contentblockall">
    <div class="contentblock">
        <div class="contentblockblock">
            <div class="contentblocktableblock">
                <div class="contentblocktable">
                    <div class="contentblocktablebgtitle">
                        <div class="contentblocktabletitle">
                            <h1>Баланс</h1>
                        </div>
                    </div>
                    <div class="contentblocktablebgcontblock">
                        <div class="contentblocktablebgcont">
                          <?= Alert::widget() ?>
<script>
function skidka(n) {
    var skin = 0;
    return skin;
}
</script>
                            <div id="textn1">На данной странице можно ознакомится с Вашим балансом в системе (баланс в рублях и числом реалов).
                                <br>Чуть ниже, Вы можете купить любое кол-во реалов, используя Ваш баланс в системе.
                            </div>


                            <table width="955">
                                <tr>
                                    <td>

                                        <span class="hh">Основной баланс: <b><?= $userModel->balans; ?></b> руб.</span><br/>
                                        <span class="hh">Баланс реалов: <b><?= $userModel->trafbalans; ?></b> реалов</span><br/>

                                        <hr>
                                        <span class="hh">Личная скидка на покупку: <b><?= $userModel->koef; ?></b> %</span><br/>
                                        <span class="hh">Дополнительные реалы при покупке: <b>+ <?= $userModel->procent; ?></b> %</span><br/>
                                        <span class="hh">Наш курс доллара: <b> <?=round($usd,2);?></b> руб. за 1$</span><br/>

                                        <span class="hh">Историю транзакций аккаунта можно посмотреть на
                                            <a target="_blank" href="transactions">этой странице</a></span>
                                    </td>
                                </tr>
                            </table>



                            <hr>

                            <br>

                            <div id="textn2">Выберите нужный пакет реалов. После покупки кол-во реалов будет зачислено Вам на баланс. <br>
                                Пакеты - стоят дешевле ввиду скидки, для покупки произвольного числа реалов используйте «розничный тариф»</div>


                            <div id="blocktar" >

<?php if ($userModel->pay != 1): ?>
          <div id="w0" class="alert-dismissible alert-danger alert fade in" style="margin-top:50px;">
          После регистрации Вам необходимо активировать аккаунт, иначе он будет удален.<br />
          Неактивные аккаунты удаляются каждый <?=$day_delete;?><br />
          Подтверждение аккаунта производится путем пополнения баланса в системе на любую сумму.
          </div>


<?php endif; ?>


    <h4>Покупка внутренней валюты (реалов)</h4>
    <?php $form = ActiveForm::begin([
      'enableClientValidation' => false,
      'fieldConfig' => [
          'template' => "{input}",
      ],
    ]); ?>
     <p>
     <select id="seltar"  name="BalansForm[tarch]" onchange="ftar();rezpay()">

    <?php

    if($userModel->coef1 != 0){
      $coef1 = $userModel->coef1;

    }
    else{
        $coef1 = $coef_default;

    }


       //цена в рублях за 1к кредитов, с учетом коэфф-та юзера
       //цена в рублях 1к кредитов = доллар * цена долларах за 1к кредитов * коэфф-т юзера
       $priceonerub = $usd*$priceperone*$coef1;


      //разбираем массив тарифов
      foreach ($tarif as $t) {

       //цена за тариф в рублях
       $mcost = $priceonerub*$t->count/1000;

       //цена с учетом скидки за тариф
       $mcost -= $mcost*$t->skidka/100;

       //цена с учетом скидки для юзера
       $mcost = $mcost*($userModel->koef == 0? 1: (100 - $userModel->koef)/100);

       //округляем цену
       $mcost = round($mcost, 0);

 echo '<option '
       . 'traf="'. $t->count .'"  '
       . 'mcost="'. $mcost . '" '
       . 'wosk="'. round(($priceonerub*$t->count/1000-$mcost),0) .'"  '
       . 'value="'. $t->id .''
       . '">'
       . $t->name .' '
       . '('. $t->count .' реалов) = '
       . ''. $mcost .' руб '.
       ($t->skidka > 0? '(скидка '. $t->skidka .'%)': '(нет скидки)').''
       . '</option>';

                            }
  echo '<option  mcost="'.($priceonerub*($userModel->koef == 0? 1: (100 - $userModel->koef)/100)/1000).'"  traf="0" value="rozn">Розничный тариф</option>';
      ?>
     </select>
    </p>


    <div id="mytarifdiv" style="display:none">
      <p>Введите кол-во реалов</p>
      <?= $form->field($model, 'mytarinp')->textInput([
        'onchange'=>'fcalk(this.value);rezpay()',
        'onkeyup'=>'fcalk(this.value);rezpay()',
        'id'=>'mytarinp',
        'class'=>'up',
        'autocomplete' => 'off',
        ]) ?>
    </div>

    <span id="rezpay"></span><br/><br/>

    <p><input type="submit" value="Купить реалы"></p>
    <?php ActiveForm::end(); ?>



    <div id="textn3">Вы можете пополнить баланс на сумму, необходимую для покупки определенного пакета реалов, или вписать свою.</div><br />
    <hr><br />


      <h4>Пополнение рублёвого баланса</h4>
      <?php $form = ActiveForm::begin([
        'id' => 'roboforma',
        'options' => ['name' => 'forma'],
        'enableClientValidation' => false,
        'fieldConfig' => [
            'template' => "{input}",
            'radioTemplate' => '{input}',
        ],
      ]); ?>

      <p><select id="selpay" onchange="fpay()">

      <?php

         //цена в рублях 1к кредитов = доллар * цена долларах за 1к кредитов * коэфф-т юзера
       $priceonerub = $usd*$priceperone*$coef1;

       //цена первого тарифа
       $minimal_price =  round($first_tarif->count*$usd*$priceperone*$coef1/1000, 0);

      //разбираем массив тарифов
      foreach ($tarif as $t) {


       //цена за тариф в рублях
       $mcost = $priceonerub*$t->count/1000;

       //цена с учетом скидки за тариф
       $mcost -= $mcost*$t->skidka/100;

       //цена с учетом скидки для юзера
       $mcost = $mcost*($userModel->koef == 0? 1: (100 - $userModel->koef)/100);

       //округляем цену
       $mcost = round($mcost, 0);

       echo '<option '
       . 'traf="'. $t->count .'"  '
       . 'mcost="'. $mcost . '" '
       . 'wosk="'. round(($priceonerub*$t->count/1000-$mcost),0) .'"  '
       . 'value="'. $t->id .''
       . '">'
       . $t->name .' '
       . '('. $t->count .' реалов) = '
       . ''. $mcost .' руб '.
       ($t->skidka > 0? '(скидка '. $t->skidka .'%)': '(нет скидки)').''
       . '</option>';

      }


      ?>
    </select>
  </p>

  <input type="hidden" id="minp" value="<?=$min_pay_summ?>" />
  <div id="mypaydiv" style="display:block">
    <table  border="0" >
      <tr>
        <td style="width: 15%;">
          <div id="liPs" class="showPreview"><span class="text-p">Или введите сумму сами:</span></div>
        </td>
        <td>
            <input form="roboforma" type="hidden" name="OutSum"  id="sum" class="up" value="<?=$min_pay_summ?>"/>
            <?= $form->field($modelPay, 'ammount')->textInput(['id' => 'sum2', 'class'=>'up', 'onchange'=>'fcalkpay(this.value)', 'onkeyup'=>'fcalkpay(this.value)']) ?>
        </td>
    </tr>
    <tr>
        <td colspan="2"><span class="text-p"><b>Минимум к пополнению <?=$min_pay_summ?>р. </b></span></td>
    </tr>
    <tr>


        <td colspan="2">

          <fieldset class="typo-legend">

            <legend>Способ пополнения</legend>
            <label for="type_1">
              <?= $form->field($modelPay, 'type')->inline()->radio(['uncheck'=>null, 'value'=>'robokassa', 'id'=>'type_1']) ?>
              <img src="/images/robokassa.png" /> (VISA/MasterCard, QIWI, АльфаБанк)
            </label> <br/><br/>


            <label for="type_2">
              <?= $form->field($modelPay, 'type')->inline()->radio(['uncheck'=>null, 'value'=>'webmoney', 'id'=>'type_2']) ?>
              <img src="/images/webmoney.png" /> (WMR)
            </label><br/><br/>

            <label for="type_3">
              <?= $form->field($modelPay, 'type')->inline()->radio(['uncheck'=>null, 'value'=>'interkassa', 'id'=>'type_3']) ?>
              <img src="/images/interkassa.png"/> (Яндекс Деньги, QIWI, WebMoney, Онлайн Банки, Биткоин, LiqPay, Единая Касса, МТС, Билайн, Мегафон, Сбербанк, Промсвязь, АльфаБанк)
            </label> <br/><br/>

            <label for="type_4">
              <?= $form->field($modelPay, 'type')->inline()->radio(['uncheck'=>null, 'value'=>'sprypay', 'id'=>'type_4']) ?>
              <img src="/images/sraypay.png" /> (Яндекс Деньги, QIWI, Онлайн Банки, SMS)
            </label><br/><br/>

        </fieldset>
          </td>
      </tr>
  </table>

                                    <br /><br />
                                    <input type="hidden" name="tarrific" id="tarrific" value="">
                                    <input type="hidden" name="Culture" value="ru">
                                    <input type="button" onclick="fpayinfo1()" value='Пополнить рублевый баланс' >

                                </div>

                                <?php ActiveForm::end(); ?>
                            </div>
                            <div id="blocktarinfo" class="ruls-border">

                                <ul>
                                    Перед покупкой реалов и запуска шаблонов, ознакомьтесь с пояснениями:<br><br>
                                    <u>Для клиентов с панелью вебмастера:</u><br>
                                    <li>В системе имеется широкий выбор гео-таргеттинга (как по городам, так и странам)</li>
                                    <li>В системе насчитывается порядка 100 000 русских и 50 000 украинских посетителей ежесуточно!</li>
                                    <li>Используются реальные просмотры сайтов/страниц, все Mac адреса уникальны</li>
                                    <li>Используются обновленные версии браузеров (IE, Google Chrome, Yandex, Opera и другие),
                                        более 2500 различных версий всех браузеров, имеется возможность только мобильного трафика (платформы андроид, айфон)
                                    </li>
                                    <li>Полная, системная подмена referer-а с возможностью задавать свои ссылки (из соц.сетей, поисковиков, закладок)</li>
                                    <li>Полная уникальность IP у подсетей</li>
                                    <li>Максимально допустимое время просмотра страницы и времени действий 900 сек, минимальное 30 сек</li>
                                    <li>Стоимость каждого выполнения шаблона зависит от настроек гео, времени и всех параметров доступных для изменения</li>
                                    <li>Поддержка от администрации по вопросам настройки шаблонов, кроме голосований и опросов</li>
                                    <li>Техническая поддержка сайта не отвечает за ошибочную настройку шаблона, манипуляции с ним ведущие к полной или частичной трате средств. Вся ответственность за работу шаблонов ложиться на пользователя (включая допущенные ошибки).</li>
                                </ul>

                                <hr>

                                <u>Для клиентов работающих по голосованиям (опросам, рейтингам):</u><br>

                                <ul>
                                    <li>Средний процент учета голосов составляет 70-80% (может быть и ниже, мы за него не отвечаем)</li>
                                    <li>Администрация не компенсирует незасчитанные голоса в Вашем голосовании, вся накрутка на Ваш страх и риск</li>
                                    <li>Вы принимаете на себя риски связанные с: возможной блокировкой, снятием голосов, частичное их не засчитывание</li>
                                    <li>Поддержка от администрации по вопросам настройки голосований и опросов, рейтингов</li>
                                    <li>В системе имеется широкий выбор гео-таргеттинга (как по городам, так и странам)</li>
                                    <li>В системе насчитывается порядка 100 000 русских и 50 000 украинских посетителей ежесуточно!</li>
                                    <li>Используются реальные просмотры сайтов/страниц, все Mac адреса уникальны</li>
                                    <li>Используются обновленные версии браузеров (IE, Google Chrome, Yandex, Opera и другие),
                                        более 2500 различных версий всех браузеров, имеется возможность только мобильного трафика (платформы андроид, айфон)
                                    </li>
                                    <li>Полная, системная подмена referer-а с возможностью задавать свои ссылки (из соц.сетей, поисковиков, закладок)</li>
                                    <li>Полная уникальность IP у подсетей</li>
                                    <li>Максимально допустимое время просмотра страницы и времени действий 900 сек, минимальное 30 сек</li>
                                    <li>Стоимость каждого выполнения шаблона зависит от настроек гео, времени и всех параметров доступных для изменения</li>
                                    <li>Техническая поддержка сайта не отвечает за ошибочную настройку шаблона, манипуляции с ним ведущие к полной или частичной трате средств. Вся ответственность за работу шаблонов ложиться на пользователя (включая допущенные ошибки).</li>
                                </ul>

                                <hr>

                                <u>Для клиентов с двумя панелями:</u><br>
                                <ul>
                                    <li>Все пояснения описанные выше</li>
                                    <li>Поддержка по любым вопросам в системе</li>
                                    <br>
                                    <hr>
                                    <li>Вы соглашаетесь с
                                        <a target="_blank" href="/pravila" title="Правила сайта">правилами сайта</a>,
                                        <a target="_blank" href="/bezopasnost" title="Безопасность">политикой безопасности</a>,
                                        <a target="_blank" href="/platezhi" title="Операции с оплатой">операциями с оплатой</a>
                                    </li>
                                    <li>Вы ознакомились с <a target="_blank" href="/faq">FAQ</a> по системе</li>
                                    <li><font color="red">Вы соглашаетесь с тем, что средства заведенные в сервис обратно не выводятся, и могут быть потрачены только в нем!</font></li>
                                </ul>

                                <hr>

                                <input type="button" onclick="fpayinfo2()" value='Не согласен' >
                                <input type="button" onclick="fpayinfo3()" value='Согласен с правилами' >
                            </div>
                            <br />
                            <hr>
                            <div class="info"></div>
                            <p style="margin-left:15px; font-size:100%;">Обратный обмен реалов на деньги невозможен<br></p>
                            <div class="info"></div>
                            <p style="margin-left:15px; font-size:100%;">На пакеты реалов есть скидки, они выгоднее к покупке<br></p>
                            <div class="info"></div>
                            <p style="margin-left:15px; font-size:100%;">Для покупки произвольного числа реалов используйте "розничный тариф" <br></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
