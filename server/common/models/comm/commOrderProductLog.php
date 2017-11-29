<?php

namespace common\models\comm;

use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;

class commOrderProductLog extends ActiveRecord {
    
    public function rules()
    {
        return [
            [['name', 'email'], 'required'],
            ['name', 'email'],
        ];
    }

    public static function getListByPage($page, $limit = 10) {

        $ret = self::find()->where("id <= 10")
                ->orderBy("id desc")
                ->offset($page)
                ->limit($limit)
                ->all();
        
        return $ret;
    }
    
    
    public static function getDetail($id) {

        $ret = self::findOne($id);
        return $ret;
    }

}

