<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\comm\CommProduct */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="comm-product-form" style="width: 1000px;">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'desc')->widget(\yii\redactor\widgets\Redactor::className(), [ 
        'clientOptions' => [ 
            'imageManagerJson' => ['/redactor/upload/image-json'], 
            'imageUpload' => ['/redactor/upload/image'], 
            'fileUpload' => ['/redactor/upload/file'], 
            'lang' => 'zh_cn', 
            'plugins' => ['clips', 'fontcolor','imagemanager']
        ],
        
    ]) ?>

    <?= $form->field($model, 'cover')->fileInput() ?>
    
    <?= Html::hiddenInput("cover_path", $model->cover)?>

    <?= $form->field($model, 'price')->textInput() ?>

    <?= $form->field($model, 'sell')->textInput() ?>

    <?= $form->field($model, 'count')->textInput() ?>

    <?= $form->field($model, 'status')->dropDownList(['0'=>'下架','1'=>'上架'], ['style'=>'width:120px', "value" => $model->status])->label("状态") ?>  
    
    <?php //echo  $form->field($model, 'update_time')->textInput() ?>

    <?php //echo $form->field($model, 'create_time')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '添加' : '修改', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
