<?php

namespace common\modules\media\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

use common\modules\media\models\MediaFormat;

/**
 * MediaFormatSearch represents the model behind the search form about `common\modules\media\models\MediaFormat`.
 */
class MediaFormatSearch extends MediaFormat
{
    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['id', 'width', 'height', 'mode', 'watermark', 'status', 'created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
            [['format'], 'safe'],
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
        $query = MediaFormat::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'width' => $this->width,
            'height' => $this->height,
            'mode' => $this->mode,
			'watermark' => $this->watermark,
            'status' => $this->status,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'format', $this->format]);

        return $dataProvider;
    }
}
