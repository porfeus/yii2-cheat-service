<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\models\Tikets;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\admin\models\TiketsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Тикеты';
$this->params['breadcrumbs'][] = $this->title;


$script = <<< JS
  jQuery(function(){
      jQuery('a.blank').attr('target', '_blank');
  });
JS;
$this->registerJs($script, yii\web\View::POS_READY);
?>
<div class="tikets-index">

    <h1><?= Html::encode($this->title) ?>

      <span style="float: right">

        <?
        echo Html::beginForm(Url::canonical(), 'GET');
        echo Html::dropDownList(
              'status',
              Yii::$app->request->get('status', Tikets::STATUS_NEW),
              array(
                Tikets::STATUS_NEW => 'Новые',
                Tikets::STATUS_READED => 'Отвеченные',
                Tikets::STATUS_ARCHIVED => 'Архивные'
              ),
              ['class'=>'form-control', 'onchange'=>'this.form.submit()']
           );
        echo Html::endForm();
        ?>
      </span>
    </h1>

    <?= Html::beginForm(['delete'], 'post') ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
              'class' => 'yii\grid\SerialColumn',
              'headerOptions' => [
                'style' => 'width: 45px;'
              ],
            ],

            //'id',
            //'parent_id',
            //'date',
            [
              'attribute' => 'login',
              'label' => 'Логин',
              'headerOptions' => [
                'style' => 'width: 180px;'
              ],
              'format' => 'html',
              'value' => function ($model, $key, $index){
                return Html::a(
                    $model->userInfo->login,
                    ['users/update', 'id' => $model->userInfo->id],
                    ['class' => 'blank']
                );
              }
            ],
            //'is_support',
            'title',
            //'message:ntext',
            //'readed',
            //'answered',
            //'archived',

            [
              'attribute' => 'date',
              'label' => 'Дата создания',
              'headerOptions' => [
                'style' => 'width: 180px;'
              ],
            ],

            [
              'class' => 'yii\grid\ActionColumn',
              'header' => 'Действия',
              'headerOptions' => [
                'style' => 'width: 90px;'
              ],
              'template' => '{view} {close}',
              'buttons' => [
                'view' => function ($url, $model, $key) {
                    return Html::a('[Просмотр]', $url);
                },
                'close' => function ($url, $model, $key) {
                    if( Yii::$app->request->get('status') != 2 ){
                      return Html::a('[Закрыть]', $url);
                    }
                },
              ],
            ],
            [
              'class' => 'yii\grid\CheckboxColumn',
              'header' => Html::checkbox('0', false, ['onclick'=>'for(var i=0; i< this.form.elements.length; i++){var elem = this.form.elements[i]; if(elem.type==\'checkbox\' && elem.name != 0){elem.checked=this.checked;}}']),
              'headerOptions' => [
                'style' => 'width: 45px;'
              ],
            ],
        ],
    ]); ?>
    <?= Html::submitButton('Удалить отмеченное', ['class' => 'btn btn-danger']) ?>
    <?= Html::endForm() ?>
</div>
