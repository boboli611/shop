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

/**
 * Site controller
 */
class ProductItemController extends Controller {

    public function actionIndex() {
        $list = (new \common\models\comm\CommProductItem())->getListBySort();
        $out["list"] = $list;
        $this->asJson(widgets\Response::sucess($out));
    }

}
