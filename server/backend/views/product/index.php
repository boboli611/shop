<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\comm\CommProductSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '商品列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="comm-product-index" style="width: 80%">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('新商品', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'id',
            [
                'attribute' => 'title',
                'headerOptions' => [
                    'width' => '50px'
                ],
                'format' => 'raw',
                'value' =>
                function($model) {
                    return mb_substr($model->title, 0, 10);
                },
            ],
            [
                'attribute' => 'cover',
                'format' => 'raw',
                'value' =>
                function($model) {
                    $list = json_decode($model->cover, true);
                    $list = is_array($list) ? $list : [];
                    $str = "";
                    foreach ($list as $val) {
                        $str .= Html::a('图片', $val, ["target" => "_blank"]) . " ";
                    }
                    return $str;
                },
                    ],
                    [
                        'attribute' => 'price',
                        'value' =>
                        function($model) {
                            return $model->price / 100;
                        }
                    ],
                    'sell',
                    'count',
                    [
                        'attribute' => 'status',
                        'value' =>
                        function($model) {
                            return $model->status ? "上架" : "下架";
                        }
                    ],
                    'updated_at',
                    'created_at',
                    ['class' => 'yii\grid\ActionColumn'],
                ],
            ]);
            ?>

</div>
