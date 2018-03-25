<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\comm\CommProduct */

$this->title = '编辑: ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Comm Products', 'url' => ['index']];
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
