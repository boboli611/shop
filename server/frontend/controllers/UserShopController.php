<?php

namespace frontend\controllers;

use Yii;
use common\models\user\UserShop;
use common\models\user\UserShopSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\widgets;

/**
 * UserShopController implements the CRUD actions for UserShop model.
 */
class UserShopController extends Controller
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
     * Lists all UserShop models.
     * @return mixed
     */
    public function actionIndex()
    {
        
        $userId = \common\widgets\User::getUid();
        $shop = UserShop::getByUid($userId);
 
        $this->asJson(widgets\Response::sucess($shop));
    }

  

    /**
     * Creates a new UserShop model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $pid = Yii::$app->request->post("product_id");
        $num = (int)Yii::$app->request->post("num"); 
        $num = $num ? $num : 1;
        if (!$pid || !is_numeric($pid)){
            $this->asJson(widgets\Response::error("参数错误"));
            return;
        }
 
        $product = \common\models\comm\CommProduct::findOne($pid);
        if (!$product) {
            $this->asJson(widgets\Response::error("商品不存在"));
            return;
        }

        if ($product->count == 0){
             $this->asJson(widgets\Response::error("已售罄"));
             return;
        }

        if ($product->count <= $num){
             $this->asJson(widgets\Response::error("库存不足"));
             return;
        }
        
        if (!$product->status){
             $this->asJson(widgets\Response::error("已下架"));
             return;
        }
                
        $userId = \common\widgets\User::getUid();
       
        $model = new UserShop();
        $result = UserShop::getByUidPid($userId, $pid);
        if (is_object($result)){
            $model = $result;
            $model->num = $model->num + $num;
        }else{
            $model->user_id = $userId;
            $model->product_id = $pid;
            $model->num = $num;
        }
        if ($model->num > 0){
            $ret = $model->save();
        }else{
            $ret = $model->delete();
        }
        if(!$ret){
            $this->asJson(widgets\Response::error("操作失败"));
        }

        $out['id'] =  $model->getPrimaryKey();
        $this->asJson(\common\widgets\Response::sucess($out));
    }
    
    
    /**
     * Creates a new UserShop model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionUpdate()
    {
        $id = Yii::$app->request->post("id");
        $num = (int)Yii::$app->request->post("num"); 
        if (!$id || !is_numeric($id)){
            $this->asJson(widgets\Response::error("参数错误"));
            return;
        }

        $userId = \common\widgets\User::getUid();
       
        $model = UserShop::findOne($id);
        if (!$model){
            $this->asJson(widgets\Response::error("非法操作"));
            return;
        }
        
        if ($model->user_id != $userId){
            $this->asJson(widgets\Response::error("非法操作2"));
            return;
        }
        
        $model->num = $num;
        if ($model->num > 0){
            $ret = $model->save();
        }else{
            $ret = $model->delete();
        }
        if(!$ret){
            $this->asJson(widgets\Response::error("操作失败"));
        }

        $out['id'] =  $model->getPrimaryKey();
        $this->asJson(\common\widgets\Response::sucess($out));
    }
    
    
    /**
     * Deletes an existing UserShop model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete()
    {
        $id = Yii::$app->request->post("id");
        
        if (!$id || !is_numeric($id)){
            $this->asJson(widgets\Response::error("参数错误"));
            return;
        }
 
        $shop = UserShop::findOne($id); 
        if (!$shop) {
            $this->asJson(widgets\Response::error("商品不存在"));
            return;
        }
        
        $userId = \common\widgets\User::getUid();
        if ($shop->user_id != $userId){
            $this->asJson(widgets\Response::error("非法操作"));
            return;
        }
        
        if (!$shop->delete()){
            $this->asJson(widgets\Response::error("操作失败"));
            return;
        }
        
        $out['id'] =  $shop->getPrimaryKey();
        $this->asJson(\common\widgets\Response::sucess($out));
    }

}
