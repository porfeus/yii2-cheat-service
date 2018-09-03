<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Conf */

$this->title = Yii::t('app', 'Create Conf');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Confs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="conf-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
