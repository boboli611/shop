<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace console\controllers;

use yii\console\Controller;
use common\models\comm\CommOrder;
/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class ExpressController extends Controller
{
    
    //快递公司名称 参数 num
    private static $companyUrl = "https://m.kuaidi100.com/autonumber/auto";
    
    //订单记录 --参数 type=zhongtong&postid=472347597886
    private static $expressLogUrl = "https://m.kuaidi100.com/query";
    /**
     * 保存快递记录
     * @param string $message the message to be echoed.
     */
    public function actionIndex($message = 'hello world')
    {
        $list = \common\models\comm\CommOrder::find()->where(['status' => CommOrder::status_goods_waiting_receve])->groupBy("order_id")->all();
        if (!$list){
            return;
        }

        foreach ($list as $val){
            $url = self::$companyUrl . "?num={$val->expressage}";
            $result = \common\widgets\Http::Get($url);
            $result = json_decode($result, true);
            if (!$result){
                continue;
            }
            
            $comCode = $result[0]['comCode'];
            $url= self::$expressLogUrl . "?type={$comCode}&postid={$val->expressage}";
            $result = \common\widgets\Http::Get($url);
            $data = json_decode($result, true);
            if (!$data || $data['message'] != 'ok'){
                continue;
            }
            
            $express = \common\models\comm\CommExpressLog::find()->where(['no' => $val->expressage])->one();
            if (!$express){
                $express = new \common\models\comm\CommExpressLog();
            }
            
            $express->no  = $val->expressage;
            $express->content  = json_encode($data['data']);
            $express->company  = $comCode;
            $express->save();
            
            echo $val->expressage . "保存成功\n";
        }
        
        echo $message . "\n";
    }

}