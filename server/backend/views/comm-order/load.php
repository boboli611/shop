<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $searchModel common\models\comm\CommProductSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '上传快递单号';
$this->params['breadcrumbs'][] = $this->title;
?>

<style type="text/css">
table.gridtable {
    font-family: verdana,arial,sans-serif;
    font-size:11px;
    color:#333333;
    border-width: 1px;
    border-color: #666666;
    border-collapse: collapse;
}
table.gridtable th {
    border-width: 1px;
    padding: 8px;
    border-style: solid;
    border-color: #666666;
    background-color: #dedede;
}
table.gridtable td {
    border-width: 1px;
    padding: 8px;
    border-style: solid;
    border-color: #666666;
    background-color: #ffffff;
}
</style>

<div class="comm-product-index" style="width: 80%">


   
<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>
 
    <?= $form->field($model, 'id')->fileInput() ?>
 
    <button>提交</button>
 
<?php ActiveForm::end() ?>
    
     <?php if (count($outFail) > 0){?>   
     <h4>保存失败</h4> 
    <table class="gridtable">
        <?php foreach ($outFail as $val){
            $str = sprintf("<tr><td>%s</td><td>%s</td></tr>", $val['id'], $val['msg']);
            echo $str;
         }?>
    </table> 
 <?php }?>
</div>
 
  
 <?php if (count($data) > 0){?>   
     <h4>保存成功</h4> 
    <table class="gridtable">
        <?php foreach ($data as $val){
            $str = sprintf("<tr><td>%s</td><td>%s</td></tr>", $val['id'], $val['msg']);
            echo $str;
         }?>
    </table> 
 <?php }?>
</div>