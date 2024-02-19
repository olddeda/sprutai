<?php

namespace common\modules\settings\models\search;

use Yii;
use yii\data\ActiveDataProvider;

use common\modules\settings\models\Settings;

/**
 * Class SettingsSearch
 * @package common\modules\settings\models\search
 */
class SettingsSearch extends Settings
{
    /**
     * Returns the validation rules for attributes.
     * @return array validation rules
     */
    public function rules() {
        return [
            [['id', 'type', 'section', 'key', 'value', 'status'], 'safe'],
        ];
    }

    /**
     * Setup search function for filtering and sorting
     * based on fullName field
     * @param $params
     * @return ActiveDataProvider
     */
    public function search($params) {
        $query = Settings::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 10
            ]
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere(['id' => $this->id]);
        $query->andFilterWhere(['status' => $this->status]);
        $query->andFilterWhere(['section' => $this->section]);
        $query->andFilterWhere(['type' => $this->type]);
        $query->andFilterWhere(['like', 'key', $this->key]);
        $query->andFilterWhere(['like', 'value', $this->value]);

        return $dataProvider;
    }
}
