<?php

namespace common\models\comm;

use Yii;

/**
 * This is the model class for table "comm_ticket".
 *
 * @property integer $id
 * @property integer $title
 * @property integer $condition
 * @property integer $money
 * @property integer $index_show
 * @property string $duration
 * @property integer $status
 * @property integer $count
 * @property integer $num
 * @property string $updated_at
 * @property string $created_at
 */
class CommTicket extends \common\models\BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'comm_ticket';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title','condition', 'money','duration'], 'required'],
            [['condition', 'money', 'index_show', 'status', 'count', 'num'], 'integer'],
            [['updated_at', 'created_at'], 'safe'],
            [['title','duration'], 'string', 'max' => 255],
        ];  
    }
    
    public function load($data,$formName = null){
      
        $data['CommTicket']['money'] = $data['CommTicket']['money'] * 100;
        $data['CommTicket']['condition'] = $data['CommTicket']['condition'] * 100;
        if ($data['CommTicket']['duration']){
            $data['CommTicket']['duration'] = substr($data['CommTicket']['duration'], 0, 10) ." 23:59:59";
        }
        
        return parent::load($data);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => '标题',
            'condition' => '使用条件(金额)',
            'money' => '抵扣额度',
            'index_show' => '首页显示',
            'duration' => '有效期',
            'status' => '状态',
            'count' => '发行数量',
            'num' => '领取数量',
            'updated_at' => '更新时间',
        ];
    }
    
    public static function findOne($condition) {
        return parent::findOne($condition);
    }
}
