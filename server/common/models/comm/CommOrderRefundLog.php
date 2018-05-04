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
            'order_id' => 'Order ID',
            'refound' => 'Refound ID',
            'price' => '退款金额',
            'admin_id' => 'Admin ID',
            'admin_nickname' => 'Admin Nickname',
            'updated_at' => 'Updated At',
            'created_at' => 'Created At',
        ];
    }
}
