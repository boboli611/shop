<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\comm\CommBanner */

$this->title = '广告位-修改';
$this->params['breadcrumbs'][] = ['label' => '广告位', 'url' => ['index']];
//$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
//$this->params['breadcrumbs'][] = 'Update';
?>
<div class="comm-banner-update">


    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
