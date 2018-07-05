<?php

namespace common\models\user;

use Yii;

/**
 * This is the model class for table "user_ticket".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $ticket_id
 * @property integer $money
 * @property integer $status
 * @property integer $end_time
 * @property string $updated_at
 * @property string $created_at
 */
class UserTicket extends \common\models\BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_ticket';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'ticket_id', 'money', 'status'], 'integer'],
            [['end_time'], 'string'],
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
            'money' => 'Money',
            'status' => 'Status',
            'end_time' => 'End Time',
            'updated_at' => 'Updated At',
            'created_at' => 'Created At',
        ];
    }
    
    /**
     * @inheritdoc
     * @return ActiveQuery the newly created [[ActiveQuery]] instance.
     */
    public static function getByUser($userId, $ticketId){
        return self::find()->where(['user_id' => $userId])->andWhere(["ticket_id" => $ticketId])->one();
    }
    
     /**
     * @inheritdoc
     * @return ActiveQuery the newly created [[ActiveQuery]] instance.
     */
    public static function getUserList($userId, $page, $limit = 10){
        
        $offset = $page * $limit;
        return self::find()->where(['user_id' => $userId])->limit($limit)->offset($offset)->all();
    }
}
