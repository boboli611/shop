<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>
<?php $form = ActiveForm::begin(
        [
            'id' => 'login-form',  
            'options' => ['style'=>'width:50%', 'enctype' => 'multipart/form-data'],  
            'class' => '.form-inline',
        ]); ?>

    <?= $form->field($model, 'name')->textInput(['style'=>'width:450px', "maxlength" => 50])->label("标题") ?>


    <?= $form->field($model, 'cover')->fileInput() ?>

    <?= $form->field($model, 'desc')->widget(\yii\redactor\widgets\Redactor::className(), [ 
        'clientOptions' => [ 
            'imageManagerJson' => ['/redactor/upload/image-json'], 
            'imageUpload' => ['/redactor/upload/image'], 
            'fileUpload' => ['/redactor/upload/file'], 
            'lang' => 'zh_cn', 
            'plugins' => ['clips', 'fontcolor','imagemanager']
        ],
        
    ]) ?>

    <?= $form->field($model, 'price')->textInput(['style'=>'width:150px', "maxlength" => 10])->label("价格") ?>
    <?= $form->field($model, 'count')->textInput(['style'=>'width:150px', "maxlength" => 10])->label("库存") ?>

    <div class="form-group">
        <?= Html::submitButton('Submit', ['class' => 'btn btn-primary']) ?>
    </div>

<?php ActiveForm::end(); ?>