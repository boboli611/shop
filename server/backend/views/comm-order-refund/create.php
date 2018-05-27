<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\comm\CommOrderRefundLog */

$this->title = 'Create Comm Order Refund Log';
$this->params['breadcrumbs'][] = ['label' => 'Comm Order Refund Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="comm-order-refund-log-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
