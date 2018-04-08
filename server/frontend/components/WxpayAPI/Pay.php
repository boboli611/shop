<?php

namespace frontend\components\WxpayAPI;

require_once "lib/WxPay.Api.php";
//require_once "WxPay.JsApiPay.php";

class Pay {
    
    private static $notifyUrl = "https://www.ttyouhiu.com/wx/notice";

    public static function pay($openId, $product) {

        $appid = \yii::$app->params["wx"]['appId'];
        $mchId = \yii::$app->params["wx"]['mchId'];
        $input = new \WxPayUnifiedOrder();
        $input->SetAppid($appid);
        $input->SetMch_id($mchId);
        $input->SetBody("lipez");
        $input->SetAttach($product->order_id);
        $input->SetOut_trade_no($product->order_id);
        $input->SetTotal_fee($product->price);
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 600));
        $input->SetGoods_tag("goods");
        $input->SetNotify_url(self::$notifyUrl);
        $input->SetTrade_type("JSAPI");
        $input->SetOpenid($openId);

        $order = \WxPayApi::unifiedOrder($input);

        return $order;
    }
    
    
     public static function query($product) {

        $appid = \yii::$app->params["wx"]['appId'];
        $mchId = \yii::$app->params["wx"]['mchId'];
        $input = new \WxPayOrderQuery();
        $input->SetAppid($appid);
        $input->SetMch_id($mchId);
        $input->SetOut_trade_no($product->order_id);

        $order = \WxPayApi::orderQuery($input);

        return $order;
    }

}
