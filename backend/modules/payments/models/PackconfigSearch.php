<?php

namespace backend\modules\payments\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\payments\models\Packconfig;

/**
 * PackconfigSearch represents the model behind the search form of `backend\modules\payments\models\Packconfig`.
 */
class PackconfigSearch extends Packconfig
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'packId', 'rankId', 'relevel', 'cmpsntype', 'recipientType', 'units', 'recordBy', 'changedBy'], 'integer'],
            [['amount'], 'number'],
            [['itemcntrl', 'recordDate', 'changeDate'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
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
        $query = Packconfig::find();

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
            'packId' => $this->packId,
            'rankId' => $this->rankId,
            'relevel' => $this->relevel,
            'cmpsntype' => $this->cmpsntype,
            'recipientType' => $this->recipientType,
            'units' => $this->units,
            'amount' => $this->amount,
            'recordBy' => $this->recordBy,
            'recordDate' => $this->recordDate,
            'changedBy' => $this->changedBy,
            'changeDate' => $this->changeDate,
        ]);

        $query->andFilterWhere(['like', 'itemcntrl', $this->itemcntrl]);

        return $dataProvider;
    }
}
