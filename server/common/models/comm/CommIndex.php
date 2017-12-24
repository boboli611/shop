<?php

namespace common\models\comm;

use Yii;

/**
 * This is the model class for table "comm_index".
 *
 * @property integer $id
 * @property integer $product_id
 * @property string $updated_at
 * @property string $created_at
 */
class CommIndex extends \common\models\BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'comm_index';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
             [['product_id'], 'required'],
            [['product_id'], 'integer'],
            [['updated_at', 'created_at'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'product_id' => '商品id',
            //'updated_at' => 'Updated At',
            //'created_at' => 'Created At',
        ];
    }
    
    public static function getAll(){
        return self::find()->all();
    }
}
