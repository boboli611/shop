<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\CommOrder */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Comm Orders', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="comm-order-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('更新', ['update', 'id' => $model->order_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('首页', ['index'],['class' => 'btn btn-primary'] ) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'order_id',
            'user_id',
            'product_id',
            'price',
            'pay_price',
            'num',
            'address',
            'status',
            'expressage',
            'content',
            'updated_at',
            'created_at',
        ],
    ]) ?>

</div>
