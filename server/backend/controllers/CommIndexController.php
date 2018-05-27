<?php

namespace backend\controllers;

use Yii;
use common\models\comm\CommIndex;
use common\models\comm\CommIndexSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * CommIndexController implements the CRUD actions for CommIndex model.
 */
class CommIndexController extends Controller {

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
     * Lists all CommIndex models.
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new \common\models\comm\CommProductRecommendSearch();
        $params = [];
        if (Yii::$app->request->get('type')){
            $params['CommProductRecommendSearch']['type'] = Yii::$app->request->get('type');
        }
        $dataProvider = $searchModel->search($params); 
        
     
        $productSearchModel = new \common\models\comm\CommProductSearch();
        $productDataProvider = $productSearchModel->search(Yii::$app->request->queryParams);


        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'productSearchModel' => $productSearchModel,
                    'productDataProvider' => $productDataProvider,
        ]);
    }

    /**
     * Displays a single CommIndex model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id) {
        return $this->render('view', [
                    'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new CommIndex model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new \common\models\comm\CommProductRecommend();

        $type = Yii::$app->request->get("type");
        $id = Yii::$app->request->get("id");
        if (!$type || !$id){
            return $this->redirect(['index']);
        }
        
        $model->product_id = $id;
        $model->type = $type;
        $model->save();
        return $this->redirect(['index']);
    }

    /**
     * Updates an existing CommIndex model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing CommIndex model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id) {

        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the CommIndex model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return CommIndex the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = \common\models\comm\CommProductRecommend::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
