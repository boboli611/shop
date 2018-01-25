<?php


namespace frontend\controllers;

use Yii;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use frontend\models\ContactForm;
use common\widgets;

/**
 * Site controller
 */
class ShopController extends Controller {
    
    public function actionList() {
        
        $page = (int) Yii::$app->request->get("p");
        $page = $page > 0 ? $page - 1 : $page;
        $limit = 10;
        $offset = $page * $limit;
        $uid = widgets\User::getUid();
        
        $sql = "select c.*,c.price as p, b.size,b.style,b.id as storage_id, b.price from user_shop a "
                . " inner join comm_production_storage b on a.storage_id = b.id"
                . " inner join comm_product c on b.product_id = c.id"
                . " where a.user_id = {$uid} and b.status = 1 and c.status = 1"
                . " order by id desc limit {$offset}, {$limit}";
        $info = \common\models\user\UserShop::findBySql($sql)->asArray()->all();
        
        $address = \common\models\user\UserAddress::getByUserAuto($uid);
        
        $carriage = 0;
        $price = 0;
        foreach($info as $val){
            $carriage += $val['carriage'];
            $price += $val['price'];
        }
        
        $out["info"] = $info;
        $out["order"]["price"] = $price / 100;
        $out["order"]["carriage"] = $carriage / 100;
        $out["order"]["discount"] = 0;
        $out["address"]['id'] = $address['id'];
        $out["address"]['address'] = $address['full_region'] . $address['address'];
    
        return $this->asJson(widgets\Response::sucess($out));

    }
    
     public function actionAdd() {
        
        $id = (int) Yii::$app->request->get("id");
        if (!$id) {
            $this->asJson(widgets\Response::error("参数错误"));
            return;
        }
        
        $storage = \common\models\comm\CommProductionStorage::getByid($id);
       
        if (!$storage){
            $this->asJson(widgets\Response::error("参数错误!"));
            return;
        }
        
        $uid = widgets\User::getUid();
        $model = new \common\models\user\UserShop();
        $model->user_id = $uid;
        $model->storage_id = $id;
        $model->num = 1;
        if($model->save()){
            $id = $model->id;
            return $this->asJson(widgets\Response::sucess(['id' => $id]));
        }else{
            return $this->asJson(widgets\Response::error("添加失败"));
        }
    }
}
