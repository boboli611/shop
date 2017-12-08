<?php

namespace frontend\components\WxpayAPI;

require_once "lib/WxPay.Api.php";
require_once 'lib/WxPay.Notify.php';

class PayNotify extends \WxPayNotify {

    //查询订单
    public function Queryorder($transaction_id) {
        $input = new \WxPayOrderQuery();
        $input->SetTransaction_id($transaction_id);
        $result = \WxPayApi::orderQuery($input);
        \Log::DEBUG("query:" . json_encode($result));
        if (array_key_exists("return_code", $result) && array_key_exists("result_code", $result) && $result["return_code"] == "SUCCESS" && $result["result_code"] == "SUCCESS") {
            return true;
        }
        return false;
    }

    //重写回调处理函数
    public function NotifyProcess($data, &$msg) {
        \Log::DEBUG("call back:" . json_encode($data));
        $notfiyOutput = array();

        if (!array_key_exists("transaction_id", $data)) {
            $msg = "输入参数不正确";
            return false;
        }
        //查询订单，判断订单真实性
        if (!$this->Queryorder($data["transaction_id"])) {
            $msg = "订单查询失败";
            return false;
        }

        try {
            (new \frontend\service\Pay())->storage($data['out_trade_no'], $data['total_fee']);
        } catch (Exception $exc) {
            echo $exc->getMessage();
            \frontend\service\Error::addLog($exc->getMessage(), json_encode($exc->errorInfo));
            exit;
        }

        return true;
    }

}
