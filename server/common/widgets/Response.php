<?php
namespace common\widgets;

use Yii;


class Response extends \yii\bootstrap\Widget
{
   public static function sucess($data){
       
       $out["sucess"] = true;
       $out["msg"] = "";
       $out["data"] = $data;
       $out["errno"] = 0;
       return $out;
   }
   
   public static function error($msg){
       
       $out["sucess"] = false;
       $out["msg"] = $msg;
       $out["data"] = [];
       return $out;
   }
}
