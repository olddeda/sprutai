<?php

namespace common\modules\user\models\search;

use common\modules\base\components\Debug;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

use common\modules\user\models\UserLog;


class UserLogSearch extends UserLog
{
	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[['id'], 'integer'],
			[['ip', 'user_agent', 'visit'], 'string'],
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
		$query = UserLog::find();
		$query->andWhere(['user_id' => $this->user_id]);

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'sort' => false,
		]);

		$this->load($params);
		if (!$this->validate())
			return $dataProvider;

		$query->andFilterWhere([
			'ip' => $this->ip,
			'user_agent' => $this->user_agent,
		]);

		if ($this->visit !== null && strlen($this->visit)) {
			$date = strtotime($this->visit);
			$query->andFilterWhere(['between', 'visit', $date, $date + 3600 * 24]);
		}

		return $dataProvider;
	}
}