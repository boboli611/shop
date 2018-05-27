<?php

namespace common\models\comm;

use Yii;

/**
 * This is the model class for table "comm_order_refund_log".
 *
 * @property integer $id
 * @property integer $order_id
 * @property integer $storage_id
 * @property integer $content
 * @property integer $refound
 * @property integer $expressage_status
 * @property integer $expressage_num
 * @property integer $price
 * @property integer $admin_id
 * @property integer $admin_nickname
 * @property string $updated_at
 * @property string $created_at
 */
class CommOrderRefundLog extends \common\models\BaseModel
{
    
    public static $expressage_status = ['2' => '未发货', '3' => '待收货', '4' => '已收货'];
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'comm_order_refund_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [[ 'price'], 'required'],
            [['order_id', 'refound', 'price', 'admin_id', 'admin_nickname'], 'integer'],
            [['updated_at', 'created_at'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_id' => '订单号',
            'refound' => '退单状态',
            'price' => '退款金额',
            'expressage_status' => '快递状态',
            'Storage Id' => '商品名称',
            'content' => '留言',
            'expressage_num' => '退货快递单号',
            'created_at' => 'Created At',
        ];
    }
}
