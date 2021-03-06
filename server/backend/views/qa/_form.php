<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\comm\CommQa */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="comm-qa-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'username')->textInput()->label("账号") ?>

    <?= $form->field($model, 'password_hash')->textInput(['maxlength' => true])->label("密码") ?>

    <?= $form->field($model, 'email')->textInput()->label("邮箱地址") ?>


    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '保存' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
