<?php

namespace common\models\user;

use Yii;

/**
 * This is the model class for table "user_address".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $name
 * @property integer $mobile
 * @property integer $full_region
 * @property string $address
 * @property integer $status
 * @property string province
 * @property string city
 * @property string county
 * @property string $updated_at
 * @property string $created_at
 */
class UserAddress extends \common\models\BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_address';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'status', 'mobile'], 'integer'],
            [['name','province','city','county', 'address', 'updated_at', 'created_at'], 'string', 'max' => 255],
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
            'address' => 'Address',
            'status' => 'Status',
            'updated_at' => 'Updated At',
            'created_at' => 'Created At',
        ];
    }
    
    public static function findOne($condition) {
        return parent::findOne($condition);
    }
    
      /**
     * @return ActiveDataProvider
     * results in nothing.
     */
    public static function getByUserAuto($uid){
        return self::find()->where(['user_id'=>$uid, 'status' => 1])->one();
    }
}
