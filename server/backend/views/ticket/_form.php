<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\comm\CommTicket */
/* @var $form yii\widgets\ActiveForm */
var_dump($model->duration);
?>

<div class="comm-ticket-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'title')->textInput(['style' => 'width:600px']) ?>

    <?= $form->field($model, 'condition')->textInput(['style' => 'width:120px'])->label("使用条件(金额)") ?>

    <?= $form->field($model, 'money')->textInput(['style' => 'width:120px']) ?>

    <?= $form->field($model, 'index_show')->dropDownList(["1" => "显示", 0 => "不显示" ], ['style' => 'width:120px', "value" => $model->index_show]) ?>

    <?= $form->field($model, 'duration')->textInput(['style' => 'width:240px', 'readonly' => 'readonly']) ?>


    <?= $form->field($model, 'status')->dropDownList(["1" => "上架", 0 => "下架" ], ['style' => 'width:120px', "value" => $model->status]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '新增' : '修改', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<link href="/datetimepicker/bootstrap/bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">
<link href="/datetimepicker/css/bootstrap-datetimepicker.min.css" rel="stylesheet" media="screen">
<script type="text/javascript" src="/datetimepicker/bootstrap/jquery/jquery-1.8.3.min.js" charset="UTF-8"></script>
<script type="text/javascript" src="/datetimepicker/bootstrap/bootstrap/js/bootstrap.min.js"></script>
<script type="text/javascript" src="/datetimepicker/js/bootstrap-datetimepicker.js" charset="UTF-8"></script>
<script type="text/javascript" src="/datetimepicker/js/locales/bootstrap-datetimepicker.fr.js" charset="UTF-8"></script>
<script>
    $('#commticket-duration').datetimepicker({
        format: "yyyy-mm-dd",
        language: 'fr',
        weekStart: 1,
        todayBtn: 1,
        autoclose: 1,
        todayHighlight: 1,
        startView: 2,
        minView: 2,
        forceParse: 0
    });
</script>
