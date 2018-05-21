<?php

namespace common\models\comm;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\comm\CommOrderProduct;

/**
 * CommOrderProductSeatch represents the model behind the search form about `common\models\comm\CommOrderProduct`.
 */
class CommOrderProductSeatch extends CommOrderProduct
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'product_id', 'user_id', 'price', 'pay_price', 'num'], 'integer'],
            [['order_id', 'updated_at', 'created_at'], 'safe'],
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
        $query = CommOrderProduct::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'product_id' => $this->product_id,
            'user_id' => $this->user_id,
            'price' => $this->price,
            'pay_price' => $this->pay_price,
            'num' => $this->num,
            'updated_at' => $this->updated_at,
            'created_at' => $this->created_at,
        ]);

        $query->andFilterWhere(['like', 'order_id', $this->order_id]);

        return $dataProvider;
    }
}