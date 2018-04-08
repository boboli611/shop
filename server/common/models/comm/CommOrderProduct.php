<?php

namespace common\models\comm;

use Yii;

/**
 * This is the model class for table "comm_order_product_log".
 *
 * @property integer $id
 * @property integer $order_id
 * @property integer $product_id
 * @property integer $price
 * @property integer $pay_price
 * @property integer $num
 * @property string $update_time
 * @property string $create_time
 */
class CommOrderProduct extends \common\models\BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'comm_order_product';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_id', 'product_id', 'price', 'pay_price', 'num'], 'integer'],
            [['update_time', 'create_time'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_id' => 'Order ID',
            'product_id' => 'Product ID',
            'price' => 'Price',
            'pay_price' => 'Pay Price',
            'num' => 'Num',
            'update_time' => 'Update Time',
            'create_time' => 'Create Time',
        ];
    }
}
