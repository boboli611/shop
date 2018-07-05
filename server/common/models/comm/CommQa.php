<?php

namespace common\models\comm;

use Yii;

/**
 * This is the model class for table "comm_qa".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $content
 * @property integer $status
 * @property integer $admin_id
 * @property string $updated_at
 * @property string $created_at
 */
class CommQa extends \common\models\BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'comm_qa';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'status', 'admin_id'], 'integer'],
            [['updated_at', 'created_at'], 'safe'],
            [['content'], 'string', 'max' => 255],
            [['phone'], 'string', 'max' => 32],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => '用户id',
            'content' => '留言',
            'status' => '状态',
            'admin_id' => 'Admin ID',
            'updated_at' => 'Updated At',
            'created_at' => '时间',
        ];
    }
}
