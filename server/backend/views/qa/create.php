<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\comm\CommQa */

$this->title = 'Create Comm Qa';
$this->params['breadcrumbs'][] = ['label' => 'Comm Qas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="comm-qa-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
