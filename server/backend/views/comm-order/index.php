<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\CommOrderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '订单列表';
$this->params['breadcrumbs'][] = $this->title;
$params = Yii::$app->request->queryParams['CommOrderSearch'];
var_dump($params['status']);
?>
<div class="comm-order-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Comm Order', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'id',
            'order_id',
            [
                'attribute' => 'user_id',
                'format' => 'raw',
                'value' =>
                function($model) {
                    $useModel = common\models\user\User::findOne($model->user_id);
                    return $useModel->username . "[{$useModel->id}]";
                },
            ],
            [
                'attribute' => 'product_id',
                'format' => 'raw',
                'value' =>
                function($model) {
                    $product = \common\models\comm\CommProductionStorage::getInfoById($model->product_id);
                    return $product['title'];
                },
            ],
            [
                'attribute' => 'pay_price',
                'format' => 'raw',
                'filter' => false,
                'value' =>
                function($model) {
                    return $model->pay_price / 100;
                },
            ],
            // 'num',
            // 'address',
            [
                'attribute' => 'status',
                'format' => 'raw',
                'filter' =>  Html::dropDownList("CommOrderSearch[status]", $params['status'], common\models\comm\CommOrder::$payName, ['prompt' => '全部', "class" => "form-control "]),
                'value' => function($model) {
                    return common\models\comm\CommOrder::$payName[$model->status];
                },
            ],
            // 'expressage',
            [
                'attribute' => 'content',
                'format' => 'raw',
                'filter' => false,
                'value' =>
                function($model) {
              
                    $str =  mb_substr($model->content, 0, 8);
                    return strlen($model->content) == strlen($str) ? $str : $str."...";
                },
            ],
            'updated_at',
            'created_at',
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]);
    ?>
</div>
