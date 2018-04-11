<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\comm\CommOrderRefundLog */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Comm Order Refund Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="comm-order-refund-log-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'order_id',
            'refound_id',
            'price',
            'admin_id',
            'admin_nickname',
            'updated_at',
            'created_at',
        ],
    ]) ?>

</div>
