<?php
namespace frontend\service;

class Error {
    
    public static function addLog($userId, $msg, $info){
        
       $model = new \common\models\Error();
       $model->message = $msg;
       $model->info = $info;
       $model->user_id = $userId;
       
       return $model->save();
    }
}

