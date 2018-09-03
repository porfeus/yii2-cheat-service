<?php

use yii\helpers\Html;
use yii\widgets\ListView;
use yii\bootstrap\ActiveForm;

$this->title = Html::encode($tiket->title);
$this->params['breadcrumbs'][] = ['label' => 'Тикеты', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tikets-view">

    <h1><?= $this->title ?>
      <span style="float: right; font-size: 14px;">Тикет создан: <?= Html::encode($tiket->date) ?></span>
    </h1>

<? if( !$tiket->archived ):?>
    <p>
        <?= Html::a('Закрыть', ['close', 'id' => $tiket->id], ['class' => 'btn btn-danger']) ?>
    </p>
<? endif; ?>

    <?= ListView::widget([
       'dataProvider' => $dataProvider,
       'itemView' => '_messages',
    ]);
    ?>


    <div class="row">
        <div class="col-lg-12">
          <?php $form = ActiveForm::begin(); ?>
          <?= $form->field($model, 'message')->textarea(['rows' => 5]) ?>
          <div class="form-group">
            <?= Html::submitButton('Отправить сообщение', ['class' => 'btn btn-primary']) ?>
          </div>
          <?php ActiveForm::end(); ?>
        </div>
    </div>

</div>
