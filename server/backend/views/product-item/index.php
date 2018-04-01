<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\comm\CommProductItemSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '类别管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="comm-product-item-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('新增', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'id',
            'title',
            [
                'attribute' => 'icon',
                'format' => 'raw',
                'value' =>
                function($model) {
                    return    $str .= Html::a('图片', $model->icon, ["target" => "_blank"]);
                },
            ],
            [
                'attribute' => 'info',
                'format' => 'raw',
                'value' =>
                function($model) {
                    $info = json_decode($model->info, true);
                    return implode(',', $info);
                },
            ],
            'sort',
            'updated_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
