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
class BannerController extends Controller {
    
    public function actionInfo(){
        
        $out["img_url"] = "https://ss3.bdstatic.com/70cFv8Sh_Q1YnxGkpoWK1HF6hhy/it/u=3725915652,1920562135&fm=27&gp=0.jpg"; 
        $out["name"] = "公司就欧式机";
        $this->asJson(widgets\Response::sucess($out));
    }
}
