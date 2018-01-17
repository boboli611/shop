<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

class BaseModel extends ActiveRecord {
    
    public function behaviors()
    {
        return [
            [
             'class' => TimestampBehavior::className(),
             'createdAtAttribute' => 'created_at',
             'updatedAtAttribute' => 'updated_at',
             'value' =>  date("Y-m-d H:i:s"),
         ],
        ];
    }
}