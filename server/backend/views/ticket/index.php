<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\comm\CommTicketSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '优惠劵';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="comm-ticket-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('新增', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'id',
            'title',
            [
                'attribute' => 'condition',
                'value' =>
                function($model) {
                    return $model->condition  / 100;
                }
            ],
            [
                'attribute' => 'money',
                'value' =>
                function($model) {
                    return $model->money  / 100;
                }
            ],
            [
                'attribute' => 'index_show',
                'value' =>
                function($model) {
                    return $model->index_show ? "是" : "否";
                }
            ],
                    [
                'attribute' => 'status',
                'value' =>
                function($model) {
                    return $model->status ? "是" : "否";
                }
            ],
            // 'duration',
            // 'status',
            // 'count',
            // 'num',
            // 'updated_at',
            // 'created_at',
            [
                        'class' => 'yii\grid\ActionColumn',
                        'template' => '{update} ',
                        'buttons' => [],
                        'header' => '操作',
                    ],
        ],
    ]);
    ?>
</div>
