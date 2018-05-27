<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\comm\CommOrderRefundLogSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="comm-order-refund-log-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'order_id') ?>

    <?= $form->field($model, 'storage_id') ?>

    <?= $form->field($model, 'refound') ?>

    <?= $form->field($model, 'expressage_status') ?>

    <?php // echo $form->field($model, 'expre_company') ?>

    <?php // echo $form->field($model, 'expressage_num') ?>

    <?php // echo $form->field($model, 'mobile') ?>

    <?php // echo $form->field($model, 'price') ?>

    <?php // echo $form->field($model, 'content') ?>

    <?php // echo $form->field($model, 'admin_id') ?>

    <?php // echo $form->field($model, 'admin_nickname') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
