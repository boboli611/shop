<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\comm\CommQa */

$this->title = "";
$this->params['breadcrumbs'][] = ['label' => '留言', 'url' => ['index']];
?>
<div class="comm-qa-view">


    <p>
        <?= Html::a('首页', ['index'], ['class' => 'btn btn-primary']) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            [
                'attribute' => 'user_id',
                'value' =>
                function($model) {
                    $userModel = common\models\user\User::findOne($model->user_id);
                    return $userModel->username;
                }
            ],
            'content',
            [
                'attribute' => 'status',
                'value' =>
                function($model) {
                    return $model->status ? "已读" : "未读";
                }
            ],
            'created_at',
        ],
    ]) ?>

</div>
