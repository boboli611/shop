<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
/* @var $this yii\web\View */
/* @var $model common\models\CommOrder */

$refundModle = new common\models\comm\CommOrderRefundLog();

$this->title = '退款';
$this->params['breadcrumbs'][] = ['label' => 'Comm Orders', 'url' => ['index']];
$this->params['breadcrumbs'][] = '订单';
?>
<div class="comm-order-update">

    <div class="comm-order-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'order_id')->textInput(['style' => 'width:120px', "readonly" => true]) ?>

    <?= $form->field($model, 'username')->textInput(["readonly" => true]) ?>

    <?php echo $form->field($model, 'total')->textInput(["readonly" => true]) ?>

    <?php echo $form->field($model, 'address')->textInput(['style' => 'width:120px', "readonly" => true]) ?>

    <?= $form->field($model, 'status')->textInput(["readonly" => true]); ?>

    <?= $form->field($refundModle, 'price')->textInput(['style' => 'width:120px']); ?>
    <?php
    if ($model->refund == 1) {
        $model->refund = common\models\comm\CommOrder::$refund[$model->refund];
        echo $form->field($model, 'refund')->textInput(["readonly" => true]);
    } else {
        $refund = common\models\comm\CommOrder::$refund;
        unset($refund[common\models\comm\CommOrder::status_refund_no]);
        echo $form->field($model, 'refund')->dropDownList($refund, ['style' => 'width:120px', "value" => $model->refund]);
    }
    ?> 

    <?= $form->field($model, 'expressage')->textInput(['style' => 'width:120px']) ?>

    <?= $form->field($model, 'content')->textarea(['style' => 'width:120px', "readonly" => true]) ?>

    <?= $form->field($model, 'updated_at')->textInput(['style' => 'width:120px', "readonly" => true]) ?>

    <?= $form->field($model, 'created_at')->textInput(['style' => 'width:120px', "readonly" => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('退款', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

  
</div>

</div>
