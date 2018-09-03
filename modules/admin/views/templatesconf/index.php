<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\admin\models\TemplatesconfSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Настройки шаблонов';
$this->params['breadcrumbs'][] = $this->title;


$script = <<< JS
jQuery('a.blank').attr('target', '_blank');

jQuery('.disable-button').on('click', function(e){
  e.preventDefault();
  var o = this;
  $(o)
    .removeClass('btn-danger')
    .removeClass('btn-success')
    .addClass('btn-default');
  jQuery.getJSON(this.href).done(function(data){
    if( data.disabled ){
      $(o)
      .text('Включить')
      .removeClass('btn-default')
      .removeClass('btn-danger')
      .addClass('btn-success');
    }else{
      $(o)
      .text('Выключить')
      .removeClass('btn-default')
      .addClass('btn-danger')
      .removeClass('btn-success');
    }
  });
});
JS;
$this->registerJs($script, yii\web\View::POS_READY);
?>
<div class="templatesconf-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Создать настройку шаблона', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'name',
            'value',
            [
              'attribute' => 'url',
              'format' => 'html',
              'value' => function($model){
                return Html::a('/robot/add-'.$model->url, ['/site/robot-add', 'id' => $model->url], ['class' => 'blank']);
              }
            ],
            [
              'label' => 'Действие',
              'format' => 'html',
              'value' => function($model){
                if( !$model->disabled ){
                  return Html::a('Выключить', ['toggle', 'id'=>$model->id], ['class' => 'btn btn-danger disable-button']);
                }else{
                  return Html::a('Включить', ['toggle', 'id'=>$model->id], ['class' => 'btn btn-success disable-button']);
                }
              }
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
