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
class PageController extends Controller {
    
    public function actionIndex_bak(){
        $out['banner'] = "http://img.alicdn.com/imgextra/i6/TB1dUD5XJHO8KJjSZFtYXIhfXXa_M2.SS2_430x430q90.jpg";
        $out['msg'] = "享受免费配送服务 & 免费退货";
    }
    
    public function actionIndex(){
        
        $list =  \common\models\comm\CommIndex::getAll();
        $ids = [];
        foreach ($list as $v){
            $ids[] = $v['product_id'];
        }
        
        $ids = implode(",", $ids);
        /*
        $products = \common\models\comm\CommProduct::find()->select('comm_product.*,comm_product_item.title as item')
                ->join('LEFT JOIN','comm_product_item','comm_product.item_id = comm_product_item.id')
                ->filterWhere(['in','comm_product.id',$ids])
                ->andFilterWhere(['=', "comm_product.status", 1])
                ->all();
               //->createCommand()->getRawSql();
        */
        
        $sql = "SELECT `comm_product`.*, `comm_product_item`.`title` AS `item` FROM `comm_product` "
                . "LEFT JOIN `comm_product_item` ON comm_product.item_id = comm_product_item.id "
                . "WHERE (`comm_product`.`id` IN ({$ids})) AND (`comm_product`.`status` = 1)";
                
        $products = \common\models\comm\CommProduct::findBySql($sql)->asArray()->all();
        $this->asJson(widgets\Response::sucess($products));
return;       
// var_dump($products);exit;
        $out['banner'][0]["id"] = 1; 
        $out['banner'][0]["link"] = "https://www.baidu.com"; 
        $out['banner'][0]["image_url"] = "https://ss3.bdstatic.com/70cFv8Sh_Q1YnxGkpoWK1HF6hhy/it/u=3725915652,1920562135&fm=27&gp=0.jpg"; 
        $out['banner'][1]["id"] = 1; 
        $out['banner'][1]["link"] = "https://www.baidu.com"; 
        $out['banner'][1]["image_url"] = "https://ss1.bdstatic.com/70cFuXSh_Q1YnxGkpoWK1HF6hhy/it/u=938658904,399774595&fm=27&gp=0.jpg"; 
        $out['banner'][2]["id"] = 1; 
        $out['banner'][2]["link"] = "https://www.baidu.com"; 
        $out['banner'][2]["image_url"] = "https://ss1.bdstatic.com/70cFuXSh_Q1YnxGkpoWK1HF6hhy/it/u=146055988,3665972215&fm=27&gp=0.jpg"; 
        
        //供应商
        $out["channel"][0]["id"] = 1;
        $out["channel"][0]["icon_url"] ="https://ss0.bdstatic.com/70cFuHSh_Q1YnxGkpoWK1HF6hhy/it/u=3617910610,1840682386&fm=200&gp=0.jpg";
        $out["channel"][0]["name"] = "test1";
        $out["channel"][1]["id"] = 2;
        $out["channel"][1]["icon_url"] ="https://ss0.bdstatic.com/70cFuHSh_Q1YnxGkpoWK1HF6hhy/it/u=3617910610,1840682386&fm=200&gp=0.jpg";
        $out["channel"][1]["name"] = "test2";
        $out["channel"][2]["id"] = 2;
        $out["channel"][2]["icon_url"] ="https://ss0.bdstatic.com/70cFuHSh_Q1YnxGkpoWK1HF6hhy/it/u=3617910610,1840682386&fm=200&gp=0.jpg";
        $out["channel"][2]["name"] = "test2";
        $out["channel"][3]["id"] = 2;
        $out["channel"][3]["icon_url"] ="https://ss0.bdstatic.com/70cFuHSh_Q1YnxGkpoWK1HF6hhy/it/u=3617910610,1840682386&fm=200&gp=0.jpg";
        $out["channel"][3]["name"] = "test2";
        $out["channel"][4]["id"] = 2;
        $out["channel"][4]["icon_url"] ="https://ss0.bdstatic.com/70cFuHSh_Q1YnxGkpoWK1HF6hhy/it/u=3617910610,1840682386&fm=200&gp=0.jpg";
        $out["channel"][4]["name"] = "test2";
        $out["channel"][5]["id"] = 2;
        $out["channel"][5]["icon_url"] ="https://ss0.bdstatic.com/70cFuHSh_Q1YnxGkpoWK1HF6hhy/it/u=3617910610,1840682386&fm=200&gp=0.jpg";
        $out["channel"][5]["name"] = "test2";
        
        //商品
        $out["brand"][0]["id"] = 1;
        $out["brand"][0]["new_pic_url"] = "https://ss0.bdstatic.com/70cFvHSh_Q1YnxGkpoWK1HF6hhy/it/u=1353498151,4264969287&fm=27&gp=0.jpg";
        $out["brand"][0]["name"] = "商品1";
        $out["brand"][0]["floor_price"] = "10";
        
        $out["brand"][1]["id"] = 2;
        $out["brand"][1]["new_pic_url"] = "https://ss0.bdstatic.com/70cFvHSh_Q1YnxGkpoWK1HF6hhy/it/u=1353498151,4264969287&fm=27&gp=0.jpg";
        $out["brand"][1]["name"] = "商品2";
        $out["brand"][1]["floor_price"] = "10.01";
        
        //新品
        $out["newGoods"][0]["id"] = 1;
        $out["newGoods"][0]["list_pic_url"] = "https://ss0.bdstatic.com/70cFvHSh_Q1YnxGkpoWK1HF6hhy/it/u=1353498151,4264969287&fm=27&gp=0.jpg";
        $out["newGoods"][0]["name"] = "商品1";
        $out["newGoods"][0]["retail_price"] = "10";
        
        $out["newGoods"][1]["id"] = 2;
        $out["newGoods"][1]["list_pic_url"] = "https://ss0.bdstatic.com/70cFvHSh_Q1YnxGkpoWK1HF6hhy/it/u=1353498151,4264969287&fm=27&gp=0.jpg";
        $out["newGoods"][1]["name"] = "商品2";
        $out["newGoods"][1]["retail_price"] = "10.01";
        
        //热门商品
        $out["hotGoods"][0]["id"] = 1;
        $out["hotGoods"][0]["list_pic_url"] = "https://ss0.bdstatic.com/70cFvHSh_Q1YnxGkpoWK1HF6hhy/it/u=1353498151,4264969287&fm=27&gp=0.jpg";
        $out["hotGoods"][0]["name"] = "商品1";
        $out["hotGoods"][0]["retail_price"] = "10";
        $out["hotGoods"][0]["goods_brief"] = "到付搜狐佛山佛山房搜,到放假时间佛搜方式见of奇偶瑟吉欧,方式见佛顶山奇偶方式见";
        
        $out["hotGoods"][1]["id"] = 2;
        $out["hotGoods"][1]["list_pic_url"] = "https://ss0.bdstatic.com/70cFvHSh_Q1YnxGkpoWK1HF6hhy/it/u=1353498151,4264969287&fm=27&gp=0.jpg";
        $out["hotGoods"][1]["name"] = "商品2";
        $out["hotGoods"][1]["retail_price"] = "10.01";
        $out["hotGoods"][1]["goods_brief"] = "到付搜狐佛山佛山房搜到放假奇偶方式见";
        
        
         //专题
        $out["topics"][0]["id"] = 1;
        $out["topics"][0]["scene_pic_url"] = "https://ss0.bdstatic.com/70cFvHSh_Q1YnxGkpoWK1HF6hhy/it/u=1353498151,4264969287&fm=27&gp=0.jpg";
        $out["topics"][0]["title"] = "商品1";
        $out["topics"][1]["subtitle"] = "bbbbb";
        $out["topics"][0]["price_info"] = "10";
        
        $out["topics"][1]["id"] = 2;
        $out["topics"][1]["scene_pic_url"] = "https://ss0.bdstatic.com/70cFvHSh_Q1YnxGkpoWK1HF6hhy/it/u=1353498151,4264969287&fm=27&gp=0.jpg";
        $out["topics"][1]["title"] = "商品2";
        $out["topics"][1]["subtitle"] = "bbbbbbb";
        $out["topics"][1]["price_info"] = "10.01";

        
        $out["floorGoods"][0]["id"] = 1;
        $out["floorGoods"][0]["name"] = "哈哈哈";
        $out["floorGoods"][0]["goodsList"][0]["id"] = 1;
        $out["floorGoods"][0]["goodsList"][0]["list_pic_url"] = "https://ss0.bdstatic.com/70cFvHSh_Q1YnxGkpoWK1HF6hhy/it/u=1353498151,4264969287&fm=27&gp=0.jpg";
        $out["floorGoods"][0]["goodsList"][0]["name"] = "搜到和佛山ggggg";
        $out["floorGoods"][0]["goodsList"][0]["retail_price"] = 1.11;
        $out["floorGoods"][0]["goodsList"][1]["id"] = 2;
        $out["floorGoods"][0]["goodsList"][1]["list_pic_url"] = "https://ss0.bdstatic.com/70cFvHSh_Q1YnxGkpoWK1HF6hhy/it/u=1353498151,4264969287&fm=27&gp=0.jpg";
        $out["floorGoods"][0]["goodsList"][1]["name"] = "sdfsfsfs";
        $out["floorGoods"][0]["goodsList"][1]["retail_price"] = 2.11;
        
        $this->asJson(widgets\Response::sucess($out));
    }
}

