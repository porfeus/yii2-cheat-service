<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Antifraud */

$this->title = 'Создать запись';
$this->params['breadcrumbs'][] = ['label' => 'Антифрод', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="antifraud-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
