<?php

namespace common\widgets;

use Yii;

class Oss extends \yii\bootstrap\Widget {

    public static function getImageUrl($url, $size) {
        return "{$url}?x-oss-process=image/resize,w_$size";
    }

}
