<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\comm\CommProduct */

$this->title = '编辑商品 ';
$this->params['breadcrumbs'][] = ['label' => '商品管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = '编辑';
?>
<div class="comm-product-update">


    <?php
    echo $this->render('_form', [
        'model' => $model,
        'storageList' => $modelStorage,
        'modelRecommend' => $modelRecommend,
    ]) ?>

</div>
