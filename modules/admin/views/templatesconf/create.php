<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Templatesconf */

$this->title = 'Создать настройку шаблона';
$this->params['breadcrumbs'][] = ['label' => 'Настройки шаблонов', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="templatesconf-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
