<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Paymentsinfo */

$this->title = Yii::t('app', 'Create Paymentsinfo');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Paymentsinfos'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="paymentsinfo-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
