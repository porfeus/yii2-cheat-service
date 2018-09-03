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
use app\models\Tikets;
use yii\widgets\Menu;

//jQuery(document).ready
$script = <<< JS
  jQuery('.reset > .headmenub:not(:last)').after('<li class="headmenubst"></li>');
JS;
$this->registerJs($script);

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

    <?php if(!\Yii::$app->user->isGuest):?>
   <div id="myModal" class="reveal-modal">
         <h1></h1>
         <br />
         <p></p>
         <a class="close-reveal-modal">&#215;</a>
      </div>
    <?php endif;?>

      <div class="wrapperuser">


      <div class="userlogo">

         <div class="userheadlogo">
            <a href="/"><img src="/images/logo.png" title="Система автоматизированного трафика Go-Ip.ru"></a>
         </div>

         <div class="userheadcontacts">
            <div class="usercontact">
              <span class="usercontact_title">Служба поддержки</span>
               <strongimg>
                  <img src="/images/icq.png" style="widtg:18px;height:18px">
                    <strong><?=Conf::getParams('icq');?></strong>
               </strongimg>
               <strongimg>
                <img src="/images/phone.png" style="widtg:18px;height:18px">
                    <strong><?=Conf::getParams('adminmail');?></strong>
               </strongimg>
               <strongimg>
                 <img src="/images/skype.png" style="widtg:18px;height:18px">
                    <strong><a rel="nofollow" title="Добавить в скайп" href="skype:<?=Conf::getParams('adminskype');?>?add"><?=Conf::getParams('adminskype');?></a></strong>
               </strongimg>
            </div>
         </div>


      </div>


      <div class="userhead">


         <div class="userip">
            <div class="useripblock">
            Вы авторизованы на сайте под логином  <?php echo Yii::$app->user->identity->login; ?>, ваш IP адрес: <?php echo $_SERVER['REMOTE_ADDR'];?>, E-mail: <?php echo Yii::$app->user->identity->email; ?>
            </div>
         </div>


         <div class="usermenublock">
            <div class="usermenucont">
              <a href="#" class="mobile_menu"><span></span><p>Меню</p></a>

              <?php
                $news_add = (!Yii::$app->user->identity->news? '<sup style="color: red">НОВОЕ</sup>':'');
                $tikets_add = (Tikets::countUnreadTikets()? '<sup style="color: red">НОВОЕ</sup>':'');

                echo Menu::widget([
                  'activateItems' => true,
                  'activeCssClass' => 'active',
                  'encodeLabels' => false,
                  'options' => [
                    'class' => 'reset',
                  ],
                  'itemOptions' => [
                    'class' => 'headmenub',
                  ],
                  'items' => [
                      ['label' => 'Баланс <img src="/images/icon_balans.png">', 'url' => ['site/balans']
                      , 'active' => in_array(Yii::$app->controller->action->id, array('transactions', 'balans'))],
                      ['label' => 'Профиль', 'url' => ['site/profile']],
                      ['label' => 'Панель', 'url' => ['site/api']
                      , 'active' => function(){
                        if( in_array(Yii::$app->controller->action->id,
                        ['api', 'edit', 'delete', 'schedule']) ) return true;
                        if( strstr(Yii::$app->request->url, '/robot/edit/') ) return true;
                        return false;
                      }],
                      ['label' => 'Создать шаблон <img src="/images/icon_zakaz.png">', 'url' => ['site/order']
                      , 'active' => function(){
                        if( Yii::$app->controller->action->id == 'order' ) return true;
                        if( Yii::$app->request->url == '/add-id' ) return true;
                        if( strstr(Yii::$app->request->url, '/robot/add-') ) return true;
                        return false;
                      }],
                      ['label' => 'F. A. Q', 'url' => ['site/pages', 'url' => 'faq']
                      , 'active' => Yii::$app->request->url == '/faq'],
                      ['label' => 'Блог', 'url' => ['site/materials']],
                      ['label' => 'Тикеты'.$tikets_add, 'url' => ['tikets/index']],
                      ['label' => 'Новости'.$news_add, 'url' => ['site/news']],
                      ['label' => 'Выход <img src="/images/icon_out.png">', 'url' => ['site/logout']
                      , 'template' => '<a href="{url}" data-method="post">{label}</a>'],
                  ],
              ]);
              ?>

            </div>
         </div>
      </div>





     <?= Alert::widget() ?>
        <?= $content ?>

 <?php echo ViewsComponents::widget(['view' => 'footer']); ?>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
