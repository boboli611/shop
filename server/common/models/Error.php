<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "error".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $message
 * @property string $info
 * @property string $updated_at
 * @property string $created_at
 */
class Error extends BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'error';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id'], 'integer'],
            [['message', 'info'], 'string', 'max' => 255],
            [['updated_at', 'created_at'], 'string', 'max' => 20],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'Uid',
            'message' => 'Message',
            'info' => 'Info',
            'updated_at' => 'Updated At',
            'created_at' => 'Created At',
        ];
    }
}
