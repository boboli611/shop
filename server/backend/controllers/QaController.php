<?php

namespace backend\controllers;

use Yii;
use common\models\comm\CommQa;
use common\models\comm\CommQaSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * QaController implements the CRUD actions for CommQa model.
 */
class QaController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all CommQa models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CommQaSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single CommQa model.
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
     * Creates a new CommQa model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        exit;
        $model = new CommQa();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing CommQa model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        exit;
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing CommQa model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        exit;
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the CommQa model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return CommQa the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = CommQa::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    
    public function actionAdd() {
        $model = new \backend\models\UserBackend();
        $data = Yii::$app->request->post();
        $uid = \Yii::$app->user->getId();
        $sql = "select * from auth_assignment where user_id = {$uid} and item_name = '人员管理'";
        $role = \backend\models\UserBackend::findBySql($sql)->asArray()->one();
        if(!$role){
            return $this->redirect('/admin/site/index', [ 'model' => $model, ]);
        }
        if ($data) {
            $pwd = $data['UserBackend']['password_hash'];

            // 实例化登录模型 common\models\LoginForm
           $model->setPassword($pwd);
            $pwd = $model->password_hash;
            $model->generateAuthKey();
            $authKey = $model->getAuthKey();
            $data['UserBackend']['password_hash'] = $pwd;
            $data['UserBackend']['auth_key'] = $authKey;
            $data['UserBackend']['created_at'] = date("Y-m-d H:i:s");
            $data['UserBackend']['updated_at'] = date("Y-m-d H:i:s");
            $model->load($data);
            if ($model->save()){
                return $this->redirect('/admin/assignment/index');
            }
            
            return $this->render('create', [ 'model' => $model, ]);
        } else {
            return $this->render('create', [
                        'model' => $model,
            ]);
        }
    }
}
