<?php

namespace common\modules\eav\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * EavEntitySearch represents the model behind the search form about `common\modules\eav\models\EavEntity`.
 */
class EavEntitySearch extends EavEntity
{
    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['id', 'categoryId'], 'integer'],
            [['entityName', 'entityModel'], 'safe'],
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
        $query = EavEntity::find();

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
            'categoryId' => $this->categoryId,
        ]);

        $query->andFilterWhere(['=', 'entityName', $this->entityName])
            ->andFilterWhere(['=', 'entityModel', $this->entityModel]);

        return $dataProvider;
    }
}
