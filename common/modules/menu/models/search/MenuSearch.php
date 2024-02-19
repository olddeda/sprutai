<?php
namespace common\modules\menu\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

use common\modules\base\helpers\enum\Status;

use common\modules\menu\models\Menu;

/**
 * MenuSearch represents the model behind the search form about `common\modules\menu\models\Menu`.
 */
class MenuSearch extends Menu
{
	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[['id', 'tag_id', 'visible', 'status', 'created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
			[['title', 'created_at', 'updated_at'], 'safe'],
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
		$query = Menu::find();
		$query->where(['not in', self::tableName().'.status', [Status::TEMP, Status::DELETED]]);
		
		// add conditions that should always apply here
		$dataProvider = new ActiveDataProvider([
			'query' => $query,
		]);
		
		// Set data provider sort
		$dataProvider->setSort([
			'attributes' => [
				'id',
				'tag_id',
				'title',
				'visible',
				'status',
			],
			'defaultOrder' => [
				'title' => SORT_ASC,
			],
		]);
		
		$this->load($params);
		
		if (!$this->validate()) {
			return $dataProvider;
		}
		
		// grid filtering conditions
		$query->andFilterWhere([
			'id' => $this->id,
			'tag_id' => $this->tag_id,
			'status' => $this->status,
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
