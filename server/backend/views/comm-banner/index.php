<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\comm\commBannerSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '广告管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="comm-banner-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?php echo Html::a('新增', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
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
                'attribute' => 'img',
                'format' => 'raw',
                'filter' => false,
                'value' =>
                function($model) {
                    $imgs = [];
                    $model->img = json_decode($model->img, true);
                    foreach ($model->img as $val) {
                        $imgs[] = "<a href='{$val}'>查看图片</a>";
                    }
                    return implode("&nbsp;", $imgs);
                },
                    ],
                    [
                        'attribute' => 'position',
                        'format' => 'raw',
                        'filter' => false,
                        'value' =>
                        function($model) {
                            return \common\models\comm\CommBanner::$postions[$model->position];
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
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update} ',
                'buttons' => [],
                'header' => '操作',
                'urlCreator' => function ($action, $model, $key, $index) {
                    switch ($action) {
                        case 'view':
                            return '/comm-banner/view?id=' . $model->id;
                            break;
                        case 'update':
                            return '/comm-banner/update?id=' . $model->id;
                            break;
                    }
                },
            ],
                ],
            ]);
            ?>
</div>
