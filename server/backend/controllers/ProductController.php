<?php

namespace backend\controllers;

use Yii;
use common\models\comm\CommProduct;
use common\models\comm\CommProductSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ProductController implements the CRUD actions for CommProduct model.
 */
class ProductController extends Controller {

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
     * Lists all CommProduct models.
     * @return mixed
     */
    public function actionIndex() {


        $searchModel = new CommProductSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single CommProduct model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id) {
        return $this->render('view', [
                    'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new CommProduct model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {

        $model = new CommProduct();
        $modelStorage = new \common\models\comm\CommProductionStorage();
        $recommend = new \common\models\comm\CommProductRecommend();

        $status = false;
        if (!Yii::$app->request->post()) {

            return $this->render('create', [
                        'model' => $model,
                        'modelStorage' => $modelStorage,
                            //'modelRecommend' => $recommend,
            ]);
            return;
        }

        try {

            $data = Yii::$app->request->post();
   
            $transaction = CommProduct::getDb()->beginTransaction();
            $model->load(Yii::$app->request->post());

            $result = $model->save();
            if (!$result) {
                throw new \yii\db\Exception("save error:".$model->getErrors());
            }

            $storageData = [];
            foreach ($data['storage_style'] as $k => $val) {

                if (!$data['storage_style'][$k]) {
                    //continue;
                }

                $modelStorage = new \common\models\comm\CommProductionStorage();
                $storageData["CommProductionStorage"]['style'] = $data['storage_style'][$k];
                $storageData["CommProductionStorage"]['size'] = $data['storage_size'][$k];
                $storageData["CommProductionStorage"]['num'] = $data['storage_num'][$k];
                $storageData["CommProductionStorage"]['price'] = $data['storage_price'][$k];
                $storageData["CommProductionStorage"]['product_id'] = $model->getPrimaryKey();
                $storageData["CommProductionStorage"]['status'] = $model->status;
   
                $modelStorage->load($storageData);
                $result = $modelStorage->save();
                if (!$result) {
                    throw new \yii\db\Exception("error:" . $modelStorage->getErrors());
                }
                //var_dump($storageData, $modelStorage->getPrimaryKey());exit;
            }
            
            $transaction->commit();
            return $this->redirect(['view', 'id' => $model->id]);
        } catch (\Exception $ex) {
            //var_dump($ex->getMessage());exit;
            return $this->render('create', [
                        'model' => $model,
                            // 'modelRecommend' => $recommend,
            ]);
        }
    }

    /**
     * Updates an existing CommProduct model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);
        $modelStorage = new \common\models\comm\CommProductionStorage();
        $recommend = \common\models\comm\CommProductRecommend::find()->where(['product_id' => $id])->one();
        $recommend = $recommend ? $recommend : new \common\models\comm\CommProductRecommend();
        $modelStorageList = $modelStorage->getAllBPid($id);

        if (!Yii::$app->request->post()) {
            return $this->render('update', [
                        'model' => $model,
                        'modelStorage' => $modelStorageList,
                        'modelRecommend' => $recommend,
            ]);
            return;
        }

        try {

            $data = Yii::$app->request->post();
            $transaction = CommProduct::getDb()->beginTransaction();
            $data["CommProduction"]['price'] = $data['storage_price'][0];

            $model->load($data);
            $result = $model->save();
            if (!$result) {
                throw new \yii\db\Exception("错误11");
            }

            $storageData = [];
           
            foreach ($data['storage_style'] as $k => $val) {

                if (!$data['storage_style'][$k]) {
                    continue;
                }

                $storageData["CommProductionStorage"]['style'] = $data['storage_style'][$k];
                $storageData["CommProductionStorage"]['size'] = $data['storage_size'][$k];
                $storageData["CommProductionStorage"]['num'] = $data['storage_num'][$k];
                $storageData["CommProductionStorage"]['price'] = $data['storage_price'][$k];
                $storageData["CommProductionStorage"]['product_id'] = $model->getPrimaryKey();
                $storageData["CommProductionStorage"]["id"] = $data['storage_id'][$k];
                $storageData["CommProductionStorage"]['status'] = $model->status;

                $modelStorage = \common\models\comm\CommProductionStorage::findOne($data['storage_id'][$k]);
                if (!$modelStorage){
                    $modelStorage = new \common\models\comm\CommProductionStorage();
                }
                
                $modelStorage->load($storageData);
                $result = $modelStorage->save();

                if (!$result) {
                    var_dump($modelStorage->getErrors());exit;
                    throw new \yii\db\Exception("error:" . $modelStorage->getErrors());
                }
            }
            $transaction->commit();
            return $this->redirect(['update', 'id' => $model->id]);
        } catch (\Exception $ex) {
            var_dump($ex->getMessage());exit;
            return $this->render('update', [
                        'model' => $model,
                        'modelStorage' => $modelStorageList,
                        'modelRecommend' => $recommend,
            ]);
        }
    }

    /**
     * Deletes an existing CommProduct model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id) {
        return;
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the CommProduct model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return CommProduct the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = CommProduct::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
