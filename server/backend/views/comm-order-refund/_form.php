<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\comm\CommOrderRefundLog */
/* @var $form yii\widgets\ActiveForm */
$model->price = $model->price / 100;
?>

<div class="comm-order-refund-log-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'order_id')->textInput(['readonly' => "readonly"]) ?>

    <?php //echo $form->field($model, 'refound')->textInput() ?>

    <?= $form->field($model, 'price')->textInput() ?>

    <?php //$form->field($model, 'admin_id')->textInput() ?>

    <?php //$form->field($model, 'admin_nickname')->textInput() ?>

    <?php //$form->field($model, 'updated_at')->textInput() ?>

    <?php //$form->field($model, 'created_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : '更新', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
