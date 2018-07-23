<?php

namespace backend\controllers;

use Yii;
use common\models\data\DataAnalyze;
use common\models\data\DataAnalyzeSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * DataAnalyzeController implements the CRUD actions for DataAnalyze model.
 */
class DataAnalyzeController extends Controller {

    /**
     * @inheritdoc
     */
    public function behaviors() {
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
     * Lists all DataAnalyze models.
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new DataAnalyzeSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $data = DataAnalyze::find()->all();
        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'data' => $data,
        ]);
    }

    /**
     * Lists all DataAnalyze models.
     * @return mixed
     */
    public function actionProduct() {
        $page = (int) \Yii::$app->request->get("page");
        $page = $page > 1 ? $page - 1 : 0;
        $limit = 10;
        $page = $page * $limit;

        $sql = "select a.id as pid, a.*, b.* from comm_product a "
                . " left join data_analyze_product b on a.id = b.product_id"
                . " order by a.id desc limit {$page}, {$limit}"
        ;
        $data = DataAnalyze::findBySql($sql)->asArray()->all();
        $count = \common\models\comm\CommProduct::find()->count();

        $page = new \yii\data\Pagination(['totalCount' => $count, 'pageSize' => $limit]);
        return $this->render('product', [
                    'data' => $data,
                    'pages' => $page,
        ]);
    }

    /**
     * Displays a single DataAnalyze model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id) {
        return $this->render('view', [
                    'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new DataAnalyze model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new DataAnalyze();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing DataAnalyze model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id) {
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
     * Deletes an existing DataAnalyze model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id) {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the DataAnalyze model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return DataAnalyze the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = DataAnalyze::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
