<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\LinkPager;

/* @var $this yii\web\View */
/* @var $searchModel common\models\data\DataAnalyzeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '商品统计明细';
$this->params['breadcrumbs'][] = $this->title;

$line = [];
/*
 {
            name:'邮件营销',
            type:'line',
            stack: '总量',
            data:[120, 132, 101, 134, 90, 230, 210]
        },
*/
$newData = [];
$date = [];        
foreach ($data as $val){
    foreach (common\models\data\DataAnalyze::$types as $key => $name){
        $newData[$key][] = (int) $val[$key];
    }
    
    $date[] = date("m/d", strtotime($val['date']));
}

foreach ($newData as $key => $val){
    $arr['name'] =  common\models\data\DataAnalyze::$types[$key];
    $arr['type'] =  'line';
    $arr['data'] = $val;
    $line[] = $arr;
}

?>
<script src="/assets/echarts.min.js" type="text/javascript" ></script>
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


<div class="data-analyze-index">
<br />
<table class="gridtable" style="width:100%">
            <tr>
                <th>id</th>
                <th>封面</th>
                <th>标题</th>
                <?php foreach (common\models\data\DataAnalyze::$types as $key => $name){?>
                <th><?php echo $name;?></th>
                <?php }?>
            </tr>
             <?php foreach ($data as $val){
                 $cover = json_decode($val['cover'], true);
                 ?>
            <tr>
                <td><?php echo $val['pid'];?></td>
                <td><img src="<?php echo $cover[0];?>" style="width: 50px;"></td>
                <td><?php echo $val['title'];?></td>
                <?php foreach (common\models\data\DataAnalyze::$types as $key => $name){?>
                <td><?php echo (int)$val[$key];?></td>
                <?php }?>
            </tr>
                <?php }?>
            
</table>
</div>

<?= LinkPager::widget(['pagination' => $pages]); ?>  
