<?php

namespace common\models\comm;

use Yii;

/**
 * This is the model class for table "comm_order_delete_log".
 *
 * @property integer $id
 * @property string $order_id
 * @property integer $user_id
 * @property integer $product_id
 * @property integer $total
 * @property integer $freight
 * @property integer $ticket
 * @property string $prepay_id
 * @property string $num
 * @property string $address
 * @property integer $status
 * @property integer $refund
 * @property integer $refund_status
 * @property string $expressage
 * @property string $return_expressage
 * @property string $content
 * @property string $pay_time
 * @property string $send_time
 * @property string $end_time
 * @property string $updated_at
 * @property string $created_at
 */
class CommOrderDeleteLog extends \common\models\BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'comm_order_delete_log';
    } 

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'product_id', 'total', 'freight', 'ticket', 'status', 'refund', 'refund_status'], 'integer'],
            [['pay_time', 'send_time', 'end_time'], 'safe'],
            [['order_id', 'num', 'updated_at', 'created_at'], 'string', 'max' => 32],
            [['prepay_id', 'expressage', 'notice'], 'string', 'max' => 64],
            [['address'], 'string', 'max' => 512],
            [['content'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_id' => 'Order ID',
            'user_id' => 'User ID',
            'product_id' => 'Product ID',
            'total' => 'Total',
            'freight' => 'Freight',
            'ticket' => 'Ticket',
            'prepay_id' => 'Prepay ID',
            'num' => 'Num',
            'address' => 'Address',
            'status' => 'Status',
            'refund' => 'Refund',
            'refund_status' => 'Refund Status',
            'expressage' => 'Expressage',
            'notice' => 'notice',
            'content' => 'Content',
            'pay_time' => 'Pay Time',
            'send_time' => 'Send Time',
            'end_time' => 'End Time',
            'updated_at' => 'Updated At',
            'created_at' => 'Created At',
        ];
    }
}
