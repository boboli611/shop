<?php
namespace frontend\service;
use \yii\db\Exception as Exception;
use common\constant\App;

class Banner {

    public static function get($position){
        $info = \common\models\comm\CommBanner::find()->where(['position' => $position, "status"=> App::APP_VAILD_STATUS])->all();
       
        if (!$info){
            return [];
        }
        
     
        $out = [];
        foreach ($info as $val){
            $img = \common\widgets\Oss::getImageUrl($val->img, 440);
            $out[] = ['img' => $img, "product_id" => $val->product_id];
        }
 
        return $out;
    } 

}
