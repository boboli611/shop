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
    
    public function actionShopBuy() {

        $ids = (int) Yii::$app->request->get("ids");
        if (!$ids){
            $this->asJson(widgets\Response::error("参数错误"));
            return;
        }
        $uid = widgets\User::getUid();

        $sql = "select a.id as shop_id, c.*,c.price as p, b.size,b.style,b.id as storage_id, b.price from user_shop a "
                . " inner join comm_production_storage b on a.storage_id = b.id"
                . " inner join comm_product c on b.product_id = c.id"
                . " where a.user_id = {$uid} and a.id in ($ids) b.status = 1 and c.status = 1"
                . " order by id desc";
        $info = \common\models\user\UserShop::findBySql($sql)->asArray()->all();
        $address = \common\models\user\UserAddress::getByUserAuto($uid);

        $carriage = 0;
        $price = 0;
        $buyNum = 0;
        foreach ($info as &$val) {
            
            $buyNum += $val['num'];
            $carriage += $val['carriage'];
            $price += $val['price'];
            $val['price'] = $val['price'] / 100;
            $val['carriage'] = $val['carriage'] / 100;
            $cover = json_decode($val['cover'], true);
            $val['cover'] = $cover[0];
        }

        $out["info"] = $info;
        $out["order"]["price"] = $price;
        $out["order"]["carriage"] = \frontend\service\ExpressFee::sumPrice($address['province'], $buyNum) / 100;
        $out["address"]['id'] = $address['id'];
        $out["address"]['address'] = $address['full_region'] . $address['address'];

        return $this->asJson(widgets\Response::sucess($out));
    }

    public function actionList() {

        $page = (int) Yii::$app->request->get("p");
        $page = $page > 0 ? $page - 1 : $page;
        $limit = 10;
        $offset = $page * $limit;
        $uid = widgets\User::getUid();
        
    
        $sql = "select a.id as shop_id, c.*,c.price as p, b.size,b.style,b.id as storage_id, b.price from user_shop a "
                . " inner join comm_production_storage b on a.storage_id = b.id"
                . " inner join comm_product c on b.product_id = c.id"
                . " where a.user_id = {$uid} and b.status = 1 and c.status = 1"
                . " order by id desc limit {$offset}, {$limit}";
        $info = \common\models\user\UserShop::findBySql($sql)->asArray()->all();
   
        //$address = \common\models\user\UserAddress::getByUserAuto($uid);

        $carriage = 0;
        $price = 0;
        $pIds = [];
        foreach ($info as &$val) {
            
            $carriage += $val['carriage'];
            $price += $val['price'];
            $val['price'] = $val['price'] / 100;
            $val['carriage'] = $val['carriage'] / 100;
            $cover = json_decode($val['cover'], true);
            $val['cover'] = $cover[0];
            $pIds[$val['id']] = 1;
        }
        
        $pIds = array_keys($pIds);
        $storages = \common\models\comm\CommProductionStorage::find()->where(['in', 'product_id', $pIds])->all();
        $storagesList = [];
        foreach ($storages as $v){
            $v = $v->toArray();
            $arr['id'] = $v['id'];
            $arr['size'] = $v['size'];
            $arr['style'] = $v['style'];
            $arr['num'] = $v['num'];
            $storagesList[$v['product_id']][] = $arr;
        }
         foreach ($info as &$val) {
             $val['storage'] = $storagesList[$val['id']];
         }
        //var_dump($storages);
        $out["info"] = $info;
        /*
        $out["order"]["price"] = $price / 100;
        $out["order"]["carriage"] = $carriage / 100;
        $out["order"]["discount"] = 0;
        $out["address"]['id'] = $address['id'];
        $out["address"]['address'] = sprintf("%s,%s,%s %s", $address['province'],$address['city'],$address['county'],$address['address']);
*/
        return $this->asJson(widgets\Response::sucess($out));
    }
    
    public function actionIdList() {

        $ids =  Yii::$app->request->get("ids");
        $uid = widgets\User::getUid();
        
    
        $sql = "select a.id as shop_id, c.*,c.price as p, b.size,b.style,b.id as storage_id, b.price from user_shop a "
                . " inner join comm_production_storage b on a.storage_id = b.id"
                . " inner join comm_product c on b.product_id = c.id"
                . " where a.id in({$ids}) and a.user_id = {$uid} and b.status = 1"
                . " order by id desc";

        $info = \common\models\user\UserShop::findBySql($sql)->asArray()->all();
   
        $address = \common\models\user\UserAddress::getByUserAuto($uid);

        $carriage = 0;
        $price = 0;
        foreach ($info as &$val) {
            
            $carriage += $val['carriage'];
            $price += $val['price'];
            $val['price'] = $val['price'] / 100;
            $val['carriage'] = $val['carriage'] / 100;
            $cover = json_decode($val['cover'], true);
            $val['cover'] = $cover[0];
        }

        $out["info"] = $info;
        $out["order"]["price"] = $price / 100;
        $out["order"]["carriage"] = $carriage / 100;
        $out["order"]["discount"] = 0;
        $out["address"]['id'] = $address['id'];
        $out["address"]['address'] = sprintf("%s,%s,%s %s", $address['province'],$address['city'],$address['county'],$address['address']);

        return $this->asJson(widgets\Response::sucess($out));
    }

    public function actionAdd() {

        $id = (int) Yii::$app->request->get("id");
        if (!$id) {
            $this->asJson(widgets\Response::error("参数错误"));
            return;
        }

        $storage = \common\models\comm\CommProductionStorage::getByid($id);

        if (!$storage) {
            $this->asJson(widgets\Response::error("参数错误!"));
            return;
        }

        $uid = widgets\User::getUid();
        $model = new \common\models\user\UserShop();
        $model->user_id = $uid;
        $model->storage_id = $id;
        $model->num = 1;
        if ($model->save()) {
            $id = $model->id;
            return $this->asJson(widgets\Response::sucess(['id' => $id]));
        } else {
            return $this->asJson(widgets\Response::error("添加失败"));
        }
    }

    public function actionDelete() {
        
        $id = (int) Yii::$app->request->get("id");
        if (!$id) {
            $this->asJson(widgets\Response::error("参数错误"));
            return;
        }

        $uid = widgets\User::getUid();
        $storage = \common\models\user\UserShop::findOne($id);
        if (!$storage) {
            $this->asJson(widgets\Response::error("参数错误!"));
            return;
        }
        
        if ($storage->user_id != $uid){
            $this->asJson(widgets\Response::error("参数错误2"));
            return;
        }

        if (\common\models\user\UserShop::deleteAll(["id" => $id])){
            return $this->asJson(widgets\Response::sucess(['id' => $id]));
            return;
        }
        
        return $this->asJson(widgets\Response::error("删除失败"));
    }
    
    public function actionUpdate(){
        
        $id = (int) Yii::$app->request->post("id");
        $num = (int) Yii::$app->request->post("num");
        $storage_id = (int) Yii::$app->request->post("storage_id");
        if (!$id){
            $this->asJson(widgets\Response::error("选择购物车Id"));
            return;
        }
        if (!$num){
            $this->asJson(widgets\Response::error("选择数量"));
            return;
        }
        if (!$storage_id){
            $this->asJson(widgets\Response::error("选择商品ID"));
            return;
        }
        
        $uid = widgets\User::getUid();
        $userShop = \common\models\user\UserShop::findOne($id);
        if (!$userShop || $userShop->user_id != $uid){
            $this->asJson(widgets\Response::error("信息不存在"));
            return;
        }
        
        $product = \common\models\comm\CommProductionStorage::find()->where(["id"=>$storage_id])->andWhere(["status" => 1])->one();
        if (!$product){
            $this->asJson(widgets\Response::error("商品不存在"));
            return;
        }
        
        if ($product['num'] < $num){
            $this->asJson(widgets\Response::error("库存不足"));
            return;
        }
        
        $userShop->storage_id = $storage_id;
        $userShop->num = $num;
        if (!$userShop->save()){
            $this->asJson(widgets\Response::error("修改失败"));
            return;
        }
        $this->asJson(widgets\Response::sucess("成功"));
        
    }

}
