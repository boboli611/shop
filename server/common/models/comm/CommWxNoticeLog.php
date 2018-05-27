<?php

namespace common\models\comm;

use Yii;

/**
 * This is the model class for table "comm_wx_notice_log".
 *
 * @property integer $id
 * @property string $content
 * @property string $updated_at
 * @property string $created_at
 */
class CommWxNoticeLog extends \common\models\BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'comm_wx_notice_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['content'], 'string', 'max' => 512],
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
            'content' => 'Content',
            'updated_at' => 'Updated At',
            'created_at' => 'Created At',
        ];
    }
}
