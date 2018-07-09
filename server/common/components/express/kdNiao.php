<?php
namespace common\components\express;

//电商ID
defined('EBusinessID') or define('EBusinessID', '1324750');
//电商加密私钥，快递鸟提供，注意保管，不要泄漏
defined('AppKey') or define('AppKey', '92b0d201-6f1c-437f-b944-3d70668b1f83');
//请求url
defined('ReqURL') or define('ReqURL', 'http://api.kdniao.cc/Ebusiness/EbusinessOrderHandle.aspx');

/**
 * 快递鸟:http://www.kdniao.com
 * 免费每天3000次
 */
class kdNiao {

    public function run($No,$ShipperCode) {
        $logisticResult = $this->getOrderTracesByJson($No, $ShipperCode);
        $data = json_decode($logisticResult, true);
        if (!$data || $data['Success'] != 'true'){
                return [];
        }
        
        $message = $data['Traces'];
        $data['Traces'] = [];
        foreach ($message as $val){
            $data['Traces'][] = [
                'time' => $val['AcceptTime'],
                'ftime' => $val['AcceptTime'],
                'context' => $val['AcceptStation'],
            ];
        }
        
        krsort($data['Traces']);
        $data['Traces'] = array_values($data['Traces']);
        
        return $data;

    }

    /**
     * Json方式 查询订单物流轨迹
     */
    public function getOrderTracesByJson($No,$ShipperCode) {
        $requestData = "{'OrderCode':'','ShipperCode':'{$ShipperCode}','LogisticCode':'{$No}'}";

        $datas = array(
            'EBusinessID' => EBusinessID,
            'RequestType' => '1002',
            'RequestData' => urlencode($requestData),
            'DataType' => '2',
        );
        $datas['DataSign'] = $this->encrypt($requestData, AppKey);
        $result = $this->sendPost(ReqURL, $datas);


        return $result;
    }

    /**
     *  post提交数据 
     * @param  string $url 请求Url
     * @param  array $datas 提交的数据 
     * @return url响应返回的html
     */
    public function sendPost($url, $datas) {
        $temps = array();
        foreach ($datas as $key => $value) {
            $temps[] = sprintf('%s=%s', $key, $value);
        }
        $post_data = implode('&', $temps);
        $url_info = parse_url($url);
        if (empty($url_info['port'])) {
            $url_info['port'] = 80;
        }
        $httpheader = "POST " . $url_info['path'] . " HTTP/1.0\r\n";
        $httpheader.= "Host:" . $url_info['host'] . "\r\n";
        $httpheader.= "Content-Type:application/x-www-form-urlencoded\r\n";
        $httpheader.= "Content-Length:" . strlen($post_data) . "\r\n";
        $httpheader.= "Connection:close\r\n\r\n";
        $httpheader.= $post_data;
        $fd = fsockopen($url_info['host'], $url_info['port']);
        fwrite($fd, $httpheader);
        $gets = "";
        $headerFlag = true;
        while (!feof($fd)) {
            if (($header = @fgets($fd)) && ($header == "\r\n" || $header == "\n")) {
                break;
            }
        }
        while (!feof($fd)) {
            $gets.= fread($fd, 128);
        }
        fclose($fd);

        return $gets;
    }

    /**
     * 电商Sign签名生成
     * @param data 内容   
     * @param appkey Appkey
     * @return DataSign签名
     */
    public function encrypt($data, $appkey) {
        return urlencode(base64_encode(md5($data . $appkey)));
    }

}

?>