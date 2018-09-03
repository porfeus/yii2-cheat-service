<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Jettarif */

$this->title = Yii::t('app', 'Create Jettarif');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Jettarifs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="jettarif-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
