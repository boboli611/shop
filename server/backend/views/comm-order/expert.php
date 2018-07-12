<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\comm\CommProductSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '导出未发货订单';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="comm-product-index" style="width: 80%">


    <p>
        <?= Html::a('导出', ['export', "load" => 1], ['class' => 'btn btn-success']) ?>
    </p>
   

</div>
