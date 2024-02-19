<?php

namespace common\modules\media\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

use common\modules\base\components\Debug;
use common\modules\base\helpers\enum\Status;

use common\modules\company\models\Company;

use common\modules\media\models\MediaImage;
use common\modules\media\helpers\enum\Type;

/**
 * MediaImageSearch represents the model behind the search form about `common\modules\media\models\MediaImage`.
 */
class MediaImageSearch extends MediaImage
{
	public $width_and_height;

	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[['id', 'module_type', 'module_id', 'width', 'height', 'size', 'status', 'created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
			[['title', 'width_and_height'], 'safe'],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function scenarios() {
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
		$query = MediaImage::find();
		$query->where = [];
		$query->params = [];

		// Add type condition
		$query->andWhere(MediaImage::tableName().'.type = :type', [
			':type' => Type::IMAGE,
		]);

		// Add status condition
		$query->andWhere(['in', MediaImage::tableName().'.status', [
			Status::ENABLED,
			Status::DELETED,
		]]);

		// Create data provider
		$dataProvider = new ActiveDataProvider([
			'query' => $query,
		]);

		// Set data provider sort
		$dataProvider->setSort([
			'attributes' => [
				'id',
				'module_type',
				'module_id',
				'title',
				'size',
				'status',
				'width_and_height' => [
					'asc' => ['CONCAT('.MediaImage::tableName().'.width, "x", '.MediaImage::tableName().'.height)' => SORT_ASC],
					'desc' => ['CONCAT('.MediaImage::tableName().'.width, "x", '.MediaImage::tableName().'.height)' => SORT_DESC],
				],
			]
		]);

		$this->load($params);

		if (!$this->validate()) {
			return $dataProvider;
		}

		$query->andFilterWhere([
			'id' => $this->id,
			'module_type' => $this->module_type,
			'module_id' => $this->module_id,
			'width' => $this->width,
			'height' => $this->height,
			'size' => $this->size,
			'status' => $this->status,
			'created_by' => $this->created_by,
			'updated_by' => $this->updated_by,
			'created_at' => $this->created_at,
			'updated_at' => $this->updated_at,
		]);

		$query->andFilterWhere(['like', 'title', $this->title]);
		$query->andFilterWhere(['like', 'CONCAT('.MediaImage::tableName().'.width, "x", '.MediaImage::tableName().'.height)', $this->width_and_height]);

		return $dataProvider;
	}
}
