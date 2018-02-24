<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\CommOrder */

$this->title = 'Update Comm Order: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Comm Orders', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="comm-order-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
