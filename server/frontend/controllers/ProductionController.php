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

    public function init() {
        $this->enableCsrfValidation = false;
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex() {
        
        $title = Yii::$app->request->get("word");
        $page = (int)Yii::$app->request->post("page");
        $orderField = (int)Yii::$app->request->post("order_field");
        $order = (int)Yii::$app->request->post("order");
        
        $item = \common\models\comm\CommProductItem::getByTitle($title);
        $condition = [];
        if ($item){
            $title = '';
            $condition['item_id'] = $item->id;
        }

        $products = \frontend\service\Product::search($condition, $title, $orderField, $order, $page);
        $products = $products ? $products : [];
        $out['list'] = [];
        
        foreach ($products as $k =>  &$val){
            
            $val = $val->toarray();
            //$item['cover'] = "https://w3.hoopchina.com.cn/fe/68/7f/fe687fac1d41dfa75d03aaaf1ae176d8002.png";
            $id = intval($k / 3);
            
            $out['list'][$id] = is_array($out['list'][$id]) ? $out['list'][$id] : $val;
            if ($k % 3){
                $out['list'][$id]['list'] = isset($out['list'][$id]['list']) ? $out['list'][$id]['list'] : [];
                $out['list'][$id]['list'][] = $val;
            }
             
        }

        return $this->asJson(widgets\Response::sucess($out));
    }
    
    
    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionDetail() {
        
        $id = (int)Yii::$app->request->get("id");
        if (!$id){
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
        foreach ($modelStorageList as $val){
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
    public function actionInfo() {
        
        $id = (int)Yii::$app->request->get("id");
        if (!$id){
            $this->asJson(widgets\Response::error("参数错误"));
            return;
        }
        
        $storage = \common\models\comm\CommProductionStorage::findOne($id);
        if (!$storage){
            $this->asJson(widgets\Response::error("参数错误"));
            return;
        }

        $info = \common\models\comm\CommProduct::findOne($storage->product_id);
        $info = $info->toArray();
        if (!$info){
            $this->asJson(widgets\Response::error("参数错误"));
            return;
        }

        $info['style'] = $storage->style;
        $info['size'] = $storage->size;
        $info['price'] = $storage->price;
        $info['storage_id'] = $storage->id;
        $out["info"] = $info;
        $out["order"]["price"] =  $storage->price;
        $out["order"]["carriage_price"] =  10;
        $out["order"]["discount"] =  50;
        $out["address"] = "度搜粉红色的佛手动哈佛搜到和佛山";
        return $this->asJson(widgets\Response::sucess($out));
    }
    
    public function actionAddChart(){
        sleep(5);
        return $this->asJson(widgets\Response::sucess([]));
    }

    
    
   
}
