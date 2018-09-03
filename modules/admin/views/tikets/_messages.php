<?php

use yii\helpers\Html;
use app\models\Conf;
use yii\helpers\Url;

$this->registerCss("
.attachments{
	margin-top: 20px;
}
.attachments ol{
	font-size: 1em;
	margin-left: 0;
	padding-left: 20px;
	margin-top: 5px;
}
.attachments a{
	font-size: 0.9em;
}
.attachments b{
	font-size: 0.9em;
}
");

$userName = $model->userInfo->fio;
if( empty($userName) ){
  $userName = $model->userInfo->login;
}
if( $model->is_support ){
  $userName = Conf::getParams('admin_tikets_name');
}
?>
<div class="panel <?=$model->is_support? 'panel-danger':($model->parent_id?'panel-info':'panel-primary') ?>">
	<a style="display: block; text-decoration: none" target="_blank" class="panel-heading" href="/admin/users?UsersSearch[search_value]=<?=$model->userInfo->id?>&UsersSearch[search_type]=user">
    <span style="float: right"><?=$model->date ?></span>
    <? if( !$model->is_support ): ?>
    <?=$userName?>
		<span style="margin-left: 50px;"><small>ID: <?=$model->userInfo->id?>, логин: <?=$model->userInfo->login?>, e-mail: <?=$model->userInfo->email?></small></span>
    <? else: ?>
    <?=$userName ?>
    <? endif; ?>
	</a>
  <div class="panel-body" style="word-break: break-all">
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
