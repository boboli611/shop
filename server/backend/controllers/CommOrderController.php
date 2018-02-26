<?php

namespace backend\controllers;

use Yii;
use common\models\comm\CommOrder;
use common\models\comm\CommOrderSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * CommOrderController implements the CRUD actions for CommOrder model.
 */
class CommOrderController extends Controller {

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
     * Lists all CommOrder models.
     * @return mixed
     */
    public function actionIndex() {

        $searchModel = new CommOrderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single CommOrder model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id) {
        return $this->render('view', [
                    'model' => $this->findModel($id),
        ]);
    }

    /**
     * Updates an existing CommOrder model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id) {

        $model = $this->findModel($id);
        if (Yii::$app->request->post()) {
            $patams = Yii::$app->request->post();
            $data['expressage'] = $patams['CommOrder']['expressage'];
            $data['status'] = CommOrder::status_goods_waiting_receve;

            if (CommOrder::updateAll($data, ['order_id' => $id])) {
                return $this->redirect(['view', 'id' => $id]);
            } else {
                return $this->render('update', [
                            'model' => $model,
                ]);
            }
        } else {
            return $this->render('update', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Finds the CommOrder model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return CommOrder the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        $model = CommOrder::find()->select(['comm_order.*', 'user.username', 'sum(comm_order.pay_price) as sumPayPrice']);
        $model->groupBy("order_id");
        $model->join('inner join', "user", "user.id = comm_order.user_id");
        $model->where(["comm_order.order_id" => $id]);
        $model = $model->one();
        if ($model !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
