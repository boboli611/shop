<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\LinkPager;

/* @var $this yii\web\View */
/* @var $searchModel common\models\CommOrderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '订单列表';
$this->params['breadcrumbs'][] = $this->title;
$params = Yii::$app->request->queryParams['CommOrderSearch'];
?>
<!-- CSS goes in the document HEAD or added to your external stylesheet -->
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




<div class="comm-order-index">
    
    <div>
        <form action="/comm-order/index" method="get">
            <span>订单号：</span><input type="text" name="order_id" value="<?php echo $order_id;?>"/> <input type="submit" value="查询" />
        </form>
    </div>
<br/>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?php
    foreach ($dataProvider as $model) {

        $order_id = $model['order_id'];
        $expressage = sprintf("[%s]%s", \common\components\express\ShipperCode::$list[$model['ShipperCode']], $model['expressage']);
        $sql = "select c.title, c.cover, a.num, a.price from comm_order_product a"
                . " left join comm_production_storage  b on b.id = a.product_id"
                . " left join comm_product c on c.id = b.product_id"
                . " where order_id = '{$order_id}'";
        $product = common\models\comm\CommProduct::findBySql($sql)->asArray()->all();
        $hb = 0;
        ?>
        <table class="gridtable" style="width:100%">
            <tr>
                <th colspan="2">订单号：<?php echo $model['order_id']; ?>&nbsp;&nbsp;&nbsp;
                    用户[ID]：<?php echo $model['username'] . "[" . $model['user_id'] . "]"; ?>
                    快递：<?php echo $model['status'] == 2 ? "无" : $expressage; ?>&nbsp;&nbsp;&nbsp;
                    付款时间：<?php echo $model['pay_time'] ?>&nbsp;&nbsp;&nbsp;

                    状态：<?php echo common\models\comm\CommOrder::$payName[$model['status']]; ?>&nbsp;&nbsp;&nbsp;
                </th>
                <th>数量</th>
                <th>价格</th>
                <th>快递费</th>
                <th>金额</th>
                <th>操作</th>
            </tr>
            <?php
            foreach ($product as $val) {
                $cover = json_decode($val['cover'], true);
                ?>
                <tr>
                    <td><img src="<?php echo $cover[0]; ?>" style="width:50px;"></td>
                    <td ><?php echo $val['title']; ?></td>
                    <td ><?php echo $val['num']; ?></td>
                    <td ><?php echo sprintf("%.2f", $val['price'] / 100) . " X " . $val['num']; ?></td>
                    <?php
                    if ($hb == 0) {
                        $hb = 1;
                        ?>
                        <td rowspan="<?php echo count($product); ?>">
                            <b><?php echo sprintf("￥%.2f", ($model['freight']) / 100); ?></b>
                        </td>
                        <td rowspan="<?php echo count($product); ?>">
                            <b><?php echo sprintf("￥%.2f", ($model['total'] + $model['freight']) / 100); ?></b>
                        </td>
                        <td rowspan="<?php echo count($product); ?>">
                            <a href="/comm-order/view?id=<?php echo $model['id']; ?>">详情</a>&nbsp;
                            <a href="/comm-order/update?id=<?php echo $model['id']; ?>">修改</a>
                        </td>

                <?php } ?>
                </tr>
    <?php } ?>
        </table>
        <br />
    <?php } ?>

<?php if (false) { ?>
        <table class="gridtable">
            <tr>
                <th>id</th>
                <th>订单号</th>
                <th>用户[ID]</th>
                <th>支付金额</th>
                <th>数量</th>
                <th>快递单号</th>
                <th>状态</th>
                <th>付款时间</th>
                <th>下单时间</th>
                <th>操作</th>
            </tr>
            <?php
            foreach ($dataProvider as $model) {

                $order_id = $model['order_id'];
                $expressage = sprintf("[%s]%s", \common\components\express\ShipperCode::$list[$model['ShipperCode']], $model['expressage']);
                $sql = "select c.title, c.cover, a.num from comm_order_product a"
                        . " left join comm_production_storage  b on b.id = a.product_id"
                        . " left join comm_product c on c.id = b.product_id"
                        . " where order_id = '{$order_id}'";
                $product = common\models\comm\CommProduct::findBySql($sql)->asArray()->all();
                ?>
                <tr>
                    <td rowspan="<?php echo count($product) + 1; ?>"><?php echo $model['id']; ?></td>
                    <td><?php echo $model['order_id']; ?></td>
                    <td><?php echo $model['username'] . "[" . $model['user_id'] . "]"; ?></td>
                    <td><?php echo sprintf("%.2f", ($model['total'] + $model['freight']) / 100); ?></td>
                    <td><?php echo $model['num']; ?></td>
                    <td><?php echo $model['status'] == 2 ? "无" : $expressage; ?></td>
                    <td><?php echo common\models\comm\CommOrder::$payName[$model['status']]; ?></td>
                    <td><?php echo $model['pay_time'] ?></td>
                    <td><?php echo $model['created_at'] ?></td>
                    <td>
                        <a href="/comm-order/view?id=<?php echo $model['id']; ?>">详情</a>&nbsp;
                        <a href="/comm-order/update?id=<?php echo $model['id']; ?>">修改</a>
                    </td>
                </tr>
                <?php
                foreach ($product as $val) {
                    $cover = json_decode($val['cover'], true);
                    ?>
                    <tr>
                        <td><img src="<?php echo $cover[0]; ?>" style="width:50px;"></td>
                        <td colspan="3"><?php echo $val['title']; ?></td>
                        <td colspan="5"><?php echo "数量:" . $val['num']; ?></td>
                    </tr>
                <?php } ?>
        <?php } ?>
        </table>
<?php } ?>
</div>

<?= LinkPager::widget(['pagination' => $pages]); ?>