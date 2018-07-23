<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\data\DataAnalyzeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '商品统计';
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
<div id="main" style="width: 100%;height:400px;"></div>
    
<br />
<br />
<table class="gridtable" style="width:100%">
            <tr>
                <th>日期</th>
                <?php foreach (common\models\data\DataAnalyze::$types as $key => $name){?>
                <th><?php echo $name;?></th>
                <?php }?>
            </tr>
             <?php foreach ($data as $val){?>
            <tr>
                <td><?php echo $val['date'];?></td>
                <?php foreach (common\models\data\DataAnalyze::$types as $key => $name){?>
                <td><?php echo (int)$val[$key];?></td>
                <?php }?>
            </tr>
                <?php }?>
            
</table>
</div>

<script type="text/javascript">
    // 基于准备好的dom，初始化echarts实例
    var myChart = echarts.init(document.getElementById('main'));

    // 指定图表的配置项和数据
    var option = {
        title: {
            text: '商品信息'
        },
        tooltip: {
            trigger: 'axis'
        },
        legend: {
            data: <?php echo json_encode(array_values(common\models\data\DataAnalyze::$types));?>
        },
        grid: {
            left: '3%',
            right: '4%',
            bottom: '3%',
            containLabel: true
        },
        toolbox: {
            feature: {
                saveAsImage: {}
            }
        },
        xAxis: {
            type: 'category',
            boundaryGap: false,
            data: <?php echo json_encode($date);?>
        },
        yAxis: {
            type: 'value'
        },
        series: <?php echo json_encode($line);?>
    };

    // 使用刚指定的配置项和数据显示图表。
    myChart.setOption(option);
</script>
