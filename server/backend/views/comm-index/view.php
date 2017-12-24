<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\comm\CommIndex */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => '详情', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="comm-index-view">

    <p>
        <?= Html::a('修改', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('删除', ['delete', 'id' => $model->id], [
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
            'product_id',
            'updated_at',
            'created_at',
        ],
    ]) ?>

</div>
