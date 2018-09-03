<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\widgets\Alert;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;
use app\widgets\ViewsComponents;
use app\models\Conf;


AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="/images/favicon.ico" rel="shortcut icon" type="image/x-icon">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>



 <div class="wrapper">
      <div class="head">
         <div id="head1">
            <div class="headlogo">
               <div class="logo" title="Go-ip.ru"><a href="/"><img src="/images/logo.png"></a></div>
            </div>
            <div class="headcontact">
               <div class="contact">
                 <span class="contact_head_title">Служба<br>поддержки</span>
                  <strongimg>
                    <img src="/images/icq.png" style="widtg:18px;height:18px">
                    <strong><?=Conf::getParams('icq');?></strong>
                  </strongimg>
                  <strongimg style="margin-left:20px">
                    <img src="/images/phone.png" style="widtg:18px;height:18px">
                    <strong><?=Conf::getParams('adminmail');?></strong>
                  </strongimg>
                  <strongimg style="margin-left:20px">
                    <img src="/images/skype.png" style="widtg:18px;height:18px">
                    <strong><a rel="nofollow" title="Добавить в скайп" href="skype:<?=Conf::getParams('adminskype');?>?add"><?=Conf::getParams('adminskype');?></a></strong>
                  </strongimg>
               </div>
            </div>
         </div>
         <div id="head2">
            <div class="bbtitle">
               <div class="bb1">Биржа трафика</div>
               <div class="bb2"></div>
            </div>
   
            <div class="htext">
               <div class="htextcont">
                  Качественный трафик для сайтов.
                  Клики, переходы, любые действия.
                  <div class="htext2">
                     <div class="htextcont2">Выборка стран и городов.</div>
                  </div>
               </div>
            </div>
            <div class="hmenu">
              <a href="#" class="mobile_menu_start"><span></span><p>Меню</p></a>
               <ul class="reset">
			      <li class="headmenub"><a href="/">Главная</a></li> 
                  <li class="headmenubst"></li>
                  <li class="headmenub"><a href="/materials">Блог</a></li>
                  <li class="headmenubst"></li>
                  <li class="headmenub"><a href="/faq">F. A. Q.</a></li>
                  <li class="headmenubst"></li>
                  <li class="headmenub"><a href="/contact">Контакты</a></li>
               </ul>
            </div>
         </div>
       <div id="head3">
            <div class="headtexts">
               <div class="headtextslog">
                  <p class="text-p">Мы работаем длительное время и заботимся о постоянных клиентах!</p>
                  <p class="text-p">Постоянно развиваемся и улучшаем свой сервис и качество!</p>
               </div>
            </div>
            <?php if (\Yii::$app->user->isGuest):?>
            <div class="regbutton">
               <ul id="iconc" class="reset">
                  <li class="regb">
                    <a href="/reg">

                      <span>Регистрация</span>
                    </a>
                  </li>
               </ul>
            </div>
            <?php endif;?>
         </div>


            <?php if (\Yii::$app->user->isGuest):?>
         <div id="head4">
           <form id="w0" action="/login" method="post">
<input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken(); ?>">
            <div class="loginblock">
               <div class="logintitleblock">

                  Вход для партнеров
               </div>
               <div class="loginblockcont">
<div class="logininp1">
                        <div class="logininplut">
                            <input class="lnplut" type="text" name="LoginForm[username]" size="15" placeholder="Логин" value="" size="18" />

                        </div>
</div>

 <div class="logininp2">
                        <div class="logininplut">
                            <input class="lnplut" type="password" name="LoginForm[password]" size="15" placeholder="Пароль" value="" size="18" />
                        </div>
 </div>


               </div>
            </div>
            <div class="log_in">
            <div class="log_in_button">
            <div class="login_on_b">
            <div class="on">
            <input type="submit" class="pointer" name="login-button" value="">

            </div>
            </div>
            </div>
            </div>
            </form>
            <div class="rel_pass">
               <div class="log_in_button">
                  <ul class="reset">
                     <li class="rbutton"><a href="/newpass">восстановление пароля</a></li>
                  </ul>
               </div>
            </div>
         </div>
       <?php else: ?>
         <a class="bb4-s" href="/api">Войти в кабинет</a>
       <?php endif; ?>
      </div>
      <div class="contentblockall">
        <div class="contentblock">
          <div class="contentblockblock">
      <p><font size="7" color="#6C6C6C">Более <b>50 000 уникальных</b> хостов в сутки!</font><br />
      <font size="4" color="#6C6C6C">Клики по вашим баннерам и рекламе, настраивамые шаблоны и действия, узкая выборка геотаргетинга, удобная панель управления, статистика переходов,
      критерии трафика и многое другое - ждут Вас на Go-Ip.ru</font></p>
          </div>
        </div>
      </div>


        <?= Alert::widget() ?>
        <?= $content ?>

 
	


 <?php echo ViewsComponents::widget(['view' => 'footer']); ?>
</div>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
