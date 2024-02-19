<?php
namespace common\modules\lookup\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

use common\modules\base\helpers\enum\Status;

use common\modules\lookup\models\Lookup;

/**
 * LookupSearch represents the model behind the search form about `common\modules\lookup\models\Lookup`.
 */
class LookupSearch extends Lookup
{

	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[['id', 'parent_id', 'type', 'sequence', 'status', 'created_at', 'updated_at'], 'integer'],
			[['title'], 'safe'],
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
		$query = Lookup::find();
		
		// Add status condition
		$query->andWhere(['NOT IN', 'status', [
			Status::DELETED,
			Status::TEMP,
		]]);
		
		// Add parent condition
		if (isset($params['parent_id'])) {
			$query->andWhere('parent_id = :parent_id', [
				':parent_id' => $params['parent_id'],
			]);
		}

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
		]);

		$this->load($params);

		if (!$this->validate()) {
			return $dataProvider;
		}

		$query->andFilterWhere([
			'id' => $this->id,
			'parent_id' => $this->parent_id,
			'type' => $this->type,
			'sequence' => $this->sequence,
			'status' => $this->status,
			'created_at' => $this->created_at,
			'updated_at' => $this->updated_at,
		]);

		$query->andFilterWhere(['like', 'title', $this->title]);

		return $dataProvider;
	}

}
