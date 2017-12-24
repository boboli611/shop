<?php

namespace common\models\comm;

use Yii;

/**
 * This is the model class for table "comm_product_item".
 *
 * @property integer $id
 * @property string $title
 * @property string $icon
 * @property integer $sort
 * @property string $updated_at
 * @property string $created_at
 */
class CommProductItem extends \common\models\BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'comm_product_item';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sort','title','icon'], 'required'],
            [['sort'], 'integer'],
            [['title'], 'string', 'max' => 32],
            [['icon'], 'string', 'max' => 255],
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
            'title' => '类名',
            'icon' => '图片',
            'sort' => '排序',
            'updated_at' => '更新时间',
            'created_at' => '创建时间',
        ];
    }
    
    public function load($data, $formName = null){

        $data['CommProductItem']['icon'] = $data['icon_path'];

        return parent::load($data);
    }
    
    public function getList(){
        $list = self::find()->all();
        $out = [];
        foreach($list as $v){
            $out[$v['id']] = $v['title'];
        }
        
        return $out;
    }
}
