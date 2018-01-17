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
        
        $out['id'] = 1; 
        $this->asJson(widgets\Response::sucess($out));
    }
    
    public function actionDelete(){
        
        $out['id'] = 1; 
        $this->asJson(widgets\Response::sucess($out));
    }
    
    public function actionList(){
        
        $arr["id"] = 1;
        $arr['name'] = '张三';
        $arr['mobile'] = 13564887650; 
        $arr['full_region'] = '上海市徐汇区';
        $arr['address'] = "龙华街道123号801";
        $arr['status'] = 1;
        $out = [$arr,$arr,$arr,$arr];
        $this->asJson(widgets\Response::sucess($out));
    }
    public function actionDetail(){
        
        $arr["id"] = 1;
        $arr['name'] = '张三';
        $arr['mobile'] = 13564887650; 
        $arr['full_region'] = '上海市徐汇区';
        $arr['address'] = "龙华街道123号801";
        $arr['status'] = 1;

        $this->asJson(widgets\Response::sucess($arr));
    }
    
}