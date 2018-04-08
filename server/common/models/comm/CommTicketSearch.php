<?php

namespace common\models\comm;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\comm\CommTicket;

/**
 * CommTicketSearch represents the model behind the search form about `common\models\comm\CommTicket`.
 */
class CommTicketSearch extends CommTicket
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'condition', 'money', 'index_show', 'status', 'count', 'num'], 'integer'],
            [['duration', 'updated_at', 'created_at'], 'safe'],
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
        $query = CommTicket::find();

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
            'condition' => $this->condition,
            'money' => $this->money,
            'index_show' => $this->index_show,
            'status' => $this->status,
            'count' => $this->count,
            'num' => $this->num,
            'updated_at' => $this->updated_at,
            'created_at' => $this->created_at,
        ]);

        $query->andFilterWhere(['like', 'duration', $this->duration]);

        return $dataProvider;
    }
}
