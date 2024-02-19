<?php

namespace common\modules\rbac\models\search;

use Yii;
use yii\data\ArrayDataProvider;
use yii\db\Query;

class SearchChild extends Search
{
	/**
	 * @var string
	 */
	protected $parent;

	/**
	 * @inheritdoc
	 */
	public function __construct($parent, $config = []) {
		parent::__construct($config);
		$this->manager = Yii::$app->authManager;
		$this->parent = $parent;
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
			$this->manager->itemChildTable.'.parent' => $this->parent
		])->leftJoin($this->manager->itemTable, $this->manager->itemTable.'.name = '.$this->manager->itemChildTable.'.child');

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
