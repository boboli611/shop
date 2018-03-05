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

        $products = \frontend\service\Product::search([], "", $orderField, $order, $page);
        $products = $products ? $products : [];       

        $out['list'] = $products;
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
        $condition = [];
        if ($item) {
            $title = '';
            $condition['item_id'] = $item->id;
        }

        $products = \frontend\service\Product::search($condition, $title, $orderField, $order, $page, 10);
        if (!$products){
            $searchStatus = false;
            $products = \frontend\service\Product::search([], "", $orderField, $order, $page, 10);
        }
      

        $out['list'] = $products;
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
