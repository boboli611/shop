<?php

namespace common\models\comm;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\comm\CommOrder;

/**
 * CommOrderSearch represents the model behind the search form about `common\models\comm\CommOrder`.
 */
class CommOrderSearch extends CommOrder
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'product_id', 'num', 'status', 'refund'], 'integer'],
            [['order_id', 'address', 'expressage', 'content', 'updated_at', 'created_at'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = CommOrder::find()->select(['comm_order.*','user.username']);
        $query->where('comm_order.status != 9');
        $query->groupBy("order_id");
        $query->join('inner join', "user", "user.id = comm_order.user_id");
        //$query->join('inner join', "comm_production_storage", "comm_production_storage.id = comm_order.product_id");
        //$query->join('inner join', "comm_product", "comm_product.id = comm_production_storage.product_id");
        // add conditions that should always apply here
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,  
                ]
            ],
            
        ]);

        $this->load($params);
        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'comm_order.id' => $this->id,
            'comm_order.user_id' => $this->user_id,
            'comm_order.product_id' => $this->product_id,
            'comm_order.num' => $this->num,
            'comm_order.status' => $this->status,
            'comm_order.refund' => $this->refund,
        ]);
        
        //var_dump($query);exit;

        $query->andFilterWhere(['like', 'order_id', $this->order_id])
            ->andFilterWhere(['like', 'address', $this->address])
            ->andFilterWhere(['like', 'expressage', $this->expressage])
            ->andFilterWhere(['like', 'content', $this->content])
            ->andFilterWhere(['like', 'updated_at', $this->updated_at])
            ->andFilterWhere(['like', 'created_at', $this->created_at]);

        return $dataProvider;
    }
}
