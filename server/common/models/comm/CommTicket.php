<?php

namespace common\models\comm;

use Yii;

/**
 * This is the model class for table "comm_ticket".
 *
 * @property integer $id
 * @property integer $condition
 * @property integer $money
 * @property integer $type
 * @property integer $index_show
 * @property string $duration
 * @property integer $status
 * @property integer $count
 * @property integer $num
 * @property string $updated_at
 * @property string $created_at
 */
class CommTicket extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'comm_ticket';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['condition', 'money', 'type', 'index_show', 'status', 'count', 'num'], 'integer'],
            [['updated_at', 'created_at'], 'safe'],
            [['duration'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'condition' => 'Condition',
            'money' => 'Money',
            'type' => 'Type',
            'index_show' => 'Index Show',
            'duration' => 'Duration',
            'status' => 'Status',
            'count' => 'Count',
            'num' => 'Num',
            'updated_at' => 'Updated At',
            'created_at' => 'Created At',
        ];
    }
    
    public static function findOne($condition) {
        return parent::findOne($condition);
    }
}
