<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\components\express\ShipperCode;

/* @var $this yii\web\View */
/* @var $model common\models\CommOrder */
/* @var $form yii\widgets\ActiveForm */
$status = $model->status;
$model->status = common\models\comm\CommOrder::$payName[$model->status];

$model->total = $model->total / 100;
?>

<div class="comm-order-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'order_id')->textInput(['maxlength' => true, "readonly" => true]) ?>

    <?= $form->field($model, 'username')->textInput(["readonly" => true]) ?>

    <?php //echo $form->field($model, 'product_id')->textInput() ?>

    <?php //echo $form->field($model, 'price')->textInput() ?>

    <?php echo $form->field($model, 'total')->textInput(["readonly" => true]) ?>

    <?php //echo $form->field($model, 'num')->textInput() ?>

    <?php //$form->field($model, 'address')->textInput(['maxlength' => true, "readonly" => true]) ?>

    <?= $form->field($model, 'status')->textInput(["readonly" => true]); ?>

    <?php
    /*
    if ($model->refund == 1) {
        $model->refund = common\models\comm\CommOrder::$refund[$model->refund];
        echo $form->field($model, 'refund')->textInput(["readonly" => true]);
    } else {
        $refund = common\models\comm\CommOrder::$refund;
        unset($refund[common\models\comm\CommOrder::status_refund_no]);
        echo $form->field($model, 'refund')->dropDownList($refund, ['style' => 'width:120px', "value" => $model->refund]);
    }
     * */
    ?> 

    <?php
   
        echo $form->field($model, 'expressage')->textInput(['maxlength' => true]);
        echo $form->field($model, 'ShipperCode')->dropDownList(ShipperCode::$list, ['style' => 'width:120px', "value" => $model->ShipperCode]);
   
     ?>
    
    <?php //$form->field($model, 'carriage')->dropDownList($carriageList, ['style' => 'width:120px', "value" => $modelStorage->carriage]) ?> 

    <?= $form->field($model, 'content')->textarea(['maxlength' => true, "readonly" => true]) ?>

    <?= $form->field($model, 'updated_at')->textInput(['maxlength' => true, "readonly" => true]) ?>

    <?= $form->field($model, 'created_at')->textInput(['maxlength' => true, "readonly" => true]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '创建' : '更新', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

  
</div>
