<?php

namespace frontend\controllers;

use Yii;
use common\models\user\UserAddress;
use common\models\user\UserAddressSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

class UserAddressController extends Controller
{
    
   public function init(){
    $this->enableCsrfValidation = false;
}
    

    /**
     * Lists all UserAddress models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UserAddressSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single UserAddress model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new UserAddress model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new UserAddress();
        $data = Yii::$app->request->post();
        
        if (!$data["address"]){
            return $this->asJson(\common\widgets\Response::error("参数错误"));
        }
        
        $user_id = \common\widgets\User::getUid();
        $model->user_id = $user_id;
        $model->status = 0;
        $model->address = $data["address"];
       
        
        if (!$model->save()){
            return $this->asJson(\common\widgets\Response::error("保存失败"));
        }

        $out["id"] = $model->getPrimaryKey();
        return $this->asJson(\common\widgets\Response::sucess($out));
    }

    /**
     * Updates an existing UserAddress model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate()
    {
        $id = Yii::$app->request->post("id");
        $address = Yii::$app->request->post("address");
        if (!$id || !$address){
            return $this->asJson(\common\widgets\Response::error("参数错误"));
        }
        
        $model = UserAddress::findOne($id);
        if (!$model){
            return $this->asJson(\common\widgets\Response::error("参数错误"));
        }

        if($model->user_id != \common\widgets\User::getUid()){
            return $this->asJson(\common\widgets\Response::error("参数错误"));
        }
        
        $model->address = $address;
        if (!$model->save()) {
            return $this->asJson(\common\widgets\Response::error("操作失败"));
        }
        
        $out["id"] = $model->getPrimaryKey();
        return $this->asJson(\common\widgets\Response::sucess($out));
    }

    /**
     * Deletes an existing UserAddress model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete()
    {
        $id = Yii::$app->request->post("id");
        if (!$id){
            return $this->asJson(\common\widgets\Response::error("参数错误"));
        }
        
        $model = UserAddress::findOne($id);
        if (!$model){
            return $this->asJson(\common\widgets\Response::error("参数错误"));
        }

        if($model->user_id != \common\widgets\User::getUid()){
            return $this->asJson(\common\widgets\Response::error("参数错误"));
        }
        
        if (!$model->delete()) {
            return $this->asJson(\common\widgets\Response::error("操作失败"));
        }
        
        $out["id"] = $id;
        return $this->asJson(\common\widgets\Response::sucess($out));
    }

   
}
