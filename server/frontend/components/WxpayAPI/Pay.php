<?php

namespace frontend\components\WxpayAPI;

require_once "lib/WxPay.Api.php";
//require_once "WxPay.JsApiPay.php";

class Pay {
    
    private static $notifyUrl = "http://paysdk.weixin.qq.com/example/notify.php";

    public static function pay($openId, $product) {
        
        $input = new \WxPayUnifiedOrder();
        $input->SetBody($product->title);
        $input->SetAttach($product->order_id);
        $input->SetOut_trade_no($product->order_id);
        $input->SetTotal_fee($product->price);
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 600));
        $input->SetGoods_tag("test");
        $input->SetNotify_url(self::$notifyUrl);
        $input->SetTrade_type("JSAPI");
        $input->SetOpenid($openId);
        var_dump($input,date("Y-m-d H:i:s"));
        $order = \WxPayApi::unifiedOrder($input);
        
        return $order;
    }

}
