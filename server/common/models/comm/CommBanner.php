<?php

namespace common\models\comm;

use Yii;

/**
 * This is the model class for table "comm_banner".
 *
 * @property integer $id
 * @property string $img
 * @property integer $position
 * @property integer $status
 * @property string $updated_at
 * @property string $created_at
 */
class CommBanner extends \common\models\BaseModel {

    const index_page_one = 1; //首页

    public static $postions = [
        self::index_page_one => "首页顶部",
    ];

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'comm_banner';
    }

    public function load($data, $formName = NULL) {
        //$data['CommBanner']['img'] = json_encode($data['img']);

        return parent::load($data);
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['img', 'position', 'product_id'], 'required'],
            [['img'], 'string'],
            [['status', 'product_id'], 'integer'],
            [['position','product_id'], 'integer', 'min' => 1, "message" => "选择广告位"],
            [['updated_at', 'created_at'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'img' => '图片',
            'position' => '位置',
            'status' => '状态',
            'updated_at' => '更新',
            'created_at' => '保存',
        ];
    }

}
