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
 * @property integer $num
 * @property integer $adress
 * @property integer $status
 * @property integer $expressage
 * @property integer $refund
 * @property integer $content
 * @property string $updated_at
 * @property string $created_at
 */
class CommOrder extends \common\models\BaseModel {

    const status_waiting_pay = 1; //待付款
    const status_goods_waiting_send = 2; //待发货
    const status_goods_waiting_receve = 3; //待收货
    const status_goods_receve = 4; //已收货
    const status_goods_close = 5; //订单关闭
    const status_pay_fail = 9; //支付失败
    
    const status_refund_no = 1; // '未申请',
    const status_refund_checking = 2; // '审核中',
    const status_refund_waiting = 3; // '退货中',
    //const status_refund_ok = 4; // '同意退货',
    const status_refund_sucess = 5; //'退货完成',
    const status_refund_fail = 9; // '退货未批准',

    public static $payName = [
        self::status_waiting_pay => "待付款",
        self::status_goods_waiting_send => "待发货",
        self::status_goods_waiting_receve => "待收货",
        self::status_goods_receve => "已收货",
        self::status_goods_close => "已关闭",
    ];
    public static $refund = [
        //self::status_refund_no => '未申请',
        self::status_refund_checking => '审核中',
        self::status_refund_waiting => '退货中',
       // self::status_refund_ok => '同意退货',
        self::status_refund_sucess => '退货完成',
        self::status_refund_fail => '退货未批准',
    ];

    public $username;
    public $title;

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'comm_order';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['user_id', 'product_id', 'num'], 'integer'],
            [['order_id', 'updated_at', 'created_at', 'expressage'], 'string', 'max' => 32],
            [['content'], 'string', 'max' => 256],
            [['address'], 'string', 'max' => 512],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'user_id' => '用户[ID]',
            'order_id' => '订单号',
            'product_id' => 'Product ID',
            'num' => '数量',
            'username' => '用户',
            'total' => "支付金额",
            'address' => 'Adress ID',
            'status' => '状态',
            'refund' => '退货',
            'content' => '用户留言',
            'expressage' => '快递单号',
            'ShipperCode' => "快递公司",
            'updated_at' => '更新时间',
            'created_at' => '创建时间',
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

        $page = $page > 0 ? $page - 1 : $page;
        $offset = $page * $limit;
        
        $model = self::find();
        if ($type){
            $model->where(['status' => $type]);
        }
        

        return $model->andWhere(["user_id" => $userId])->groupBy("order_id")->orderBy("id desc")->offset($offset)->limit($limit)->all();

    }
    
     /**
     * @inheritdoc
     * @return static|null ActiveRecord instance matching the condition, or `null` if nothing matches.
     */
    public static function getListInfo($userId, $type, $page, $limit = 10) {

        $page = $page > 0 ? $page - 1 : $page;
        $offset = $page * $limit;
        
        $model = self::find();
        $model->select(["comm_order.id","comm_order.order_id", "comm_order.user_id", "total","freight", "address", "status","refund", "expressage","content", "comm_order_product.*"]);
        $model->where("comm_order.status != ". CommOrder::status_pay_fail);
        if (in_array($type, [1,2,3])){
            $model->andWhere(['comm_order.status' => $type]);
        }elseif ($type == 4){
            $model->andWhere(['in','comm_order.status' , [4,5]]);
        }
        $model->join("inner join", "comm_order_product", "comm_order.order_id = comm_order_product.order_id");
        

        return $model->andWhere(["comm_order.user_id" => $userId])->orderBy("comm_order.id desc")->offset($offset)->limit($limit)->all();

    }
    
     /**
     * @inheritdoc
     * @return static|null ActiveRecord instance matching the condition, or `null` if nothing matches.
     */
    public static function getInfoByOrder($orderId, $userId) {

        $sql = "select comm_order.id,comm_order.freight,comm_order.order_id,comm_order.user_id,total,address,status,comm_order.refund,expressage,comm_order.content,pay_time,send_time,
            end_time,comm_order_product.*,comm_order_refund_log.refound as refound_status, comm_order_refund_log.id as refound_id from comm_order
            inner join comm_order_product on comm_order.order_id = comm_order_product.order_id
            left join comm_order_refund_log on comm_order_product.order_id = comm_order_refund_log.order_id and comm_order_product.product_id = comm_order_refund_log.storage_id
            where comm_order.order_id = {$orderId} and comm_order.user_id = {$userId} 
            order by comm_order.id desc    
            ";
        return self::findBySql($sql)->asArray()->all();
       

    }
    

    public static function createOrderId($userId) {

        return date("Ymd") . time() . mt_rand(10000, 99999);
        //return md5($userId.time().mt_rand(1,50000));
    }

    public static function get_type_text($id) {
        return self::$payName[$id];
    }

}
