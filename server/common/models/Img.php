<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "img".
 *
 * @property integer $id
 * @property string $img
 * @property string $updated_at
 * @property string $created_at
 */
class Img extends BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'img';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['img'], 'string', 'max' => 255],
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
            'img' => 'Img',
            'updated_at' => 'Updated At',
            'created_at' => 'Created At',
        ];
    }
}
