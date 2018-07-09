<?php

namespace backend\controllers;

use Yii;
use common\models\comm\CommOrderRefundLog;
use common\models\comm\CommOrderRefundLogSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * CommOrderRefundController implements the CRUD actions for CommOrderRefundLog model.
 */
class CommOrderRefundController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
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
     * Lists all CommOrderRefundLog models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CommOrderRefundLogSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single CommOrderRefundLog model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }
    
    
    public function actionRefund($id){
        
        $model = $this->findModel($id);
        $status = Yii::$app->request->get('status');
       
        if ($model->refound != \common\models\comm\CommOrder::status_refund_checking){
            return $this->redirect(['index', 'error' => "退款已处理"]);
        }
        
        $order = \common\models\comm\CommOrder::find()->where(['order_id' => $model->order_id])->one();
        if(!$order){
            return $this->redirect(['index', 'error' => "订单不存在"]);
        }
        
        //不批准
        if ($status == 2){
            $model->refound = \common\models\comm\CommOrder::status_refund_fail;
            if (!$model->save()){
                return $this->redirect(['index', 'error' => '操作失败']);
            }
            
            return $this->redirect(['index', 'id' => $model->id]);
        }
        
        //批准退款
        $transaction = \common\models\comm\CommOrder::getDb()->beginTransaction();
        try {
            
            $model->refound = \common\models\comm\CommOrder::status_refund_sucess;
            if (!$model->save()){
                throw new \Exception('操作失败');
            }
            
            $ret = \common\components\WxpayAPI\Pay::refund($order->order_id, $order->total, $model->price);
            if($ret['err_code'] == "ERROR"){
                throw new \Exception($ret['err_code_des']);
            }
            
            $transaction->commit();
        } catch (\Exception $exc) {
            $transaction->rollBack();
             return $this->redirect(['index', 'error' => $exc->getMessage()]);
        }

        return $this->redirect(['index', 'id' => $model->id]);
        
    }



    /**
     * Updates an existing CommOrderRefundLog model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }


    /**
     * Finds the CommOrderRefundLog model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return CommOrderRefundLog the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = CommOrderRefundLog::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
