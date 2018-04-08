<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\comm\CommTicket */

$this->title = '优惠劵-新增';
$this->params['breadcrumbs'][] = ['label' => '优惠劵', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="comm-ticket-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
