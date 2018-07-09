<?php

namespace common\models\comm;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\comm\CommOrderRefundLog;

/**
 * CommOrderRefundLogSearch represents the model behind the search form about `common\models\comm\CommOrderRefundLog`.
 */
class CommOrderRefundLogSearch extends CommOrderRefundLog
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'storage_id', 'refound', 'expressage_status', 'price', 'admin_id', 'admin_nickname'], 'integer'],
            [['order_id', 'expre_company', 'expressage_num', 'mobile', 'content', 'updated_at', 'created_at'], 'safe'],
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
        $query = CommOrderRefundLog::find();
        $query->orderBy("id desc");
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
            'storage_id' => $this->storage_id,
            'refound' => $this->refound,
            'expressage_status' => $this->expressage_status,
            'price' => $this->price,
            'admin_id' => $this->admin_id,
            'admin_nickname' => $this->admin_nickname,
            'updated_at' => $this->updated_at,
            'created_at' => $this->created_at,
        ]);

        $query->andFilterWhere(['like', 'order_id', $this->order_id])
            ->andFilterWhere(['like', 'expre_company', $this->expre_company])
            ->andFilterWhere(['like', 'expressage_num', $this->expressage_num])
            ->andFilterWhere(['like', 'mobile', $this->mobile])
            ->andFilterWhere(['like', 'content', $this->content]);

        return $dataProvider;
    }
}
