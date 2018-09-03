<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\admin\models\PagesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Pages');
$this->params['breadcrumbs'][] = $this->title;


$script = <<< JS
  jQuery(function(){
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
  });
JS;
$this->registerJs($script, yii\web\View::POS_READY);
?>
<div class="pages-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('app', 'Create Pages'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            [
              'attribute' => 'full',
              'format' => 'ntext',
              'value' => function($model){
                return StringHelper::truncate($model->full, 500);
              }
            ],
            'title',
            'meta_key:ntext',
            'description:ntext',
            [
              'attribute' => 'url',
              'label' => 'Url статьи',
              'format' => 'html',
              'value' => function ($model, $key, $index){
                return Html::a(
                    $model->url,
                    Yii::$app->request->hostInfo.'/'.$model->url,
                    ['class' => 'blank']
                );
              }
            ],
            //'date',
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
        'tableOptions' =>[
          'class' => 'table table-striped table-bordered',
          'style' => 'width: 1140px; max-width: none;',
        ]
    ]); ?>
</div>
