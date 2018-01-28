<?php

namespace frontend\controllers;

use Yii;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\widgets;

/**
 * index controller
 */
class AddressController extends Controller {
    
    public function actionSave(){
        
        $id = (int)Yii::$app->request->post("id");
        $name = Yii::$app->request->post("name");
        $mobile = (int)Yii::$app->request->post("mobile");
        $full_region = Yii::$app->request->post("full_region");
        $address = Yii::$app->request->post("address");
        $status = (int)Yii::$app->request->post("status");
        
        if(!$name || !$mobile || !$full_region || !$address){
            $this->asJson(widgets\Response::error("参数错误"));
            return;
        }
        
        
        
        $uid = widgets\User::getUid();
        
        try {
            
            if ($id){
                $addressModel = \common\models\user\UserAddress::findOne($id);
                if ($addressModel->user_id != $uid){
                    $this->asJson(widgets\Response::error("只能修改自己的地址"));
                    return;
                }
            }else{
                $addressModel = new \common\models\user\UserAddress();
            }

            $addressModel->user_id = $uid;
            $addressModel->name = $name;
            $addressModel->mobile = $mobile;
            $addressModel->full_region = $full_region;
            $addressModel->address = $address;
            $addressModel->status = $status;
            
            if ($status == 1){
                \common\models\user\UserAddress::updateAll(['status' => 0], "user_id = {$uid}");
            }
      
            $res = $addressModel->save();
            if (!$res){
                 throw new \Exception("保存失败");
            }
            
        } catch (\Exception $ex) {
            $this->asJson(widgets\Response::error($ex->getMessage()));
            return;
        }
        
        $out['id'] = $addressModel->id; 
        $this->asJson(widgets\Response::sucess($out));
    }
    
    public function actionDelete(){
        
        $out['id'] = 1; 
        $this->asJson(widgets\Response::sucess($out));
    }
    
    public function actionList(){
        
        $uid = widgets\User::getUid();
        
        $list = \common\models\user\UserAddress::find()->where(["user_id" => $uid])->orderBy("id desc")->all();
       
        $this->asJson(widgets\Response::sucess($list));
    }
    
    public function actionDetail(){
        
        $id = (int)Yii::$app->request->get("id");
        if (!$id){
            $this->asJson(widgets\Response::error("参数错误"));
            return;
        }
        
        $uid = widgets\User::getUid();
        
        $Info = \common\models\user\UserAddress::find()->where(["id" => $id])->andWhere(["user_id" => $uid])->one();
        if (!$Info){
             $this->asJson(widgets\Response::error("参数错误!"));
            return;
        }
        
        $this->asJson(widgets\Response::sucess($Info));
    }
    
    public function actionDefault(){
        
        
        $uid = widgets\User::getUid();
        
        $Info = \common\models\user\UserAddress::find()->where(["status" => 1])->andWhere(["user_id" => $uid])->one();
        if (!$Info){
             $this->asJson(widgets\Response::error("参数错误!"));
            return;
        }
        
        $this->asJson(widgets\Response::sucess($Info));
    }
    
}