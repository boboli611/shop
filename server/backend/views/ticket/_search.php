<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\comm\CommTicketSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="comm-ticket-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'condition') ?>

    <?= $form->field($model, 'money') ?>

    <?= $form->field($model, 'type') ?>

    <?= $form->field($model, 'index_show') ?>

    <?php // echo $form->field($model, 'duration') ?>

    <?php // echo $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'count') ?>

    <?php // echo $form->field($model, 'num') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
