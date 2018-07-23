<?php

namespace common\models\data;

use Yii;

/**
 * This is the model class for table "data_analyze".
 *
 * @property integer $id
 * @property integer $visit_user
 * @property integer $visit_num
 * @property integer $cart_num
 * @property integer $pay_num
 * @property string $date
 */
class DataAnalyze extends \yii\db\ActiveRecord
{
    
    static $types = [
        'visit_user' => "访问人数",
        'visit_num' => "访问次数",
        'cart_num' => "加购数",
        'pay_num' => "购买次数",
    ];
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'data_analyze';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['visit_user', 'visit_num', 'cart_num', 'pay_num'], 'integer'],
            [['date'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'visit_user' => 'Visit User',
            'visit_num' => 'Visit Num',
            'cart_num' => 'Cart Num',
            'pay_num' => 'Pay Num',
            'date' => 'Date',
        ];
    }
}
