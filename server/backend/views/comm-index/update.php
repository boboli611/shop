<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\comm\CommIndex */

$this->title = '编辑: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Comm Indices', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="comm-index-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
