<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\data\DataAnalyze */

$this->title = 'Update Data Analyze: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Data Analyzes', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="data-analyze-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
