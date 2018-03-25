<?php
namespace frontend\service;
use \yii\db\Exception as Exception;
use common\constant\App;

class Banner {

    public static function get($position){
        $info = \common\models\comm\CommBanner::find()->where(['position' => $position, "status"=> App::APP_VAILD_STATUS])->one();
        if (!$info){
            return [];
        }
        
        $out = json_decode($info->img, true);
       
        $out = is_array($out) ? $out : [];
        foreach ($out as &$val){
            $val = \common\widgets\Oss::getImageUrl($val, 440);
        }
        
        return $out;
    } 

}
