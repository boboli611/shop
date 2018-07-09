<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\comm\CommOrderRefundLog */

$this->title = '修改金额';
$this->params['breadcrumbs'][] = ['label' => '退款订单', 'url' => ['index']];
//$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
//$this->params['breadcrumbs'][] = 'Update';
?>
<div class="comm-order-refund-log-update">


    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
