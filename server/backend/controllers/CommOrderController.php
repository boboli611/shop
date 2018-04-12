<?php

namespace backend\controllers;

use Yii;
use common\models\comm\CommOrder;
use common\models\comm\CommOrderSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use \yii\db\Exception as Exception;
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
    public function actionView($id) {
        return $this->render('view', [
                    'model' => $this->findModel($id),
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
            $data['status'] = CommOrder::status_goods_waiting_receve;

            if (CommOrder::updateAll($data, ['order_id' => $id])) {
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

    public function actionRefund($id) {

        $model = $this->findModel($id);

        /*
        if (!Yii::$app->request->post()) {
            return $this->render('update', [
                        'model' => $model,
            ]);
        }
*/
        $patams = Yii::$app->request->post();
        $refund = $patams['refund'];
        $price = $patams['price'];
        
        $refund = 4;
        if ($model->status == CommOrder::status_waiting_pay) {
            throw new Exception("订单未付款");
        }
        
        if ($model->refund != CommOrder::status_refund_waiting) {
            throw new Exception("订单未申请退款");
        }

        if (!in_array($refund, [CommOrder::status_refund_fail,CommOrder::status_refund_ok])) {
            throw new Exception("退单状态错误");
        }

        if ($model->total < $price) {
            throw new Exception("退款金额超出订单金额");
        }
        $transaction = Yii::$app->db->beginTransaction();
        try {

            $data['refund'] = $patams['CommOrder']['refund'];
            if (!CommOrder::updateAll($data, ['order_id' => $id])) {
                throw new Exception("操作失败");
            }

            $RefundLogModel = new \common\models\comm\CommOrderRefundLog();
            $RefundLogModel->order_id = $model->order_id;
            $RefundLogModel->refound = $refund;
            $RefundLogModel->price = $price;
            $RefundLogModel->admin_id = yii::$app->user->identity->id;
            $RefundLogModel->admin_nickname = yii::$app->user->identity->username;
            if (!$RefundLogModel->save()) {
                throw new Exception("操作失败");
            }
 var_dump(__LINE__);exit;
            $ret = \common\components\WxpayAPI\Pay::refund($out_trade_no, $total_fee, $refund_fee);
            if (!$ret){
                //throw new Exception("退款失败");
            }
            $transaction->commit();
        } catch (Exception $exc) {
            $transaction->rollBack();
            echo $exc->getTraceAsString();
        }


        if (CommOrder::updateAll($data, ['order_id' => $id])) {
            return $this->redirect(['view', 'id' => $id]);
        } else {
            return $this->render('update', [
                        'model' => $model,
            ]);
        }


        if (Yii::$app->request->post()) {
            $patams = Yii::$app->request->post();
            $out_trade_no = "20180405152289680546119";
            $total_fee = 2;
            $refund_fee = 1;


            $openid = \common\models\user\UserWxSession::findOne(7);
            $product = (object) [];
            $product->title = "Lipze";
            $product->order_id = $out_trade_no;
            $product->price = $total_fee;
            //$order = \common\components\WxpayAPI\Pay::pay($openid['open_id'], $product);
            //var_dump($order);exit;

            \common\components\WxpayAPI\Pay::refund($out_trade_no, $total_fee, $refund_fee);
        }
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

}
