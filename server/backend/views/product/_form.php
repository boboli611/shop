<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\file\FileInput;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\comm\CommProduct */
/* @var $form yii\widgets\ActiveForm */

$items = $itemInfos = [];
$itemList = \common\models\comm\CommProductItem::find()->orderBy("sort desc")->all();
array_unshift($items, "选择类别");
foreach ($itemList as $val) {
    $items[$val->id] = $val->title;
    $itemInfos[$val->id] = json_decode($val->info, TRUE);
}

$itemInfos = json_encode($itemInfos);

$model->price = $model->price / 100;
$model->carriage = $model->carriage / 100;
$imgModel = new common\models\Img();
$covers = json_decode($model->cover, true);
$covers = is_array($covers) ? $covers : [];
$model->cover = array_shift($covers);

$productInfo[$model->item_id] = json_decode($model->info, true);
$productInfo = json_encode($productInfo);

$storageJson = [];
foreach($storageList as $val){

    $sData['id'] = $val->id;
    $sData['style'] = $val->style;
    $sData['size'] = $val->size;
    $sData['num'] = $val->num;
    $sData['price'] = $val->price;
    $sData['status'] = $val->status;
    $storageJson[] = $sData;
}

$storageJson = json_encode($storageJson);

//$modelRecommend->id = $modelRecommend ? 1 : 0;
$carriageList = ["0" => "包邮", "5" => "5元", "10" => "10元"];
        
?>

<style>
    .storage span{margin-left: 20px;}
    .storage input{margin-left: 20px;}
     .storage a{margin-left: 20px;}
    
    table {
    font-family: verdana,arial,sans-serif;
    font-size:11px;
    color:#333333;
    border-width: 1px;
    border-color: #666666;
    border-collapse: collapse;
}
table th {
    border-width: 1px;
    padding: 8px;
    border-style: solid;
    border-color: #666666;
    background-color: #dedede;
}
table td {
    border-width: 1px;
    padding: 8px;
    border-style: solid;
    border-color: #666666;
}
</style>

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

    <?php echo $form->field($model, 'status')->dropDownList(['0' => '下架', '1' => '上架'], ['style' => 'width:120px', "value" => $model->status])->label("状态") ?>  

    <?php echo $form->field($model, 'item_id')->dropDownList($items, ['style' => 'width:120px', "value" => $model->item_id])->label("类别") ?>  
    <div id="item_info"></div>
    <?= $form->field($model, 'type')->dropDownList(['2' => '否', '1' => "是"], ['style' => 'width:120px', "value" => $modelStorage->type])->label("首页推荐") ?>  

    <?php
    /*
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
    <?php  } */?>
    <!--<div class="" style="display:hidden;">
        <div class="col-lg-2">
            <a href="javascript:void(0)" onclick="add()">增加</a>
        </div>
    </div>-->
    <br />

    <div class="storage">
        <div class="form-group field-commproduct-type">
            <span><label class="control-label">样式:</label><input name="styles" value="" style="width:100px"><a class="styleadd" href="javascript:void(null)">+</a></span>
        </div>

       <div class="form-group field-commproduct-type">
            <span><label class="control-label">尺码:</label><input name="sizes" value="" style="width:100px"><a class="styleadd" href="javascript:void(null)">+</a></span>
        </div>

        <div class="form-group field-commproduct-type">
            <span><label class="control-label">数量:</label><input name="num" value="" style="width:100px"> </span>
        </div>
        <div class="form-group field-commproduct-type">
            <span><label class="control-label">价格:</label><input name="price" value="" style="width:100px"> </span>
        </div>
        <div class="form-group field-commproduct-type">
            <span class="control-label"><button type="button" id="createTable" class="btn btn-success">生成表格</button></span>
        </div>
        <br />
        <br />
        <div id="table"></div>    
    </div>

    <br />
    <br />
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '保存' : '保存', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<script src="/assets/4d7e46b8/jquery.js"></script>
<script>
                var iteamsInfo = <?php echo $itemInfos; ?>;
                var productInfo = <?php echo $productInfo; ?>;

                function addImgHiden(url) {
                    var val = $("#commproduct-cover").val()
                    if (!val) {
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
                    if (!$("#coverList").html()) {
                        $("#commproduct-cover").val(null)
                    }
                }

                function addItemInfo(id) {
                    var info = productInfo[id]
                    var itemV = ""
                    console.log(iteamsInfo[id], id)
                    $("#item_info").html("")
                    iteamsInfo[id].forEach(function (value, index, array) {
                        if (info && info[index]) {
                            itemV = info[index].value;
                        }

                        var input = "<p>" + value + ':<input type="text" style="width:200px;display:inline;" class="form-control" name="item_info[' + index + '][value]" value="' + itemV + '" ></p>'
                        input += '<input type="hidden"  class="form-control" name="item_info[' + index + '][name]" value="' + value + '" >';
                        $("#item_info").append(input)
                    });
                    //commproduct-item_id
                }


                $("#commproduct-item_id").change(function () {
                    var id = $(this).val();
                    addItemInfo(id);
                });

                var item_id = <?php echo intval($model->item_id); ?>;
                if (item_id > 0) {
                    addItemInfo(item_id);
                }



                var json = <?php echo $storageJson;?>;
                var storage_num, storage_price;
                run();
                init();

                function run() {
                    
                    if (json.length == 0){
                        return;
                    }
                    
                    var data = {};
                    var dataList = {};
                    var num, price;
                    var table = "<table>"

                    var title = {"style": "样式", "size": "尺寸", "num": "数量", "status": "状态"}
                    json.forEach(function (val, i) {
                        if (!data["style"]) {
                            data["style"] = {};
                        }
                        if (!data["size"]) {
                            data["size"] = {};
                        }
                        if (!data["num"]) {
                            data["num"] = [];
                        }
                        if (!data["status"]) {
                            data["status"] = {};
                        }

                        if (!data["size"][val.size])
                        {
                            data["size"][val.size] = [];
                        }


                        table += "<input type='hidden' name='storage_id[]' value='" + val.id + "'>";
                        data["style"][val.style] = 1;
                        data["size"][val.size].push(val.style);
                        data["num"].push(val.num);
                        num = val.num
                        price = val.price
                        //data["status"].push(val.status);

                    })

                    table += "<tr><td>&nbsp;</td>";
                    for (var val in data['style'])
                    {
                        //var val = data['style'][k]
                        table += "<td>" + val + "</td>";
                    }
                    table += "</tr>";

                    for (var k in data['size'])
                    {
                        var size = data['size'][k]
                        table += "<tr><td>" + k + "</td>";
                        size.forEach(function (item, index) {

                            table += "<td><input name='storage_num[]' value='" + num + "'></td>";
                            table += "<input type='hidden' name='storage_style[]' value='" + item + "'>";
                            table += "<input type='hidden' name='storage_size[]' value='" + k + "'>";
                            table += "<input type='hidden' name='storage_price[]' value='" + price + "'>";
                        })
                        table += "</tr>";
                    }
                    
                   table += "<tr><td>价格</td>";
                    for (var val in data['style'])
                    {
                        //var val = data['style'][k]
                        table += "<td>" + price + "</td>";
                    }

                    table += "</tr></table>"
                    $("#table").html(table)
                    //alert(table)

                }
                $(".styleadd").click(function () {
                    var input = $(this).prev().clone()
                    $(input).val("")
                    $(this).before(input)
                })

                $("#createTable").click(function () {
                    var style = []
                    var size = []
                    var ids = []
                    var num = $("input[name=num]").val()
                    storage_num = $("input[name=num]").val()
                    storage_price = $("input[name=price]").val()

                    var datas = []

                    $("input[name=styles]").each(function () {
                        style.push($(this).val())
                    })

                    $("input[name=sizes]").each(function () {
                        size.push($(this).val())
                    })
                    
                    $("input[name='storage_id[]']").each(function () {
                        ids.push($(this).val())
                    })
                    
                    console.log(ids)
                    var j = 0;
                    style.forEach(function (style, index) {
                        size.forEach(function (size, k) {
                            var storage = {}
                            storage.style = style
                            storage.size = size
                            storage.num = storage_num
                            storage.price = storage_price
                            storage.id = ids[j]
                            storage.status = 1

                            datas.push(storage)
                            j++;
                        })


                    })

                    console.log(datas)
                    json = datas

                    run()
                })
                
                function init(){
                    var data = {"style":{}, "size":{}}
                    var num ,price;
                     json.forEach(function (val, i) {
                         
                         var style = val.style
                         var size = val.size
                         num = val.num
                         price = val.price
                         
                         data.style[style] = 1
                         data.size[size] = 1
                     })
                     
              
                     var styleXY = $("input[name='styles'] + a")
                     var styleobj = $("input[name='styles']")
                     for (var i in data.style){
                         var val = $(styleobj).val();
                         if (!val){
                             $(styleobj).val(i)
                             continue;
                         }else{
                             var obj = $(styleobj).clone();
                             $(obj).val(i)
                             $(styleXY).before(obj)
                             
                         }    
                     }
                     
                     
                     var sizeXY = $("input[name='sizes'] + a")
                     var sizeobj = $("input[name='sizes']")
                     for (var i in data.size){
                                console.log(i)
                         var val = $(sizeobj).val();
                         if (!val){
                             $(sizeobj).val(i)
                             continue;
                         }else{
                             var obj = $(sizeobj).clone();
                             $(obj).val(i)
                             console.log(obj)
                             $(sizeXY).before(obj)
                             
                         }    
                     }
                     console.log(num)
                     $("input[name='num']").val(num)
                     $("input[name='price']").val(price)
                }


</script>
