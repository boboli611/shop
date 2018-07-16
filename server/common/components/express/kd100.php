<?php

namespace common\components\express;

/**
 * 快递100 https://www.kuaidi100.com/
 * 免费每天2000次
 */
class kd100 {

    //快递公司名称 参数 num
    private static $companyUrl = "https://m.kuaidi100.com/autonumber/auto";
    //订单记录 --参数 type=zhongtong&postid=472347597886
    private static $expressLogUrl = "https://m.kuaidi100.com/query";

    public function get($order) {

        if (!$order) {
            return [];
        }
        $url = self::$companyUrl . "?num={$order->expressage}";
        $result = \common\widgets\Http::Get($url);
        $result = json_decode($result, true);

        if (!$result) {
            return [];
        }

        $comCode = $result[0]['comCode'];
        $url = self::$expressLogUrl . "?type={$comCode}&postid={$order->expressage}";
        $result = \common\widgets\Http::Get($url);

        $data = json_decode($result, true);
        if (!$data || $data['message'] != 'ok') {
            return [];
        }

        $out['Traces'] = $data['data'];
        $out['State'] = $data['state'];

        if ($out['State'] == 1) {
            foreach ($out['Traces'] as $val) {
                if (strpos($val['context'],"已签收") !== false) {
                    $out['State'] = 3;
                    break;
                }
            }
        }

        return $out;
    }

}
