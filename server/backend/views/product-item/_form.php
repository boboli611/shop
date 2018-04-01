<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\file\FileInput;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\comm\CommProductItem */
/* @var $form yii\widgets\ActiveForm */

$imgModel = new common\models\Img();

$info = json_decode($model->info, TRUE);
$data = empty($info) ? [""] : $info;
?>

<div class="comm-product-item-form" style="width: 1000px;">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'icon')->hiddenInput() ?>
    <?php
    echo $form->field($imgModel, 'img')->label('icon')->widget(FileInput::classname(), [
       'options' => ['multiple' => false],
    'pluginOptions' => [
        // 需要预览的文件格式
        'previewFileType' => 'image',
        // 预览的文件
        'initialPreview' =>  [$model->icon],
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
           addImgHiden(data.response.initialPreview)
        }",
        ],
    ]);
    ?>

    <?php echo Html::hiddenInput("icon_path", $model->icon);?>

    <?= $form->field($model, 'sort')->textInput() ?>

    <div class="form-group field-commproductitem-title required">
        <label class="control-label" for="commproductitem-title">商品信息</label>
    </div>
    <?php
    foreach ($data as $k => $val) {
        ?>
        <div class="row">
            <div class="col-lg-2">
                <?php echo yii\helpers\BaseHtml::input("text", "name[]", $val)?>
            </div>
        </div>
    <?php } ?>
    <h5> <a href="javascript:void(0)" onclick="add()">增加</a></h5>

    <br/>
   <br/>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '保存' : '更新', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>


<script>

function addImgHiden(url){
    //$("form").append('<input type="hidden" name="icon_path" value="'+url+'">');   
    $("input[name='CommProductItem[icon]']").attr("value", url);
}

function add() {
        console.log($(".row:last").clone())
        
        var input = $(".row:last").clone()
        input.find("input").val("")
        $(".row:last").after(input)
    }

</script>
