<?php

namespace common\models\comm;

use Yii;

/**
 * This is the model class for table "comm_express_log".
 *
 * @property integer $id
 * @property string $no
 * @property string $company
 * @property string $content
 * @property string $created_at
 * @property string $updated_at
 */
class CommExpressLog extends \common\models\BaseModel
{
    
    static $company = ['shunfeng' => '顺丰','yuantong' => "圆通", 'shentong' => '申通', 'zhongtong'=>'中通'];
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'comm_express_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['no'], 'required'],
            [['content'], 'string'],
            [['no'], 'string', 'max' => 32],
            [['company'], 'string', 'max' => 64],
            [['created_at', 'updated_at'], 'string', 'max' => 20],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'no' => 'No',
            'company' => 'Company',
            'content' => 'Content',
            'created_at' => 'Created At',
            'updated_at' => 'Update At',
        ];
    }
}
