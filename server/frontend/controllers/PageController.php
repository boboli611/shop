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
        
        $out["showcase"] = array_shift($products);
        $out["list"] = $products;
        $this->asJson(widgets\Response::sucess($out));

    }
    
    
    public function actionIndexBak(){
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
        
        $out["showcase"] = array_shift($products);
        $out["list"] = $products;
        $this->asJson(widgets\Response::sucess($out));

    }
}

