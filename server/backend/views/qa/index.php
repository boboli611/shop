<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\comm\CommQaSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '用户反馈';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="comm-qa-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            [
                'attribute' => 'user_id',
                'value' =>
                function($model) {
                    $userModel = common\models\user\User::findOne($model->user_id);
                    return $userModel->username;
                }
            ],
            [
                'attribute' => 'content',
                'headerOptions' => [
                    'width' => '150px'
                ],
                'format' => 'raw',
                'value' =>
                function($model) {
                return mb_substr($model->content, 0, 30);
                },
            ],
            [
                'attribute' => 'status',
                'value' =>
                function($model) {
                    return $model->status ? "已读" : "未读";
                }
            ],
            // 'updated_at',
            'created_at',
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} ',
                'buttons' => [],
                'header' => '操作',
            ],
        ],
    ]); ?>
</div>
