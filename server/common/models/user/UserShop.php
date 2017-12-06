<?php

namespace common\models\user;

use Yii;

/**
 * This is the model class for table "user_shop".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $product_id
 * @property integer $num
 * @property string $updated_at
 * @property string $created_at
 */
class UserShop extends \common\models\BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_shop';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'product_id', 'num'], 'integer'],
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
            'user_id' => 'User ID',
            'product_id' => 'Product ID',
            'num' => 'Num',
            'updated_at' => 'Updated At',
            'created_at' => 'Created At',
        ];
    }
    
    
    public static function getByUidPid($userId, $pid) {
        
        return self::find()->where(["user_id" => $userId])
               ->andWhere(["product_id" => $pid])
               ->one();
        
    }
    
    public static function getByUid($userId) {
        
        return self::find()->where(["user_id" => $userId])
               ->orderBy("id desc")
               ->all();
        
    }
    
    
    public static function findOne($condition) {
        return parent::findOne($condition);
    }
}
