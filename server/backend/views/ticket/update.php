<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\comm\CommTicket */

$this->title = '优惠劵-更新';
$this->params['breadcrumbs'][] = ['label' => '优惠劵', 'url' => ['index']];
?>
<div class="comm-ticket-update">


    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
