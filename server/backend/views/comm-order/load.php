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
<div class="comm-product-index" style="width: 80%">


   
<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>
 
    <?= $form->field($model, 'id')->fileInput() ?>
 
    <button>Submit</button>
 
<?php ActiveForm::end() ?>
   

</div>