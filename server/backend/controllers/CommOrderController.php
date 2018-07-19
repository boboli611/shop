<?php

namespace backend\controllers;

use Yii;
use common\models\comm\CommOrder;
use common\models\comm\CommOrderSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use \yii\db\Exception as Exception;

//use PHPExcel;
/**
 * CommOrderController implements the CRUD actions for CommOrder model.
 */
class CommOrderController extends Controller {

    /**
     * @inheritdoc
     */
    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all CommOrder models.
     * @return mixed
     */
    public function actionIndex() {

        $searchModel = new CommOrderSearch();

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single CommOrder model.
     * @param integer $id
     * @return mixed
     */
    public function actionView() {

        $id = \Yii::$app->request->get("id");
        $order_id = \Yii::$app->request->get("order_id");

        if ($id) {
            $model = $this->findModel($id);
        } else {
            $model = $this->findModelByOrder($order_id);
        }

        //$product = CommOrder::getInfoByOrder($model->order_id, $model->user_id);

        $searchModel = new \common\models\comm\CommOrderProductSeatch();
        $params['CommOrderProductSeatch'] = ['order_id' => $model->order_id];
        $dataProvider = $searchModel->search($params);
        return $this->render('view', [
                    'model' => $model,
                    'product' => $dataProvider,
        ]);
    }

    /**
     * Updates an existing CommOrder model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id) {

        $model = $this->findModel($id);
        if (Yii::$app->request->post()) {
            $patams = Yii::$app->request->post();
            $data['expressage'] = $patams['CommOrder']['expressage'];


            if ($model->status == CommOrder::status_goods_waiting_send && $data['expressage']) {
                $data['status'] = CommOrder::status_goods_waiting_receve;
            }

            if (CommOrder::updateAll($data, ['id' => $id])) {
                return $this->redirect(['view', 'id' => $id]);
            } else {
                return $this->render('update', [
                            'model' => $model,
                ]);
            }
        } else {
            return $this->render('update', [
                        'model' => $model,
            ]);
        }
    }

    public function actionTest() {
        $ret = \common\components\WxpayAPI\Pay::refund('20180505152551255128614', 12, 1);
        var_dump($ret);
    }

    public function actionExport() {

        if (!$_GET) {
            return $this->render('expert', [
                        'model' => [],
            ]);

            return;
        }
        $filename = "order.csv";
        header('Content-Encoding: UTF-8');
        header("Content-Type: text/csv; charset=UTF-8");
        header("Content-Disposition: attachment; filename={$filename}");


        $headerArr = [ 'order_id' => '业务单号', 'name' => '收件人姓名', 'mobile' => '收件人手机', 'province' => '收件省', 'city' => '收件市', 'county' => '收件区/县', 'address' => '收件人地址', 'product_name' => '品名', 'num' => '数量', 'sell_remark' => '备注', 'username'=> "买家昵称"];
        // 获取句柄
        $output = fopen('php://output', 'w') or die("can't open php://output");

        // 输出头部标题
        $this->fputcsv2($output, $headerArr);
        $sql = "SELECT `comm_order`.*, `user`.`username`, `comm_order_product`.`num`, `comm_production_storage`.`style`, `comm_production_storage`.`size`, `comm_product`.`title`, `comm_product`.`info` 
                FROM `comm_order` 
                left join `user` ON comm_order.user_id = user.id 
                left join `comm_order_product` ON comm_order.order_id = comm_order_product.order_id 
                left join `comm_production_storage` ON comm_production_storage.id = comm_order_product.product_id 
                left join `comm_product` ON comm_product.id = comm_production_storage.product_id WHERE `comm_order`.`status`=2";
        $orderList = CommOrder::findBySql($sql)->asArray()->all();

       foreach ($orderList as $order) {
            $order_id = $order['order_id'];
            $address = json_decode($order['address'], true);
            $order = is_array($address) ? array_merge($order, $address) : $order;
            $info = json_decode($order['info'], true);
            $shop_id = "";
            foreach ($info as $val){
                if ($val['name'] == "货号"){
                    $shop_id = $val['value'];
                    break;
                }
            }
            $order['product_name'] = $shop_id . " ". $order['style'] . " " . $order['size'] . " ". $order['num'];

            if ($data[$order_id]){
                $data[$order_id]['product_name'] .= " ".$order['product_name'];
                $data[$order_id]['num'] += $order['num'];
            }else{
                 foreach ($headerArr as $k => $v) {
                //$objPHPExcel->getActiveSheet()->setCellValue($colum . $i, $order[$k]);
                    $data[$order_id][$k] = $order[$k];
                }
            }
            //$data = [];
          
        }

        foreach ($data as $item) {
            
            $item['product_name'] .= "［买家昵称］".$order['username'];
             // 输出头部标题
            $this->fputcsv2($output, $item);
        }
        // 关闭句柄
        fclose($output) or die("can't close php://output");
    }

    /**
     * 重写fputcsv方法，添加转码功能
     * @param $handle
     * @param array $fields
     * @param string $delimiter
     * @param string $enclosure
     * @param string $escape_char
     */
    function fputcsv2($handle, array $fields, $delimiter = ",", $enclosure = '"', $escape_char = "\\") {
        foreach ($fields as $k => $v) {
            $fields[$k] = iconv("UTF-8", "GB2312//IGNORE", $v);  // 这里将UTF-8转为GB2312编码
        }
        fputcsv($handle, $fields, $delimiter, $enclosure, $escape_char);
    }

    public function actionLoad() {
        require dirname(dirname(__DIR__)) . '/common/components/PHPExcel/Classes/PHPExcel.php';
        $data = $_FILES;
        $model = new CommOrder();
        $out = [];
        if ($data) {
            $ShipperCode = \common\components\express\ShipperCode::$list;
            $ShipperCode = array_flip($ShipperCode);
            $dir = $data['CommOrder']['tmp_name']['id'];
            $objReader = \PHPExcel_IOFactory::createReader('CSV')->setDelimiter(',')
                    ->setEnclosure('"')
                    //->setLineEnding("\r\n")
                    ->setSheetIndex(0);
            $objReader->setInputEncoding('GBK');
            $file_encoding = mb_detect_encoding($dir);
            //var_dump($file_encoding);exit;
            $objPHPExcel = $objReader->load($dir);

            $data = $objPHPExcel->getSheet()->toArray();
            $out = [];
            $outFail = [];
            foreach ($data as $val) {

                $id = trim($val[0]);
                $expressageId = trim($val[2]);
                $name = $val[1];

                if (!is_numeric($id)) {
                    continue;
                }


                $orderModel = CommOrder::find()->where(['order_id' => $id])->one();
                if (!$orderModel) {
                    $outFail[$id]['id'] = $id;
                    $outFail[$id]['msg'] = "订单号不存在";
                    continue;
                }

                $orderModel->expressage = $expressageId;
                $orderModel->ShipperCode = $ShipperCode[$name];
                $orderModel->status = CommOrder::status_goods_waiting_receve;
                if (!$ShipperCode[$name]) {
                    $outFail[$id]['id'] = $id;
                    $outFail[$id]['msg'] = "快递公司匹配失败";
                    continue;
                }
                if (!$orderModel->save()) {
                    $outFail[$id]['id'] = $id;
                    $outFail[$id]['msg'] = "保存失败";
                    continue;
                }

                $out[$id]['id'] = $id;
                $out[$id]['msg'] = "保存成功";
            }
        }



        return $this->render('load', [
                    'model' => $model,
                    'data' => $out,
                    'outFail' => $outFail,
        ]);
    }

    private function refund($out_trade_no, $total_fee, $refund_fee) {

        return \common\components\WxpayAPI\Pay::refund($out_trade_no, $total_fee, $refund_fee);
    }

    /**
     * Finds the CommOrder model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return CommOrder the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        $model = CommOrder::find()->select(['comm_order.*', 'user.username']);
        $model->join('inner join', "user", "user.id = comm_order.user_id");
        $model->where(["comm_order.id" => $id]);
        $model = $model->one();

        return $model;
    }

    protected function findModelByOrder($orderId) {
        $model = CommOrder::find()->select(['comm_order.*', 'user.username']);
        $model->join('inner join', "user", "user.id = comm_order.user_id");
        $model->where(["comm_order.order_id" => $orderId]);
        $model = $model->one();

        return $model;
    }

}
