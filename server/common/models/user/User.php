<?php

namespace common\models\user;

use Yii;

/**
 * This is the model class for table "user".
 *
 * @property integer $id
 * @property string $username
 * @property string $img
 * @property integer $gender
 * @property string $city
 * @property string $province
 * @property string $country
 * @property integer $status
 * @property integer $login_at
 * @property integer $created_at
 * @property integer $updated_at
 */
class User extends \common\models\BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username', 'login_at', 'img'], 'required'],
            [['status','gender'], 'integer'],
            [['username', 'img'], 'string', 'max' => 255],
            [['city', 'province', 'country'], 'string', 'max' => 64],
            [['login_at'], 'string', 'max' => 20],
            [['username'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'img' => 'Img',
            "gender" => "gender",
            "city" => "city",
            "province" => "province",
            "country" => "country",
            'status' => 'Status',
            'login_at' => 'login At',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
    
    public static function findOne($condition) {
        return parent::findOne($condition);
    }
}
