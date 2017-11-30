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
    
    public function behaviors()
    {
        return [
            [
                'class' => 'yii\filters\ContentNegotiator',
                'formats' => [
                    'application/json' => Response::FORMAT_JSON
                ],
            ]
        ];
    }

    public function actionUploadImg() {


        $img = UploadedFile::getInstanceByName('file');
        var_dump($img);
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
