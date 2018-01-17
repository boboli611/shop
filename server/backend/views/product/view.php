<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\comm\CommProduct */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Comm Products', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$model->price = $model->price / 100;
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
            'price',
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
        ],
    ]) ?>

</div>
