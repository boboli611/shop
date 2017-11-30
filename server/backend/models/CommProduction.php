<?php

namespace backend\models;

use Yii;
use yii\base\Model;

class CommProduction extends Model
{
    public $name;
    public $email;
    public $cover;
    public function rules()
    {
        return [
            [['name', 'email'], 'required'],
            ['name', 'email'],
        ];
    }
}
