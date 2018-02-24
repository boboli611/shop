<?php

namespace common\models\comm;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\comm\CommProduct;

/**
 * CommProductSearch represents the model behind the search form about `common\models\comm\CommProduct`.
 */
class CommProductSearch extends CommProduct {

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['id', 'price', 'sell', 'count'], 'integer'],
            [['title', 'desc', 'cover', 'updated_at', 'created_at'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios() {
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
    public function search($params) {
        $query = CommProduct::find();

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
            'id' => $this->id,
            'price' => $this->price,
            'sell' => $this->sell,
            'count' => $this->count,
            'updated_at' => $this->updated_at,
            'created_at' => $this->created_at,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
                ->andFilterWhere(['like', 'desc', $this->desc])
                ->andFilterWhere(['like', 'cover', $this->cover]);

        return $dataProvider;
    }

}
