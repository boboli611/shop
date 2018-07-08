<?php

namespace common\models\user;

use Yii;

/**
 * This is the model class for table "user_ticket_log".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $ticket_id
 * @property integer $order_id
 * @property integer $money
 * @property string $updated_at
 * @property string $created_at
 */
class UserTicketLog extends \common\models\BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_ticket_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'ticket_id', 'order_id', 'money'], 'integer'],
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
            'user_id' => 'User ID',
            'ticket_id' => 'Ticket ID',
            'order_id' => 'Product ID',
            'money' => 'money',
            'updated_at' => 'Updated At',
            'created_at' => 'Created At',
        ];
    }
}
