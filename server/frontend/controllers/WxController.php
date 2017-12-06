<?php

namespace frontend\controllers;

use Yii;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use frontend\models\ContactForm;
use common\widgets;
use \yii\db\Exception as Exception;

/**
 * Site controller
 */
class WxController extends Controller {
    
    public function actions()
{
    return [
        'error' => ['class' => 'yii\web\ErrorAction'],
    ];
}

    public function actionNotice() {

        $userId = 1;
        $orderId = 111;
        $price = 10;
        try {
            (new \frontend\service\Pay())->storage($userId, $orderId, $price);
        } catch (Exception $exc) {
            echo $exc->getMessage();
            \frontend\service\Error::addLog($userId, $exc->getMessage(), json_encode($exc->errorInfo));
            exit;
        }
    }

}
