<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\comm\CommIndex */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="comm-index-form">

    <?php $form = ActiveForm::begin(); ?> 

    <?= $form->field($model, 'product_id')->textInput() ?>

    <?php // $form->field($model, 'updated_at')->textInput(['maxlength' => true]) ?>

    <?php //$form->field($model, 'created_at')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '保存' : '修改', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
