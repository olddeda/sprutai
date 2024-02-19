<?php
namespace client\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

use artkost\qa\models\QuestionInterface;

/**
 * Question Model Search
 * @package artkost\qa\models
 */
class QuestionSearch extends Question
{
	
	/**
	 * @inheritdoc
	 */
	public function scenarios() {
		return Model::scenarios();
	}
	
	
	/**
	 * @param $params
	 * @return ActiveDataProvider
	 */
	public function search($params) {
		$query = self::find()->with('user');
		
		$query->andWhere(['status' => QuestionInterface::STATUS_PUBLISHED]);
		
		if (isset($params['tags']) && $params['tags']) {
			$query->andWhere(['like', 'tags', $params['tags']]);
		}
		
		$dataProvider = new ActiveDataProvider([
			'query' => $query,
		]);
		
		if (!($this->load($params) && $this->validate())) {
			return $dataProvider;
		}
		
		return $dataProvider;
	}
	
	/**
	 * @param $params
	 * @param int $userID
	 * @return ActiveDataProvider
	 */
	public function searchFavorite($params, $userID) {
		$dataProvider = $this->search($params);
		$dataProvider->query
			->joinWith('favorites', true, 'RIGHT JOIN')
			->andWhere(['{{%qa_favorite}}.user_id' => $userID]);
		
		return $dataProvider;
	}
	
	/**
	 * @param $params
	 * @param $userID
	 * @return ActiveDataProvider
	 */
	public function searchMy($params, $userID) {
		$dataProvider = $this->search($params);
		$dataProvider->query
			->andWhere(['status' => QuestionInterface::STATUS_DRAFT])
			->where(['user_id' => $userID]);
		
		return $dataProvider;
	}
	
	protected function addCondition($query, $attribute, $partialMatch = false) {
		if (($pos = strrpos($attribute, '.')) !== false) {
			$modelAttribute = substr($attribute, $pos + 1);
		} else {
			$modelAttribute = $attribute;
		}
		
		$value = $this->$modelAttribute;
		if (trim($value) === '') {
			return;
		}
		if ($partialMatch) {
			$query->andWhere(['like', $attribute, $value]);
		} else {
			$query->andWhere([$attribute => $value]);
		}
	}
}
