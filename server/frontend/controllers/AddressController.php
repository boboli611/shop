<?php

namespace frontend\controllers;

use Yii;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\widgets;

/**
 * index controller
 */
class AddressController extends Controller {

    public function actionSave() {

        $id = (int) Yii::$app->request->post("id");
        $name = Yii::$app->request->post("name");
        $mobile = (int) Yii::$app->request->post("mobile");
        $province = Yii::$app->request->post("province");
        $city = Yii::$app->request->post("city");
        $county = Yii::$app->request->post("county");
        $address = Yii::$app->request->post("address");
        $status = (int) Yii::$app->request->post("status");
        
        if (!$name || !$mobile || !$address ||!$province || !$city || !$county) {
            $this->asJson(widgets\Response::error("参数错误"));
            return;
        }

        $uid = widgets\User::getUid();
        try {

            if ($id) {
                $addressModel = \common\models\user\UserAddress::findOne($id);
                if ($addressModel->user_id != $uid) {
                    $this->asJson(widgets\Response::error("只能修改自己的地址"));
                    return;
                }
            } else {
                $addressModel = new \common\models\user\UserAddress();
            }

            $addressModel->user_id = $uid;
            $addressModel->name = $name;
            $addressModel->mobile = $mobile;
            $addressModel->province = $province;
            $addressModel->city = $city;
            $addressModel->county = $county;
            $addressModel->address = $address;
            $addressModel->status = $status;

            if ($status == 1) {
                \common\models\user\UserAddress::updateAll(['status' => 0], "user_id = {$uid}");
            }

            $res = $addressModel->save();
            if (!$res) {
                throw new \Exception("保存失败");
            }
        } catch (\Exception $ex) {
            $this->asJson(widgets\Response::error($ex->getMessage()));
            return;
        }

        $out['id'] = $addressModel->id;
        $this->asJson(widgets\Response::sucess($out));
    }
    
    public function actionSaveStatus() {
        $this->asJson(widgets\Response::sucess($out));
    }

    public function actionDelete() {

        $id = (int) Yii::$app->request->get("id");
        if (!$id) {
            $this->asJson(widgets\Response::error("id错误"));
            return;
        }
        
        $address = \common\models\user\UserAddress::findOne($id);
        if (!$address){
            $this->asJson(widgets\Response::error("地址不存在"));
            return;
        }
        
        $userId = widgets\User::getUid();
        if ($address->user_id != $userId){
            $this->asJson(widgets\Response::error("删除非法地址"));
            return;
        }
        
        $address->delete();
        $out['id'] = $id;
        $this->asJson(widgets\Response::sucess($out));
    }

    public function actionList() {

        $uid = widgets\User::getUid();

        $list = \common\models\user\UserAddress::find()->where(["user_id" => $uid])->orderBy("id desc")->all();

        $this->asJson(widgets\Response::sucess($list));
    }

    public function actionDetail() {

        $id = (int) Yii::$app->request->get("id");
        if (!$id) {
            $this->asJson(widgets\Response::error("参数错误"));
            return;
        }

        $uid = widgets\User::getUid();

        $Info = \common\models\user\UserAddress::find()->where(["id" => $id])->andWhere(["user_id" => $uid])->one();
        if (!$Info) {
            $this->asJson(widgets\Response::error("参数错误!"));
            return;
        }

        $this->asJson(widgets\Response::sucess($Info));
    }

    public function actionDefault() {


        $uid = widgets\User::getUid();

        $Info = \common\models\user\UserAddress::find()->where(["status" => 1])->andWhere(["user_id" => $uid])->one();
        if (!$Info) {
            $this->asJson(widgets\Response::error("参数错误!"));
            return;
        }

        $this->asJson(widgets\Response::sucess($Info));
    }

}
