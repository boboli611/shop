<?php
namespace common\widgets;

use Yii;


class User extends \yii\bootstrap\Widget
{
   public static function getUid(){
       
       $token = $_SERVER['HTTP_USER_TOKEN'];
       if (!$token){
           echo Response::encode(Response::error('未登录'));
           exit;
       }
       
       $uid = \common\models\user\UserWxSession::find()->where(['token'=> $token])->one();
       if(!$uid){
           echo Response::encode(Response::error('未登录!!!'));
           exit;
       }
       return $uid->user_id;
   } 
   
   
    public static function getUidUncheck(){
       
       $token = $_SERVER['HTTP_USER_TOKEN'];
       if (!$token){
           return 0;
       }
       
       $uid = \common\models\user\UserWxSession::find()->where(['token'=> $token])->one();
       if(!$uid){
           return 0;
       }
       return $uid->user_id;
   }
   
}
