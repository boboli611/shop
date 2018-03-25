<?php

namespace common\models\comm;

use Yii;

/**
 * This is the model class for table "comm_product_recommend".
 *
 * @property integer $id
 * @property integer $product_id
 * @property integer $sort
 * @property string $updated_at
 * @property string $created_at
 */
class CommProductRecommend extends \common\models\BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'comm_product_recommend';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['product_id', 'sort'], 'integer'],
            [['updated_at', 'created_at'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'product_id' => 'Product ID',
            'sort' => 'Sort',
            'updated_at' => 'Updated At',
            'created_at' => 'Created At',
        ];
    }
}
