<?php

namespace common\models\comm;

use Yii;

/**
 * This is the model class for table "comm_region".
 *
 * @property integer $id
 * @property integer $type
 * @property string $name
 * @property integer $price
 * @property integer $renew
 * @property string $updated_at
 * @property string $created_at
 */
class CommRegion extends \common\models\BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'comm_region';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'price', 'renew'], 'integer'],
            [['updated_at', 'created_at'], 'safe'],
            [['name'], 'string', 'max' => 32],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => 'Type',
            'name' => 'Name',
            'price' => 'Price',
            'renew' => 'Renew',
            'updated_at' => 'Updated At',
            'created_at' => 'Created At',
        ];
    }
}
