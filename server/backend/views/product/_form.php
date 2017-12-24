<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\file\FileInput;
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $model common\models\comm\CommProduct */
/* @var $form yii\widgets\ActiveForm */

$items = (new \common\models\comm\CommProductItem())->getList();
array_unshift($items,"选择类别");
?>

<div class="comm-product-form" style="width: 1000px;">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'desc')->widget(\yii\redactor\widgets\Redactor::className(), [ 
        'clientOptions' => [ 
            'imageManagerJson' => ['/redactor/upload/image-json'], 
            'imageUpload' => ['/file/upload-redactor'],  
            'fileUpload' => ['/redactor/upload/file'], 
            'lang' => 'zh_cn', 
            'plugins' => ['clips', 'fontcolor','imagemanager']
        ],
        
    ]) ?>

    <?php //echo $form->field($model, 'cover')->fileInput() ?>
    
    <?php 
$img = json_decode($model->cover, true);
$img = is_array($img) ?  $img : [];

echo $form->field($model, 'cover')->label('商品图')->widget(FileInput::classname(), [
    'options' => ['multiple' => false],
    'pluginOptions' => [
        // 需要预览的文件格式
        'previewFileType' => 'image',
        // 预览的文件
        'initialPreview' =>  $img,
        // 需要展示的图片设置，比如图片的宽度等
        //'initialPreviewConfig' => $p2,
        // 是否展示预览图
        'initialPreviewAsData' => true,
        // 异步上传的接口地址设置
        'uploadUrl' => Url::toRoute(['/file/input-upload']),
        //'uploadUrl' => '/file/async-banner',
        // 异步上传需要携带的其他参数，比如商品id等
        'uploadExtraData' => [
            'goods_id' => 1,
        ],
        'uploadAsync' => true,
        // 最少上传的文件个数限制
        'minFileCount' => 1,
        // 最多上传的文件个数限制
        'maxFileCount' => 1,
        // 是否显示移除按钮，指input上面的移除按钮，非具体图片上的移除按钮
        'showRemove' => true,
        // 是否显示上传按钮，指input上面的上传按钮，非具体图片上的上传按钮
        'showUpload' => true,
        //是否显示[选择]按钮,指input上面的[选择]按钮,非具体图片上的上传按钮
        'showBrowse' => true,
        // 展示图片区域是否可点击选择多文件
        'browseOnZoneClick' => true,
        // 如果要设置具体图片上的移除、上传和展示按钮，需要设置该选项
        'fileActionSettings' => [
            // 设置具体图片的查看属性为false,默认为true
            'showZoom' => false,
            // 设置具体图片的上传属性为true,默认为true
            'showUpload' => true,
            // 设置具体图片的移除属性为true,默认为true
            'showRemove' => true,
        ],
    ],
    // 一些事件行为
    'pluginEvents' => [
        // 上传成功后的回调方法，需要的可查看data后再做具体操作，一般不需要设置
        "fileuploaded" => "function (event, data, id, index) {
            console.log(data);
           addImgHiden(data.response.initialPreview)
        }",
    ],
]);
?>
    
    <?php
        foreach ($img as $val){
            echo  Html::hiddenInput("cover_path[]", $val);
        }
    ?>

    <?= $form->field($model, 'price')->textInput() ?>

    <?= $form->field($model, 'sell')->textInput() ?>

    <?= $form->field($model, 'count')->textInput() ?>

    <?= $form->field($model, 'status')->dropDownList(['0'=>'下架','1'=>'上架'], ['style'=>'width:120px', "value" => $model->status])->label("状态") ?>  
    
    <?php echo $form->field($model, 'status')->dropDownList($items, ['style'=>'width:120px', "value" => $model->item_id])->label("类别") ?>  
    
    <?php //echo  $form->field($model, 'update_time')->textInput() ?>

    <?php //echo $form->field($model, 'create_time')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '添加' : '修改', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<script>

function addImgHiden(url){
    $("form").append('<input type="hidden" name="cover_path" value="'+url+'">');
}

</script>
