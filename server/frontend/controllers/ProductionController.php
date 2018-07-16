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

        $recommendIds = \frontend\service\Product::getRecommond(1);
        $products = \frontend\service\Product::search(['not in', "id", $recommendIds], "", $orderField, $order, $page);
        $products = $products ? $products : []; 
        foreach ($products as &$val){
            $val['cover'] = json_decode($val['cover'], true);
            $val['cover'] = $val['cover'][0];
            $val['price'] = $val['price']/ 100;
        }
        
        $recommend = \frontend\service\Product::search(['in', "id", $recommendIds], "", $orderField, $order, $page);
        foreach ($recommend as &$recom){
            $img = json_decode($recom->cover, TRUE);
            $recom->cover = $img[0];
            $recom->price = $recom->price / 100;
        }
        
        //类目
        $items = (new \common\models\comm\CommProductItem())->getListBySort();
        $uid = widgets\User::getUidUncheck();
        $tickets = (new \frontend\service\Ticket())->getIndex($uid);
        
        $out['ticket'] = $tickets;
        $out['item'] = $items;
        $out['list'] = $products;
        $out['recommend'] = $recommend;
        $out['banner'] = \frontend\service\Banner::get(\common\models\comm\CommBanner::index_page_one);
        return $this->asJson(widgets\Response::sucess($out));
    }
    
    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionList() {

        $page = (int) Yii::$app->request->get("p");

        $recommendIds = \frontend\service\Product::getRecommond(1);
        $products = \frontend\service\Product::search(['not in', "id", $recommendIds], "", "", "", $page);
        $products = $products ? $products : []; 
        foreach ($products as &$val){
            $val['cover'] = json_decode($val['cover'], true);
            $val['cover'] = $val['cover'][0];
        }

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

        $products = \frontend\service\Product::search($condition, $title, $orderField, $order, $page);
        if (!$products){
            $searchStatus = false;
            $products = \frontend\service\Product::search([], "", $orderField, $order, $page);
           
        }
        
        foreach ($products as &$val){
            $val['cover'] = json_decode($val['cover'], true);
            $val['cover'] = $val['cover'][0];
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
        $info['info'] = json_decode($info['info'], true);
        $pattern='/<img((?!src).)*src[\s]*=[\s]*[\'"](?<src>[^\'"]*)[\'"]/i';

        preg_match_all($pattern,$info['desc'],$desc);
        $info['desc'] = is_array($desc['src']) ? $desc['src'] : [];

        $recommendIds = \frontend\service\Product::getRecommond(2);
        $products = \frontend\service\Product::search(['in', "id", $recommendIds], "", "", "", 1);
        $products = $products ? $products : [];
        //var_dump($products);exit;
        foreach ($products as &$v){
    
            $v['cover'] = json_decode($v['cover'], true);
            $v['cover'] = $v['cover'][0];
        }

        $modelStorage = new \common\models\comm\CommProductionStorage();
        $modelStorageList = $modelStorage->getAllBPid($id);

        $storage = [];
        foreach ($modelStorageList as $val) {
            $arr['style'] = $val['style'];
            $arr['size'] = $val['size'];
            $arr['num'] = $val['num'];
            $arr['id'] = $val['id'];
            $storage[] = $arr;
        }

        $info['cover'] = json_decode($info['cover'], true);
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
        $buyNum = (int) Yii::$app->request->get("buy_num");
        $ticket_id = (int) Yii::$app->request->get("ticket_id");
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

        $cover = json_decode($info['cover'], true);

        $info['cover'] = $cover[0];
        $info['style'] = $storage->style;
        $info['size'] = $storage->size;
        $info['price'] = $storage->price * $buyNum / 100;
        $info['storage_id'] = $storage->id;
        $info['buy_num'] = $buyNum;
      
        $address = isset($address) ? $address->toArray() : [];
        $address["full_addres"] = sprintf("%s,%s,%s %s", $address['province'],$address['city'],$address['county'],$address['address']);
        
        $ticket = [];
        if ($ticket_id){
            $ticketInfo = (new \frontend\service\Ticket())->getUserTicketId($uid, $ticket_id);
            $price = $price - (int)$ticketInfo['money'];
            $ticket['money'] = $ticketInfo['money'] / 100;
            $ticket['condition'] = $ticketInfo['condition'] / 100;
        }
        
        $carriage = \frontend\service\ExpressFee::sumPrice($address['province'], $buyNum);
        //postage
        $out["info"] = $info;
        $out["order"]["price"] = ($storage->price * $buyNum + (int) $carriage -(int)$ticketInfo['money']) / 100;
        $out["order"]["carriage"] = $carriage / 100;
        $out['ticket'] = $ticket;
        //$out["order"]["discount"] = 50;
        $out["address"] = $address; 
        return $this->asJson(widgets\Response::sucess($out));
    }

   

}
