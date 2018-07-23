<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\comm\CommOrderRefundLogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = "退单列表";
$this->params['breadcrumbs'][] = $this->title;

$params = Yii::$app->request->queryParams['CommOrderRefundLogSearch'];
?>
<div class="comm-order-refund-log-index">

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'order_id',
                'format' => 'raw',
                'value' =>
                function($model) {
                    return "<a href='/comm-order/view?order_id=". $model->order_id ."' target='_blank'> ".$model->order_id."</a>";
                },
            ],
            [
                'attribute' => 'storage_id',
                'format' => 'raw',
                'filter' => false,
                'value' =>
                function($model) {
                    $product = common\models\comm\CommProductionStorage::getInfoById($model->storage_id);
                    return $product['title'];
                },
            ],
            [
                'attribute' => 'refound',
                'format' => 'raw',
               'filter' => Html::dropDownList("CommOrderRefundLogSearch[refound]", $params['refound'], common\models\comm\CommOrder::$refund, ['prompt' => '全部', "class" => "form-control", 'style' => "width:100px;"]),
                'value' =>
                function($model) {
                    return \common\models\comm\CommOrder::$refund[$model->refound];
                },
            ],
            [
                'attribute' => 'expressage_status',
                'format' => 'raw',
               'filter' => false,
                'value' =>
                function($model) {
                    return \common\models\comm\CommOrderRefundLog::$expressage_status[$model->expressage_status];
                },
            ],            
            [
                'attribute' => 'price',
                'format' => 'raw',
                'filter' => false,
                'value' =>
                function($model) {
                    return ($model->price) / 100;
                },
            ],
            [
                'attribute' => 'expressage_num',
                'format' => 'raw',
                'filter' => false,
                'value' =>
                function($model) {
                    return $model->expressage_num;
                },
            ],
            [
                'attribute' => 'content',
                'format' => 'raw',
                'filter' => false,
                'headerOptions' => [
                    'width' => '250px'
                ],
                'value' =>
                function($model) {
                    return $model->content;
                },
            ],
            [
                'attribute' => 'refound',
                'format' => 'raw',
               'filter' => false,
                'header' => '操作',
                'value' =>
                function($model) {
                    if ($model->refound != \common\models\comm\CommOrder::status_refund_checking){
                        return ;
                    }
                    return "<a href='/comm-order-refund/refund?id={$model->id}&status=1'>退款</a>&nbsp;&nbsp;<a href='/comm-order-refund/refund?id={$model->id}&status=2'>不退款</a>" ;
                },
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {update} ',
                'buttons' => [],
                'header' => '操作',
                
            ],
        ],
    ]); ?>
</div>
