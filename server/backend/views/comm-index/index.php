<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\comm\CommIndexSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '首页管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="comm-index-index">
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'product_id',
                'format' => 'raw',
                'filter' => false,
                'value' =>
                function($model) {
                    $product = common\models\comm\CommProduct::findOne($model->product_id);
                    return mb_substr($product->title, 0, 10);
                },
            ],
            [
                'attribute' => 'type',
                'format' => 'raw',
                'filter' => Html::dropDownList("type", $_GET['type'], \common\models\comm\CommProductRecommend ::$location, ['prompt' => '推荐位置', "class" => "recommend form-control", "id" => $model->id]),
                'value' =>
                function($model) {
                    $location = \common\models\comm\CommProductRecommend::$location;
                    return $location[$model->type];
                },
            ],
            [
                'attribute' => 'created_at',
                'format' => 'raw',
                'filter' => false,
                'value' =>
                function($model) {
                    return $model->created_at;
                },
            ],
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]);
    ?>

    <div style="width: 100%;height: 5px;background-color: #cccccc; margin: 20px 0;"></div>
    <h3> 首页管理</h3>
    <?=
    GridView::widget([
        'dataProvider' => $productDataProvider,
        'filterModel' => $productSearchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'id',
                'headerOptions' => [
                    'width' => '10px'
                ],
                'format' => 'raw',
                'value' =>
                function($model) {
            return$model->id;
        },
            ],
            [
                'attribute' => 'title',
                'headerOptions' => [
                    'width' => '150px'
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
                'filter' => false,
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
                        'filter' => false,
                        'value' =>
                        function($model) {
                            return $model->price / 100;
                        }
                    ],
                    'sell',
                    /*
                      [
                      'attribute' => 'count',
                      'filter' => false,
                      'format' => 'raw',
                      'headerOptions' => [
                      'width' => '350px'
                      ],
                      'value' =>
                      function($model) {
                      $storage = common\models\comm\CommProductionStorage::find()->where(['product_id' => $model->id])->all();
                      $str = "";
                      foreach($storage as $val){
                      $str .= sprintf("%s|%s|%d &nbsp;", $val->style, $val->size, $val->num);
                      }
                      return $str;
                      }
                      ], */
                    [
                        'attribute' => 'status',
                        'value' =>
                        function($model) {
                            return $model->status ? "上架" : "下架";
                        }
                    ],
                    [
                        'attribute' => 'updated_at',
                        'format' => 'raw',
                        'filter' => false,
                        'headerOptions' => [
                            'width' => '150px'
                        ],
                        'value' =>
                        function($model) {
                    $recommend = \common\models\comm\CommProductRecommend::$location;
                    return Html::dropDownList("recommend", '', $recommend, ['prompt' => '推荐位置', "class" => "recommend form-control", "id" => $model->id]);
                }
                    ],
                ],
            ]);
            ?>
</div>
<script src="/assets/4d7e46b8/jquery.js"></script>
<script>
    $(function () {
        $(".recommend").change(function () {
            if ($(this).val() == 0) {
                return;
            }
            location.href = "/comm-index/create?type=" + $(this).val() + "&id=" + $(this).attr("id")
        })
    });
</script>

