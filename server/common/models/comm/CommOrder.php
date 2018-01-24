<?php

namespace common\models\comm;

use Yii;

/**
 * This is the model class for table "comm_order".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $order_id
 * @property integer $product_id
 * @property integer $price
 * @property integer $pay_price
 * @property integer $num
 * @property integer $adress
 * @property integer $status
 * @property integer $expressage
 * @property string $updated_at
 * @property string $created_at
 */
class CommOrder extends \common\models\BaseModel
{
    
    const status_waiting_pay = 1;//待付款
    const status_goods_waiting_send = 2;//代发货
    const status_goods_waiting_receve = 3;//代收货
    const status_goods_receve = 4;//已收货
    const status_pay_fail = 9;//支付失败

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
            [['user_id', 'product_id', 'price', 'pay_price', 'num', 'status'], 'integer'],
            [['order_id', 'updated_at', 'created_at', 'expressage'], 'string', 'max' => 32],
            [['address'], 'string', 'max' => 256],
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
            'order_id' => 'order_id',
            'product_id' => 'Product ID',
            'price' => 'Price',
            'pay_price' => 'Pay Price',
            'num' => 'Num',
            'address' => 'Adress ID',
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
    
    /**
     * @inheritdoc
     * @return static|null ActiveRecord instance matching the condition, or `null` if nothing matches.
     */
    public static function getByOrderId($orderId) {
        return self::find()->where(["order_id" => $orderId])->one();
    }
    
    public static function createOrderId($userId){
        
        return md5($userId.time().mt_rand(1,50000));
    }

}