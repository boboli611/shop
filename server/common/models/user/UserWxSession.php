<?php

namespace common\models\user;

use Yii;

/**
 * This is the model class for table "user_wx_session".
 *
 * @property integer $user_id
 * @property string $open_id
 * @property string $session_key
 * @property string unionId
 * @property integer $expires_in
 * @property string $updated_at
 * @property string $created_at
 */
class UserWxSession extends \common\models\BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_wx_session';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['expires_in'], 'integer'],
            [['open_id', 'session_key', 'unionId'], 'string', 'max' => 32],
            [['updated_at', 'created_at'], 'string', 'max' => 20],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id' => 'User ID',
            'open_id' => 'Open ID',
            'session_key' => 'Session Key',
            'unionId' => 'unionId',
            'expires_in' => 'Expires In',
            'updated_at' => 'Updated At',
            'created_at' => 'Created At',
        ];
    }
    
    public static function getByOpendId($openId){
        return self::find()->where(["open_id" => $openId])->one();
    }
    
    public static function findOne($condition) {
        return parent::findOne($condition);
    }
}