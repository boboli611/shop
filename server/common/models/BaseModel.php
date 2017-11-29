<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

class BaseModel extends ActiveRecord {
    
    const status_add = 1;//下单
    const status_pay_sucess = 1;//支付成功
    const status_pay_fail = 1;//支付失败
    
    public function behaviors()
    {
        return [
            [
             'class' => TimestampBehavior::className(),
             'createdAtAttribute' => 'created_at',
             'updatedAtAttribute' => 'updated_at',
             'value' => new Expression('NOW()'),
         ],
        ];
    }
}