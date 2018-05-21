<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $model common\models\CommOrder */

$this->title = "订单详情";
$this->params['breadcrumbs'][] = ['label' => 'Comm Orders', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$productInfo = [];
?>
<div class="comm-order-view">


    <p>
        <?= Html::a('修改', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('首页', ['index'],['class' => 'btn btn-primary'] ) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'order_id',
            [
                'attribute' => 'user_id',
                'format' => 'raw',
                'filter' => false,
                'value' =>
                function($model) {
                    return $model->username . "[{$model->user_id}]";
                },
            ],
            'product_id',
            [
                'attribute' => 'total',
                'format' => 'raw',
                'filter' => false,
                'value' =>
                function($model) {
                    return $model->total/ 100;
                },
            ],
           [
                'attribute' => 'address',
                'format' => 'raw',
                'filter' => false,
                'value' =>
                function($model) {
                    $address = json_decode($model->address,true);
                    return $address['region'] . $address['address'];
                },
            ],
           [
                'attribute' => 'status',
                'format' => 'raw',
                'filter' => Html::dropDownList("CommOrderSearch[status]", $params['status'], common\models\comm\CommOrder::$payName, ['prompt' => '全部', "class" => "form-control", 'style' => "width:100px;"]),
                'value' => function($model) {
                    return common\models\comm\CommOrder::$payName[$model->status];
                    },
            ],      
            'expressage',
            'content',
            'updated_at',
            'created_at',
                            
            [
                'attribute' => '',
                'label' => 'aaa',
                'format' => 'raw',
                'filter' => Html::dropDownList("CommOrderSearch[status]", $params['status'], common\models\comm\CommOrder::$payName, ['prompt' => '全部', "class" => "form-control", 'style' => "width:100px;"]),
                'value' => function($model) {
                    return common\models\comm\CommOrder::$payName[$model->status];
                    },
            ],      
        ],
    ]) ?>

    
    <?=
    GridView::widget([
        'dataProvider' => $product,
        'summary'=> '商品信息',
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'id',
                'format' => 'raw',
                'filter' => false,
                'value' =>
                function($model) {
                    return $model->id;
                },
            ],
                         [
                'attribute' => 'id',
                'label' => "名称",
                'format' => 'raw',
                'filter' => false,
                'value' =>
                function($model) {
                    global $productInfo;
                    $productInfo = \common\models\comm\CommProductionStorage::getInfoById($model->product_id);
                    return $productInfo['title'];
                },
            ],
                        [
                'attribute' => 'id',
                'format' => 'raw',
                'label' => "封面图",
                'filter' => false,
                'value' =>
                function($model) {
                    global $productInfo;
                    $cover = json_decode($productInfo['cover'], true);
                    $cover = $cover[0];
                    return "<img src='{$cover}' width='100px \'/>";
                },
            ],
            
        ],
    ]);
    ?>
</div>
