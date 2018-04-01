<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\comm\CommProduct */

$this->title = '添加商品';
$this->params['breadcrumbs'][] = ['label' => '商品管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="comm-product-create">


    <?php 
    $storageList = [$modelStorage,$modelStorage,$modelStorage,$modelStorage];
    echo $this->render('_form', [
        'model' => $model,
        'storageList' => $storageList,
    ])
    ?>

</div>
