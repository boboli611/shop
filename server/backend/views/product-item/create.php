<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\comm\CommProductItem */

$this->title = '新增类别';
$this->params['breadcrumbs'][] = ['label' => 'Comm Product Items', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="comm-product-item-create">


    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
