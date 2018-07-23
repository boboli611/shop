<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\data\DataAnalyze */

$this->title = 'Create Data Analyze';
$this->params['breadcrumbs'][] = ['label' => 'Data Analyzes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="data-analyze-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
