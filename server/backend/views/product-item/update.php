<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\comm\CommProductItem */

$this->title = '修改类别: ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Comm Product Items', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="comm-product-item-update">


    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
