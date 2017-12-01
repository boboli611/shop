<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\comm\CommProduct */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Comm Products', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="comm-product-view">

    <p>
        <?= Html::a('返回列表', ['index'], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('修改', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'title',
            'desc:ntext',
            'cover',
            'price',
            'sell',
            'count',
            'status',
            'updated_at',
            'created_at',
        ],
    ]) ?>

</div>
