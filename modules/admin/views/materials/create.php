<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Materials */

$this->title = Yii::t('app', 'Create Materials');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Materials'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="materials-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
