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
class OrderController extends Controller
{
    
    /**
     * 关闭无效订单
     * @param string $message the message to be echoed.
     */
    public function actionClosePayStatus()
    {
        $time = date("Y-m-d H:i:s", time() - 3600);
        CommOrder::updateAll(["status"=> CommOrder::status_refund_fail], "created_at <= '$time' and status = 1");
    }
    
    
     /**
     * 关闭订单
     * @param string $message the message to be echoe
     */
    public function actionUpdateStatus()
    {
        $list = CommOrder::find()->where(['in', 'status', [2,3,4]])->all();
        $endTime = date("Y-m-d H:i:s", time() - 3600 * 10);
        foreach ($list as $order){
            
            if ($order->created_at <= $endTime){
                $order->status = CommOrder::status_goods_close;
                $order->save();
                echo $order->id . " status 5 sucess \n";
                continue;
            }
            
            $expressage = \common\models\comm\CommExpressLog::find()->where(['no' => $order->expressage])->one();
            if (!$expressage){
                continue;
            }

            if ($expressage->state == 1 || $expressage->state == 2){
                $order->status = CommOrder::status_goods_waiting_receve;
                $order->save();
                echo $order->id . "status 3 sucess \n";
                continue;
            }
            
            if ($expressage->state == 3){
                $order->status = CommOrder::status_goods_receve;
                $order->save();
                echo $order->id . "status 4   sucess \n";
                continue;
            }
            
        }
    }

}