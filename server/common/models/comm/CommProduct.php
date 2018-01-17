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
 * @property integer $item_id
 * @property string $tag
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
            [['title', 'desc', 'tag', 'status', 'item_id'], 'required'],
            [['tag'], 'string', "max" => 4, "message" => "标签过长"],
            [['desc'], 'string'],
            [['item_id'], 'integer', 'min' => 1, "message" => "请选择类目"],
            [['price', 'sell', 'count', 'status', 'item_id'], 'integer'],
            [['updated_at', 'created_at'], 'safe'],
            [['title'], 'string', 'max' => 128, "message" => "标题过长"],
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
            'item_id' => '类别',
            'tag' => '标签',
            'status' => '下架',
            'updated_at' => '修改时间',
            'created_at' => '创建时间',
        ];
    }
    
    public function load($data, $formName = null){

        //$data['CommProduct']['cover'] = $data['cover_path'];
        $data['CommProduct']['price'] = intval($data['storage_price'][0] * 100);

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
