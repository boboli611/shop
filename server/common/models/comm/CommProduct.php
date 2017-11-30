<?php

namespace common\models\comm;

use Yii;

/**
 * This is the model class for table "comm_product".
 *
 * @property integer $id
 * @property string $title
 * @property string $desc
 * @property string $cover
 * @property integer $price
 * @property integer $sell
 * @property integer $count
 * @property string $update_time
 * @property string $create_time
 */
class CommProduct extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'comm_product';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['desc'], 'string'],
            [['price', 'sell', 'count', 'status'], 'integer'],
            [['update_time', 'create_time'], 'safe'],
            [['title'], 'string', 'max' => 128],
            [['cover'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => '标题',
            'desc' => '内容',
            'cover' => '封面',
            'price' => '价格',
            'sell' => '销量',
            'count' => '库存',
            'status' => '下架',
            'update_time' => '修改时间',
            'create_time' => '创建时间',
        ];
    }
}
