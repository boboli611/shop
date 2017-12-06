<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\comm\CommProductItem */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => '预览', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="comm-product-item-view">

    <p>
        <?= Html::a('修改', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('删除', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => '确定删除?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'title',
            'icon',
            'sort',
            'updated_at',
            'created_at',
        ],
    ]) ?>

</div>
