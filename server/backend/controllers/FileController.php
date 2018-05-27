<?php

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
use yii\web\UploadedFile;
use yii\web\Response;

class FileController extends Controller {

    public $enableCsrfValidation = false;

    public function behaviors() {
        return [
            [
                'class' => 'yii\filters\ContentNegotiator',
                'formats' => [
                    'application/json' => Response::FORMAT_JSON
                ],
            ]
        ];
    }

    /**
     * 编辑器上传图片
     */
    public function actionUploadRedactor() {

        $img = UploadedFile::getInstanceByName('file');
        if (!$img->tempName) {
            $this->asJson(\common\widgets\Response::error("上传失败"));
        }
        /** @var \yiier\AliyunOSS\OSS $oss */
        $oss = \Yii::$app->get('oss');
        $day = date("Ymd");
        $files = explode('.', $img->name);
        $fileName = md5($img->name . time()) . '.' . end($files);
        $fh = \Yii::$app->params['uploadPath'] . "/image/{$day}/{$fileName}";
        $ret = $oss->upload($fh, $img->tempName); // 会自动创建文件夹
        if (!$ret) {
            $this->asJson(\common\widgets\Response::error("上传失败"));
        }

        //$data["filelink"] = $p1 = \Yii::$app->params['static_domain'].$fh; 
        $data["filelink"] = $p1 = $ret["info"]["url"];
        $data["id"] = "1234";
        $this->asJson($data);
    }

    public function actionInputUpload() {

        $file = current($_FILES);
        $tmp_name = current($file['tmp_name']);
        $name = current($file['name']);

        // $p1 $p2是我们处理完图片之后需要返回的信息，其参数意义可参考上面的讲解
        $p1 = $p2 = [];
        // 如果没有商品图或者商品id非真，返回空
        if (!$tmp_name || !$name) {
            //$this->asJson(\common\widgets\Response::error("没有参数"));
            echo json_encode([
                //'initialPreviewConfig' => $p2,
                'error_msg' => "参数错误",
                'append' => false,
            ]);
            return;
        }

        $oss = \Yii::$app->get('oss');
        $day = date("Ymd");
        list(, $suffix) = explode('.', $name);

        if (!in_array($suffix, ['jpg', 'png', 'gif'])) {
            echo json_encode([
                //'initialPreviewConfig' => $p2,
                'error_msg' => "只能上传图片",
                'append' => false,
            ]);
            return;
        }
        $fileName = md5($name . time()) . '.' . $suffix;
        $fh = \Yii::$app->params['uploadPath'] . "/image/{$day}/{$fileName}";
        $ret = $oss->upload($fh, $tmp_name); // 会自动创建文件夹
        if (!$ret) {
            //$this->asJson(\common\widgets\Response::error("上传失败"));
            echo json_encode([
                //'initialPreviewConfig' => $p2,
                'error_msg' => "上传失败",
                'append' => false,
            ]);
            return;
        }

        $p1 = $ret["info"]["url"];
        //$p1 = \Yii::$app->params['static_domain'].$fh;  
        // 返回上传成功后的商品图信息
        echo json_encode([
            'initialPreview' => $p1,
            //'initialPreviewConfig' => $p2,
            'append' => true,
        ]);
        return;
    }

    public function actionInputDelete() {
        echo json_encode([
        ]);
        return;
    }

    public function actionAdd() {

        $model = new \app\models\CommProduction;
        //var_dump($model->load(Yii::$app->request->post()) , Yii::$app->request->post(),$model->validate());exit;
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            $model->cover = UploadedFile::getInstance($model, 'cover');
            if ($model->cover) {
                $model->cover->saveAs('upload/' . $model->cover->baseName . '.' . $model->cover->extension);
            }
            var_dump($model->cover);
            exit;
            return $this->render('entry-confirm', ['model' => $model]);
        } else {
            // 无论是初始化显示还是数据验证错误
            return $this->render('add', ['model' => $model]);
        }
    }

}
