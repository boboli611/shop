<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\CommOrder */

$this->title = '订单详情';
$this->params['breadcrumbs'][] = ['label' => 'Comm Orders', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="comm-order-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
