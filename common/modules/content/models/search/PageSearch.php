<?php

namespace common\modules\content\models\search;

use common\modules\base\components\Debug;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

use common\modules\base\helpers\enum\Status;

use common\modules\content\models\Page;

/**
 * PageSearch represents the model behind the search form about `common\modules\content\models\Page`.
 */
class PageSearch extends Page
{

	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[['content_id', 'id', 'type', 'status', 'created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
			[['title', 'status', 'created_at', 'updated_at'], 'safe'],
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
		$query = Page::find();
		
		$query->andWhere(['not in', self::tableName().'.status', [Status::TEMP]]);
		
		$query->joinWith([
			'parent' => function ($q) {
				$q->alias('p');
				$q->where([]);
			}
		]);

		// add conditions that should always apply here
		$dataProvider = new ActiveDataProvider([
			'query' => $query,
		]);

		// Set data provider sort
		$dataProvider->setSort([
			'attributes' => [
				'id',
				'content_id',
				'status',
				'created_at',
				'updated_at',
				'title',
			]
		]);

		$this->load($params);

		if (!$this->validate()) {
			return $dataProvider;
		}

		// grid filtering conditions
		$query->andFilterWhere([
			self::tableName().'.id' => $this->id,
			self::tableName().'.content_id' => $this->content_id,
			self::tableName().'.status' => $this->status,
		]);
		
		$query->andFilterWhere(['like', self::tableName().'.title', $this->title]);

		// Add filter time condition
		$query->andFilterWhere([
			'FROM_UNIXTIME('.self::tableName().'.created_at, "%d-%m-%Y")' => $this->created_at,
			'FROM_UNIXTIME('.self::tableName().'.updated_at, "%d-%m-%Y")' => $this->updated_at,
		]);

		return $dataProvider;
	}
}
