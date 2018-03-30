<?php

namespace common\models\comm;

use Yii;

/**
 * This is the model class for table "comm_search_analyze".
 *
 * @property integer $id
 * @property string $title
 * @property integer $num
 * @property string $updated_at
 * @property string $created_at
 */
class CommSearchAnalyze extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'comm_search_analyze';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['num'], 'integer'],
            [['updated_at', 'created_at'], 'safe'],
            [['title'], 'string', 'max' => 32],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'num' => 'Num',
            'updated_at' => 'Updated At',
            'created_at' => 'Created At',
        ];
    }
}
