<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\CommOrder */

$this->title = '订单详情';
$this->params['breadcrumbs'][] = ['label' => '订单列表', 'url' => ['index']];
?>
<div class="comm-order-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
