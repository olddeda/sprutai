<?php
namespace common\modules\paste\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

use common\modules\base\helpers\enum\Status;

use common\modules\paste\models\Paste;

/**
 * PasteSearch represents the model behind the search form about `common\modules\paste\models\Paste`.
 */
class PasteSearch extends Paste
{
	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[['id', 'is_private', 'status', 'created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
			[['descr'], 'safe'],
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
		
		$query->andWhere(['not in', self::tableName().'.status', [Status::TEMP]]);
		
		// Add conditions that should always apply here
		$dataProvider = new ActiveDataProvider([
			'query' => $query,
		]);
		
		// Set data provider sort
		$dataProvider->setSort([
			'attributes' => [
				'id',
				'descr',
				'status',
			],
			'defaultOrder' => [
				'id' => SORT_DESC,
			],
		]);
		
		$this->load($params);
		
		if (!$this->validate()) {
			return $dataProvider;
		}
		
		// Grid filtering conditions
		$query->andFilterWhere([
			self::tableName().'.id' => $this->id,
			self::tableName().'.status' => $this->status,
		]);
		
		$query->andFilterWhere(['like', self::tableName().'.descr', $this->descr]);
		
		return $dataProvider;
	}
}
