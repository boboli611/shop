<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\CommOrderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '订单列表';
$this->params['breadcrumbs'][] = $this->title;
$params = Yii::$app->request->queryParams['CommOrderSearch'];
?>
<div class="comm-order-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
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
                'attribute' => 'order_id',
                'options' => [
                    'width' => '200px'
                ],
                'format' => 'raw',
                'value' =>
                function($model) {
            return $model->order_id;
        },
            ],
            [
                'attribute' => 'user_id',
                'format' => 'raw',
                'filter' => false,
                'value' =>
                function($model) {
                    return $model->username . "[{$model->user_id}]";
                },
            ],
            [
                'attribute' => 'pay_price',
                'format' => 'raw',
                'filter' => false,
                'value' =>
                function($model) {
                    return $model->sumPayPrice / 100;
                },
            ],
            [
                'attribute' => 'expressage',
                'format' => 'raw',
                'filter' => false,
                'value' =>
                function($model) {
                    return $model->expressage;
                },
            ],
            // 'num',
            // 'address',
            [
                'attribute' => 'status',
                'format' => 'raw',
                'filter' => Html::dropDownList("CommOrderSearch[status]", $params['status'], common\models\comm\CommOrder::$payName, ['prompt' => '全部', "class" => "form-control", 'style' => "width:100px;"]),
                'value' => function($model) {
                    return common\models\comm\CommOrder::$payName[$model->status];
                    },
            ],
            [
                'attribute' => 'refund',
                'format' => 'raw',
                'filter' => Html::dropDownList("CommOrderSearch[refund]", $params['refund'], common\models\comm\CommOrder::$refund, ['prompt' => '全部', "class" => "form-control", 'style' => "width:100px;"]),
                'value' => function($model) {
                    return common\models\comm\CommOrder::$refund[$model->refund];
                    },
            ],                
            // 'expressage',
            [
                'attribute' => 'content',
                'format' => 'raw',
                'filter' => false,
                'value' =>
                function($model) {

                    $str = mb_substr($model->content, 0, 8);
                    return strlen($model->content) == strlen($str) ? $str : $str . "...";
                },
            ],
            [
                'attribute' => 'updated_at',
                'format' => 'raw',
                'filter' => false,
                'value' =>
                function($model) {
                    return $model->updated_at;
                },
            ],
            [
                'attribute' => 'created_at',
                'format' => 'raw',
                'filter' => false,
                'value' =>
                function($model) {
                    return "<a href='/comm-order/refund?id={$model->order_id}' target='_blank'>退款</>";
                },
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {update} ',
                'buttons' => [],
                'header' => '操作',
                'urlCreator' => function ($action, $model, $key, $index) {
                    switch ($action) {
                        case 'view':
                            return '/comm-order/view?id=' . $model->order_id;
                            break;
                        case 'update':
                            return '/comm-order/update?id=' . $model->order_id;
                            break;
                    }
                },
            ],
        ],
    ]);
    ?>
</div>
