<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\comm\CommIndex */

$this->title = '添加';
$this->params['breadcrumbs'][] = ['label' => 'Comm Indices', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="comm-index-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
