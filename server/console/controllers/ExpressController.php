<?php

/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace console\controllers;

use yii\console\Controller;
use common\models\comm\CommOrder;


class ExpressController extends Controller {

    
    /**
     * 保存快递记录
     * @param string $message the message to be echoed.
     */
    public function actionIndex($message = '') {
        
        $list = \common\models\comm\CommOrder::find()->where(['status' => CommOrder::status_goods_waiting_receve])->groupBy("order_id")->all();
        if (!$list) {
            return;
        }
        
        
        $kdniao = new \common\components\express\kdNiao();
        $kd100 = new \common\components\express\kd100();
        foreach ($list as $val) {
            if (!$val->expressage){
                continue;
            }
            
            echo "{$val->expressage} 开始查询 \n";
            if ($val->ShipperCode == "STO"){
                $data = $kd100->get($val);
            }else{
                $data = $kdniao->run($val->expressage, $val->ShipperCode);
            }
            
            //var_dump($val->expressage, $data);
            if (!$data) {
                continue;
            }
            
            $express = \common\models\comm\CommExpressLog::find()->where(['no' => $val->expressage])->one();
            if (!$express) {
                $express = new \common\models\comm\CommExpressLog();
            }

            
            $express->no = $val->expressage;
            $express->content = json_encode($data['Traces']);
            $express->state = $data['State'];
            $express->company = $val->ShipperCode;
            $express->save();

            echo $val->expressage . "保存成功\n";
        }

        echo "end\n";
    }

    /**
     * 保存快递记录
     * @param string $message the message to be echoed.
     */
    public function actionKuaidi($message = '') {
        $list = \common\models\comm\CommOrder::find()->where(['status' => CommOrder::status_goods_waiting_receve])->groupBy("order_id")->all();
        if (!$list) {
            return;
        }

        foreach ($list as $val) {
            $url = self::$companyUrl . "?num={$val->expressage}";
            $result = \common\widgets\Http::Get($url);
            $result = json_decode($result, true);

            if (!$result) {
                continue;
            }

            $comCode = $result[0]['comCode'];
            $url = self::$expressLogUrl . "?type={$comCode}&postid={$val->expressage}";
            $result = \common\widgets\Http::Get($url);
            var_dump($url);
            $data = json_decode($result, true);
            if (!$data || $data['message'] != 'ok') {
                continue;
            }

            $express = \common\models\comm\CommExpressLog::find()->where(['no' => $val->expressage])->one();
            if (!$express) {
                $express = new \common\models\comm\CommExpressLog();
            }

            $express->no = $val->expressage;
            $express->content = json_encode($data['data']);
            $express->state = $data['state'];
            $express->company = $comCode;
            $express->save();

            echo $val->expressage . "保存成功\n";
        }

        echo "end\n";
    }

}
