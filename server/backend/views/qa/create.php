<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\comm\CommQa */

$this->title = '添加用户';
$this->params['breadcrumbs'][] = ['label' => '首页', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="comm-qa-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
