<?php

namespace common\models\comm;

use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

class commOrder extends \common\models\BaseModel {
    
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


    public function rules()
    {
        return [
            ['product_id', 'required',"message" => "商品id不为空"],
        ];
    }

    public static function getListByPage($page, $limit = 10) {

        $ret = self::find()->where("id <= 10")
                ->orderBy("id desc")
                ->offset($page)
                ->limit($limit)
                ->all();
        
        return $ret;
    }
    
    
    public static function getDetail($id) {

        $ret = self::findOne($id);
        return $ret;
    }

}

