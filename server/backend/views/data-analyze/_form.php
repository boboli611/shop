<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\data\DataAnalyze */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="data-analyze-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'visit_user')->textInput() ?>

    <?= $form->field($model, 'visit_num')->textInput() ?>

    <?= $form->field($model, 'cart_num')->textInput() ?>

    <?= $form->field($model, 'pay_num')->textInput() ?>

    <?= $form->field($model, 'date')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
