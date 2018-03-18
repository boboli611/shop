<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\file\FileInput;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\comm\CommProduct */
/* @var $form yii\widgets\ActiveForm */

$items = (new \common\models\comm\CommProductItem())->getList();
array_unshift($items, "选择类别");
$model->price = $model->price / 100;
$model->carriage = $model->carriage / 100;
$imgModel = new common\models\Img();
$covers = json_decode($model->cover, true);
$covers = is_array($covers) ? $covers : [];
$model->cover = array_shift($covers);

$carriageList = ["0" => "包邮", "5" => "5元", "10" => "10元"]
?>

<div class="comm-product-form" style="width: 1000px;">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?=
    $form->field($model, 'desc')->widget(\yii\redactor\widgets\Redactor::className(), [
        'clientOptions' => [
            'imageManagerJson' => ['/redactor/upload/image-json'],
            'imageUpload' => ['/file/upload-redactor'],
            'fileUpload' => ['/redactor/upload/file'],
            'lang' => 'zh_cn',
            'plugins' => ['clips', 'fontcolor', 'imagemanager']
        ],
    ])
    ?>
    <?php echo $form->field($model, 'cover')->hiddenInput(['name' => 'cover']); ?>

    <?php
    if ($model->cover) {
        echo "<div id='coverList'>";
        echo "<a href='{$model->cover}' target='_blank'>查看图片</a>&nbsp;&nbsp;<a href='javascript:;' onclick='remove(this)'>删除</a>";
        echo yii\bootstrap\Html::hiddenInput("cover[]", $model->cover, ["id" => "commproduct-cover"]);
        echo "</div>";
    }


    foreach ($covers as $img) {
        //echo $form->field($imgModel, 'image')->hiddenInput(['name' => 'cover']);
        echo "<div id='coverList'>";
        echo "<a href='{$img}' target='_blank'>查看图片</a>&nbsp;&nbsp;<a href='javascript:;' onclick='remove(this)'>删除</a>";
        echo yii\bootstrap\Html::hiddenInput("cover[]", $img, ["id" => "commproduct-cover"]);
        echo "</div>";
    }
    ?>

    <?php
    echo $form->field($imgModel, 'img')->label(false)->widget(FileInput::classname(), [
        'options' => ['multiple' => false],
        'pluginOptions' => [
            // 异步上传的接口地址设置
            'uploadUrl' => Url::toRoute(['/file/input-upload']),
            // 异步上传需要携带的其他参数，比如商品id等
            'uploadAsync' => true,
            'initialPreviewAsData' => false,
            'dataShowPreview' => false,
            "showPreview" => false,
            // 最少上传的文件个数限制
            'minFileCount' => 1,
            // 最多上传的文件个数限制
            'maxFileCount' => 10,
            // 是否显示移除按钮，指input上面的移除按钮，非具体图片上的移除按钮
            'showRemove' => true,
            // 是否显示上传按钮，指input上面的上传按钮，非具体图片上的上传按钮
            'showUpload' => true,
            //是否显示[选择]按钮,指input上面的[选择]按钮,非具体图片上的上传按钮
            'showBrowse' => true,
            // 展示图片区域是否可点击选择多文件
            'browseOnZoneClick' => true,
        // 如果要设置具体图片上的移除、上传和展示按钮，需要设置该选项
        ],
        // 一些事件行为
        'pluginEvents' => [
            // 上传成功后的回调方法，需要的可查看data后再做具体操作，一般不需要设置
            "fileuploaded" => "function (event, data, id, index) {
           addImgHiden(data.response.initialPreview)
           $('.kv-upload-progress').hide();
        }",
            "fileclear" => "function (event, data, id, index) {
            console.log(data, id, index);
           clearImg();
        }",
            "filesuccessremove" => "function (event, data, id, index) {
            console.log('bbbbbb');
        }",
        ],
    ]);
    ?>

    <?= $form->field($model, 'carriage')->dropDownList($carriageList, ['style' => 'width:120px', "value" => $modelStorage->carriage]) ?>  
    <?= $form->field($model, 'tag')->textInput(['style' => 'width:100px'])->label("标签(最大4个字)") ?> 

    <?php //echo $form->field($model, 'status')->dropDownList(['0'=>'下架','1'=>'上架'], ['style'=>'width:120px', "value" => $model->status])->label("状态")   ?>  

    <?php echo $form->field($model, 'item_id')->dropDownList($items, ['style' => 'width:120px', "value" => $model->item_id])->label("类别") ?>  

    <?= $form->field($model, 'type')->dropDownList(['2' => '不置顶', '1' => "置顶"], ['style' => 'width:120px', "value" => $modelStorage->type])->label("置顶") ?>  

    <?php
    foreach ($storageList as $k => $modelStorage) {

        $status = $k === 0 ? null : false;
        ?>
        <div class="row">
            <div class="col-lg-2">
                <?php echo $form->field($modelStorage, 'style')->textInput(['name' => "storage_style[]"])->label($status) ?>
            </div>
            <div class="col-lg-2">
                <?php echo $form->field($modelStorage, 'size')->textInput(['name' => "storage_size[]"])->label($status) ?>   
            </div>
            <div class="col-lg-2">
                <?php echo $form->field($modelStorage, 'num')->textInput(['name' => "storage_num[]"])->label($status) ?>   
            </div>
            <div class="col-lg-2">
                <?php echo $form->field($modelStorage, 'price')->textInput(['name' => "storage_price[]"])->label($status) ?>   
            </div>
            <div class="col-lg-2">
                <?= $form->field($modelStorage, 'status')->dropDownList(['0' => '下架', '1' => '上架'], ['style' => 'width:120px', "value" => $modelStorage->status, 'name' => "storage_status[]"])->label($status) ?>  
            </div>


            <?php echo Html::hiddenInput("storage_id[{$k}]", $modelStorage->id); ?>
        </div>
    <?php } ?>
    <div class="">
        <div class="col-lg-2">
            <a href="javascript:void(0)" onclick="add()">增加</a>
        </div>
    </div>
    <br />
    <br />
    <br />
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '添加' : '修改', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<script src="/assets/4d7e46b8/jquery.js"></script>
<script>

    function addImgHiden(url) {
        var val = $("#commproduct-cover").val()
        if (!val){
            $("#commproduct-cover").val(url)
        }
        
        var str = "<div id='coverList'>" + "<a href='" + url + "' target='_blank'>查看图片</a>&nbsp;&nbsp;<a href='javascript:;' onclick='remove(this)'>删除</a>"
        str = str + '<input type="hidden" name="cover[]" value="' + url + '"></div>'
        $(".file-caption-main").before(str)
        
       
    }

    function clearImg() {
        $("input[name='CommProduct[cover][]']").attr("value", "");
    }

    function add() {
        console.log($(".row:last").clone())
        //$(".row:last").clone().after("#row:last")
        $(".row:last").after($(".row:last").clone())
    }
    function remove(event) {
        $(event).parent().remove();
        //alert(jQuery.isEmptyObject($("#coverList")))
        if(!$("#coverList").html()){
            $("#commproduct-cover").val(null)
        }
    }


</script>
