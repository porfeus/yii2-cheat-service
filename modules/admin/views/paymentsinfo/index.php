<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\admin\models\PaymentsinfoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Paymentsinfos');
$this->params['breadcrumbs'][] = $this->title;


$script = <<< JS
jQuery('a.blank').attr('target', '_blank');
JS;
$this->registerJs($script, yii\web\View::POS_READY);
?>
<div class="paymentsinfo-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('app', 'Create Paymentsinfo'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            'payment',
            [
              'attribute' => 'userid',
              'format'=>'html',
              'value' => function($model){
                return '<a class="blank" href="/admin/users?UsersSearch[search_value]='.$model->userid.'&UsersSearch[search_type]=user">'.$model->userid.'</a>';
              }
            ],
            [
              'attribute' => 'cost',
              'value' => function($model){
                return $model->cost.' '.$model->currency;
              }
            ],
            'time',
            //'metod:ntext',
            [
              'attribute' => 'type',
              'filter' => $searchModel->typeVariants,
              'value' => function ($model, $key, $index){
                return $model->colouredTypeText;
              },
              'format'=>'html',
            ],
            'note:ntext',
            [
              'attribute' => 'status',
              'filter' => array("OK-PAY"=>"Оплачен", "NO"=>"Ожидает"),
              'value' => function ($model, $key, $index){
                return $model->colouredStatusText;
              },
              'format'=>'html',
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
