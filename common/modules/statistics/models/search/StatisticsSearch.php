<?php
namespace common\modules\store\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

use common\modules\statistics\models\Statistics;

/**
 * StatisticsSearch represents the model behind the search form about `common\modules\statistics\models\Statistics`.
 */
class StatisticsSearch extends Statistics
{
	
	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[['module_type', 'module_id', 'show', 'visit', 'outgoing', 'status', 'created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
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
		$query = self::find();
		
		
		// And base conditions
		if ($this->module_type)
			$query->andWhere([self::tableName().'.module_type' => $this->module_type]);
		if ($this->module_id)
			$query->andWhere([self::tableName().'.module_id' => $this->module_id]);
		$query->andWhere(['not in', self::tableName().'.status', [Status::TEMP, Status::DELETED]]);
		
		// Init data provider
		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'pagination' => [
				'defaultPageSize' => 50,
			],
		]);
		
		// Set data provider sort
		$dataProvider->setSort([
			'attributes' => [
				'id',
			],
			'defaultOrder' => [
				'id' => SORT_ASC,
			],
		]);
		
		
		$this->load($params);
		
		if (!$this->validate()) {
			return $dataProvider;
		}
		
		$query->andFilterWhere([
			self::tableName().'.id' => $this->id,
		]);
		
		return $dataProvider;
	}
}
