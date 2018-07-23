<?php

namespace common\models\data;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\data\DataAnalyze;

/**
 * DataAnalyzeSearch represents the model behind the search form about `common\models\data\DataAnalyze`.
 */
class DataAnalyzeSearch extends DataAnalyze
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'visit_user', 'visit_num', 'cart_num', 'pay_num'], 'integer'],
            [['date'], 'safe'],
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
        $query = DataAnalyze::find();

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
            'visit_user' => $this->visit_user,
            'visit_num' => $this->visit_num,
            'cart_num' => $this->cart_num,
            'pay_num' => $this->pay_num,
        ]);

        $query->andFilterWhere(['like', 'date', $this->date]);

        return $dataProvider;
    }
}
