<?php

namespace common\modules\rbac\models\search;

use Yii;
use yii\data\ArrayDataProvider;
use yii\db\Query;

class SearchParent extends Search
{
	/**
	 * @var string
	 */
	protected $child;

	/**
	 * @inheritdoc
	 */
	public function __construct($child, $config = []) {
		parent::__construct($config);
		$this->manager = Yii::$app->authManager;
		$this->child = $child;
	}

	/**
	 * @param array $params
	 * @return ActiveDataProvider
	 */
	public function search($params = []) {

		// Create query
		$query = (new Query)->select([
			$this->manager->itemTable.'.name',
			$this->manager->itemTable.'.description',
			$this->manager->itemTable.'.type',
		])->from($this->manager->itemChildTable)->andWhere([
			$this->manager->itemChildTable.'.child' => $this->child,
		])->leftJoin($this->manager->itemTable, $this->manager->itemTable.'.name = '.$this->manager->itemChildTable.'.parent');

		// Load and validate
		if ($this->load($params) && $this->validate()) {
			$query->andFilterWhere(['like', $this->manager->itemTable.'.name', $this->name]);
			$query->andFilterWhere(['like', $this->manager->itemTable.'.description', $this->description]);
			$query->andFilterWhere(['like', $this->manager->itemTable.'.type', $this->type]);
		}

		// Create data provider
		$dataProvider = new ArrayDataProvider([
			'allModels' => $query->all($this->manager->db),
			'sort' => [
				'attributes' => [
					'name',
					'description',
					'type'
				],
				'defaultOrder' => [
					'name' => SORT_ASC,
				],
			],
			'pagination' => [
				'pageSize' => 50,
			],
		]);

		return $dataProvider;
	}
}
