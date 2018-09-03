<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Templatesconf */

$this->title = 'Изменение настройки шаблона';
$this->params['breadcrumbs'][] = ['label' => 'Настройки шаблонов', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Изменить';
?>
<div class="templatesconf-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
