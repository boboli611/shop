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
class ProductionController extends Controller {

    private $type_num = [1 => 1, 2 => 2, 3 => 3];

    public function init() {
        $this->enableCsrfValidation = false;
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex() {

        $page = (int) Yii::$app->request->get("page");
        $orderField = (int) Yii::$app->request->post("order_field");
        $order = (int) Yii::$app->request->post("order");
        $lastId = (int) Yii::$app->request->post("last_id");

        
        $item = \common\models\comm\CommProductItem::getByTitle("");
        $condition = [];
        if ($item) {
            $condition['item_id'] = $item->id;
        }

        $products = \frontend\service\Product::search($condition, "", $orderField, $order, $page);
        $products = $products ? $products : [];
        $out['list'] = [];


        $id = 0;
        $list = $types = $useList = [];
        $items = $products;
        foreach ($products as $k => $val) {
            $item = $val->toarray();
            $types[$val->type][] = $item;
        }

        foreach ($products as $k => $val) {
            
            $itemType = 0;
            
            if ($useList[$val->id]){
                continue;
            }
            
            if (!isset($types[$val->type])) {
                continue;
            }
            
            if (count($types[$val->type]) < $this->type_num[$val->type]){
                    $itemType = count($types[$val->type]);
            }else{
                $itemType = $val->type;
            }
            
            for ($i = count($list[$id]); $i < $this->type_num[$itemType]; $i++) {

                $item = @array_shift($types[$val->type]);
                $item && $item['type'] = $itemType;
                $item && $list[$id][] = $item;
                $item && $useList[$item['id']] = 1;
               
            }

            if (empty($types[$val->type])) {
                unset($types[$val->type]);
            }

            $id++;
        }

        $out['list'] = $list;
        return $this->asJson(widgets\Response::sucess($out));
    }
    
    
    
    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionSearch() {
       
        $title = Yii::$app->request->get("word");
        $page = (int) Yii::$app->request->get("page");
        $orderField = (int) Yii::$app->request->post("order_field");
        $order = (int) Yii::$app->request->post("order");
        $lastId = (int) Yii::$app->request->post("last_id");

        $searchStatus = true;
        $item = \common\models\comm\CommProductItem::getByTitle($title);
        if (!$item){
            $item = \common\models\comm\CommProductItem::getByTitle("");
            $title = "";
            $searchStatus = false;
        }
        $condition = [];
        if ($item) {
            $title = '';
            $condition['item_id'] = $item->id;
        }

        $products = \frontend\service\Product::search($condition, $title, $orderField, $order, $page, 12);
        $products = $products ? $products : [];
        $out['list'] = [];


        $i = 0;
        $list   = [];
        
        foreach ($products as $k => $val) {
            $item = $val->toarray();
            $item['type'] = 3;
            $list[$i][] = $item;
            if (count($list[$i]) == 3){
                $i++;
            }
        }

        $out['list'] = $list;
        $out['search'] = $searchStatus;
        return $this->asJson(widgets\Response::sucess($out));
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionDetail() {

        $id = (int) Yii::$app->request->get("id");
        if (!$id) {
            $this->asJson(widgets\Response::error("参数错误"));
            return;
        }


        $info = \common\models\comm\CommProduct::findOne($id);
        $info = $info->toArray();
        $products = \frontend\service\Product::search([], "", 2, 1, 1, 2);
        $products = $products ? $products : [];

        $modelStorage = new \common\models\comm\CommProductionStorage();
        $modelStorageList = $modelStorage->getAllBPid($id);

        $storage = [];
        foreach ($modelStorageList as $val) {
            $storage[$val->style][$val->size] = $val;
        }

        $info['price'] = $modelStorageList[0]->price;
        $info['storage'] = $storage;
        $out["info"] = $info;
        $out['recommend'] = $products;
        return $this->asJson(widgets\Response::sucess($out));
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionBuyInfo() {

        $id = (int) Yii::$app->request->get("id");
        if (!$id) {
            $this->asJson(widgets\Response::error("参数错误1"));
            return;
        }

        $uid = widgets\User::getUid();

        $storage = \common\models\comm\CommProductionStorage::findOne($id);
        if (!$storage) {
            $this->asJson(widgets\Response::error("参数错误2"));
            return;
        }

        $info = \common\models\comm\CommProduct::findOne($storage->product_id);
        $info = $info->toArray();
        if (!$info) {
            $this->asJson(widgets\Response::error("参数错误3"));
            return;
        }

        $address = \common\models\user\UserAddress::getByUserAuto($uid);

        $info['style'] = $storage->style;
        $info['size'] = $storage->size;
        $info['price'] = $storage->price / 100;
        $info['storage_id'] = $storage->id;
      
        
        //postage
        $out["info"] = $info;
        $out["order"]["price"] = $storage->price;
        $out["order"]["carriage"] = $info['carriage'];
        $out["order"]["discount"] = 50;
        $out["address"]['id'] = $address['id'];
        $out["address"]['address'] = $address['full_region'].$address['address'];
        return $this->asJson(widgets\Response::sucess($out));
    }

   

}
