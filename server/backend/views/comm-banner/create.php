<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\comm\CommBanner */

$this->title = '广告位';
$this->params['breadcrumbs'][] = ['label' => '广告位', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="comm-banner-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
