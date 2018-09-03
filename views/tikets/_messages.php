<?php

use yii\helpers\Html;
use app\models\Conf;

$userName = $model->userInfo->fio;
if( empty($userName) ){
  $userName = $model->userInfo->login;
}
if( $model->is_support ){
  $userName = Conf::getParams('admin_tikets_name');
}
?>
<? if(!$model->parent_id): ?>
<div class="tiktitlebox">
  <div class="tiktitleboxcont">
    <b><tit2><?=Html::encode($model->title) ?><tit2></b>
    <dat2><?=$model->date ?></dat2>
    <br>
  </div>
</div>
<? endif; ?>
<div class="tikmessagetitle">
  <div class="tikmessagetitlecont">
    <? if( !$model->is_support ): ?>
    <use><?=$userName  ?></use>
    <? else: ?>
    <adm><?=$userName ?></adm>
    <? endif; ?>
    <dat><?=$model->date ?></dat>
  </div>
</div>
<div class="tikmessagetext">
  <div class="tikmessagetextcont">
    <?php
    if( !empty($model->info) ){
      echo '<b>Информация для связи:</b> '.Html::encode($model->info).'<br /><br />
      <b>Доп. информация:</b> '.Html::encode($model->message).'<br />';
    }else{
      echo Html::encode($model->message);
    }
    ?>
    <?php
    $files = $model->filesList;
    if(!$model->parent_id && !empty($files)): ?>
    <div class="attachments">
      <hr />
      <b>Прикрепленные файлы:</b>
      <ol type="1">
        <?php
        foreach( $files as $file ){
          echo '
          <li>
          '.Html::a(Html::encode($file['old']), ['/tikets/file', '_' => $file['new']]).'
          </li>';
        }
        ?>
      </ol>
    </div>
    <? endif; ?>
  </div>
</div>
