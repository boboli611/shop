<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\file\FileInput;
use yii\helpers\Url;
use common\constant\App;

/* @var $this yii\web\View */
/* @var $model common\models\comm\CommBanner */
/* @var $form yii\widgets\ActiveForm */

$postions = \common\models\comm\CommBanner::$postions;
array_unshift($postions, "选择广告位");
$model->position = (int) $model->position;

$imgModel = new common\models\Img();
$imgs = json_decode($model->img, true);
$imgs = is_array($imgs) ? $imgs : [];
$model->img = array_shift($imgs);
?>

<div class="comm-banner-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php echo $form->field($model, 'img')->hiddenInput(['name' => 'img']); ?>

    <?php
    if ($model->img) {
        echo "<div id='coverList'>";
        echo "<a href='{$model->img}' target='_blank'>查看图片</a>&nbsp;&nbsp;<a href='javascript:;' onclick='remove(this)'>删除</a>";
        echo yii\bootstrap\Html::hiddenInput("img[]", $model->img, ["id" => "commbanner-img"]);
        echo "</div>";
    }

    foreach ($imgs as $img) {
        //echo $form->field($imgModel, 'image')->hiddenInput(['name' => 'cover']);
        echo "<div id='coverList'>";
        echo "<a href='{$img}' target='_blank'>查看图片</a>&nbsp;&nbsp;<a href='javascript:;' onclick='remove(this)'>删除</a>";
        echo yii\bootstrap\Html::hiddenInput("img[]", $img, ["id" => "commbanner-img"]);
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

    <?php
    if ($model->id) {

        echo $form->field($model, 'position')->hiddenInput(["readonly" => "readonly", 'style' => 'width:120px']);
        $position = $postions[$model->position];
        echo \yii\helpers\BaseHtml::input('text', 'position', $position, ["readonly" => "readonly", 'style' => 'width:120px', 'class' => "form-control"]);
        echo "<br/>";
    } else {
        echo $form->field($model, 'position')->dropDownList($postions, ['style' => 'width:120px', "value" => $model->position]);
    }
    ?>  


    <?= $form->field($model, 'status')->dropDownList([App::APP_UNVAILD_STATUS => '下架', App::APP_VAILD_STATUS => '上架'], ['style' => 'width:120px', "value" => $model->status]) ?>  


    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '保存' : '保存', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<script src="/assets/4d7e46b8/jquery.js"></script>
<script>

    function addImgHiden(url) {
        var val = $("#commbanner-img").val()
        if (!val) {
            $("#commbanner-img").val(url)
        }

        var str = "<div id='coverList'>" + "<a href='" + url + "' target='_blank'>查看图片</a>&nbsp;&nbsp;<a href='javascript:;' onclick='remove(this)'>删除</a>"
        str = str + '<input type="hidden" name="img[]" value="' + url + '"></div>'
        $(".file-caption-main").before(str)


    }
    function remove(event) {
        $(event).parent().remove();
        //alert(jQuery.isEmptyObject($("#coverList")))
        if (!$("#coverList").html()) {
            $("#commbanner-img").val(null)
        }
    }

</script>
