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
 * @property integer $content
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
    
    public static $payName = [
        self::status_waiting_pay => "待付款",
        self::status_goods_waiting_send => "待发货",
        self::status_goods_waiting_receve => "待收货",
        self::status_goods_receve => "已收货",
    ];

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
            [['user_id', 'product_id', 'price', 'pay_price', 'num', 'status', 'address'], 'integer'],
            [['order_id', 'updated_at', 'created_at', 'expressage'], 'string', 'max' => 32],
            [['content'], 'string', 'max' => 256],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => '用户 ID',
            'order_id' => 'order_id',
            'product_id' => 'Product ID',
            'price' => 'Price',
            'pay_price' => 'Pay Price',
            'num' => 'Num',
            'address' => 'Adress ID',
            'status' => 'Status',
            'content' => 'Content',
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
    
    /**
     * @inheritdoc
     * @return static|null ActiveRecord instance matching the condition, or `null` if nothing matches.
     */
    public static function getByUser($userId, $type, $page, $limit = 10) {
        
        $page = $page > 0 ? $page - 1 :$page;
        $offset = $page * $limit;

        return self::find()->where(["user_id" => $userId])->andWhere(['status' => $type])->orderBy("id desc")->offset($offset)->limit($limit)->all();
    }
    
    public static function createOrderId($userId){
        
        return date("Ymd").time().mt_rand(10000, 99999);
        //return md5($userId.time().mt_rand(1,50000));
    }
    
    public static  function  get_type_text($id){
    return  self::$payName[$id];
    }


}