<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Coderton */

$this->title = 'Создание команды';
$this->params['breadcrumbs'][] = ['label' => 'Coderton', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="coderton-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
