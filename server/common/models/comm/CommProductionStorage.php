<?php

namespace common\models\comm;

use Yii;

/**
 * This is the model class for table "comm_production_storage".
 *
 * @property integer $id
 * @property integer $product_id
 * @property string $style
 * @property string $size
 * @property integer $num
 * @property integer $price
 * @property integer $sell
 * @property integer $status
 * @property string $updated_at
 * @property string $created_at
 */
class CommProductionStorage extends \common\models\BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'comm_production_storage';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['product_id', 'style', 'size', 'status', 'price'], 'required'],
            [['product_id', 'num', 'status', 'sell'], 'integer'],
            [['price'], 'number'],
            [['style', 'size'], 'string', 'max' => 32],
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
            'style' => '风格',
            'size' => '尺码',
            'num' => '数量',
            'price' => '价格',
            'status' => '状态',
            'updated_at' => 'Update At',
            'created_at' => 'Created At',
        ];
    }
    
    public function load($data, $formName = null){

        $data['CommProductionStorage']['price'] = intval($data['CommProductionStorage']['price'] * 100);

        return parent::load($data);
    }
    
    public function getAllBPid($pid){
        $list =  self::find()->where(["product_id" => $pid])->all();
        foreach ($list as &$val){
            $val->price = $val->price / 100;
        }
        
        return $list;
    }
    
    public static function getByids($ids){
        $list =  self::find()->where(['in', "id" , $ids])->andwhere(['status' => 1])->all();
        foreach ($list as &$val){
            $val->price = $val->price / 100;
        }
 
        return $list;
    }
    
    public static function getByid($id){
        $item =  self::find()->where(['=', "id" , $id])->andwhere(['status' => 1])->one();
        if(!$item){
            return $item;
        }
        $item->price = $item->price / 100;
        
        return $item;
    }
    
    public static function getByUserId($uid){
        
        $list =  self::find()->where(["user_id" => $uid])->andwhere(['status' => 1])->all();
        foreach ($list as &$val){
            $val->price = $val->price / 100;
        }
        
        return $list;
    }
}
