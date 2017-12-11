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
class CommProduct extends \common\models\BaseModel
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
            [['updated_at', 'created_at'], 'safe'],
            [['title'], 'string', 'max' => 128],
            [['cover'], 'string'],
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
            'updated_at' => '修改时间',
            'created_at' => '创建时间',
        ];
    }
    
    public function load($data, $formName = null){

        $data['CommProduct']['cover'] = json_encode($data['cover_path']);
        return parent::load($data);
    }
    
    public static function findOne($condition) {
        return parent::findOne($condition);
    }
    
    /**
     * @param array $condition
     * @param string $order
     * @param int $limit
     * @return type
     */
    public static function getList($condition, $key, $order, $page, $limit = 10){
        
        $model = self::find()->where("1");
        if ($condition){
            $model->andWhere($condition);
        }
        
        if ($key){
            $model->andWhere("title like '%{$key}%'");
        }
        
        $ret = $model->orderBy($order)->offset($page)->limit($limit)->all();
        return $ret;
    }
}
