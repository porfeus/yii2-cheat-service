<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\admin\models\LogsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Logs');
$this->params['breadcrumbs'][] = $this->title;

$this->registerCss("
.break-all{
  word-break: break-all !important;
}
");
$this->registerJsFile('//code.jquery.com/ui/1.12.1/jquery-ui.js');
$this->registerJsFile('/js/jquery.ui.datepicker-ru.js');
$this->registerCssFile('//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css');

$script = <<< JS
    $( ".date-range-selector" ).datepicker({
    dateFormat: "yy-mm-dd"
  });
JS;
$this->registerJs($script);
?>

<div class="clearfix"></div>

<div class="row">
  <div class="col-md-12">
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>
  </div>
</div>

<div class="clearfix"></div>

<div class="logs-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('app', 'Create Logs'), ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('Очистить ЛОГ', ['clear'], [
          'class' => 'btn btn-danger',
          'style' => 'float: right',
          'onclick' => 'if(!confirm(\'Удалить все записи лога?\')) return false;',
        ]) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            [
              'attribute' => 'text',
              'format' => 'html',
              'headerOptions' => [
                'style' => 'width: 865px;',
              ],
              'value' => function($model){
                $value = $model->text.'<br />';
                if( !empty($model->user_id) ){
                  $value.= "<br />ID пользователя: {$model->user_id}";
                }
                if( !empty($model->ip) ){
                  $value.= "<br />IP адрес: {$model->ip}";
                }
                if( !empty($model->browser) ){
                  $value.= "<br />Юзер агент: {$model->browser}";
                }
                if( !empty($model->referer) ){
                  $value.= "<br />REFERER: {$model->referer}";
                }

                return '<div class="break-all">
                '.$value.'
                </div>';
              }
            ],
            [
              'attribute' => 'category',
              'filter' => $searchModel->categories,
              'value' => function($model){
                return $model->categories[$model->category];
              }
            ],
            'date',

            ['class' => 'yii\grid\ActionColumn'],
        ],
        'tableOptions' =>[
          'class' => 'table table-striped table-bordered',
          'style' => 'width: 1140px; max-width: none;',
        ]
    ]); ?>
</div>
