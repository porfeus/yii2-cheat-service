<?php

use yii\helpers\Html;
?>
<div class="tiketblockcont">
    <div class="tiketblockcontleft">
        <?=Html::a(Html::encode($model->title), ['view', 'id'=>$model->id], ['style' => !$model->readed?'color: red':'']) ?>
    </div>
    <div class="tiketblockcontright">
        <span style="margin-right:10px;"><?=$model->date?></span>
        <? if( Yii::$app->controller->action->id == 'index' ): ?>
        <?=Html::a(
          Html::img('/images/close.png', [
              'title'=>'Закрыть тикет', 'width'=>15, 'height'=>20]),
          ['close', 'id'=>$model->id], ['style' => !$model->readed?'':'color: red']) ?>
        <? endif; ?>
    </div>
</div>
