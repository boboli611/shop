<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\CommOrder */

$this->title = 'Create Comm Order';
$this->params['breadcrumbs'][] = ['label' => 'Comm Orders', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="comm-order-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
