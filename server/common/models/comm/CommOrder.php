<?php

namespace common\models\comm;

use Yii;

/**
 * This is the model class for table "comm_order".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $product_id
 * @property integer $price
 * @property integer $pay_price
 * @property integer $num
 * @property integer $adress_id
 * @property integer $status
 * @property string $updated_at
 * @property string $created_at
 */
class CommOrder extends \yii\db\ActiveRecord
{
    
    const status_add = 1;//下单
    const status_pay_sucess = 2;//支付成功
    const status_pay_fail = 3;//支付失败
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'comm_order';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'product_id', 'price', 'pay_price', 'num', 'adress_id', 'status'], 'integer'],
            [['updated_at', 'created_at'], 'string', 'max' => 32],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'product_id' => 'Product ID',
            'price' => 'Price',
            'pay_price' => 'Pay Price',
            'num' => 'Num',
            'adress_id' => 'Adress ID',
            'status' => 'Status',
            'updated_at' => 'Updated At',
            'created_at' => 'Created At',
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
    
    public static function findOne($condition) {
        return parent::findOne($condition);
    }

}