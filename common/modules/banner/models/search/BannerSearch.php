<?php
namespace common\modules\banner\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

use common\modules\base\helpers\enum\Status;

use common\modules\banner\models\Banner;

/**
 * BannerSearch represents the model behind the search form about `common\modules\banner\models\Banner`.
 */
class BannerSearch extends Banner
{
	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[['id', 'views', 'visits', 'status', 'created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
			[['title', 'url', 'date_from_at', 'date_to_at'], 'safe'],
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
				'title',
				'url',
				'views',
				'visits',
				'date_from_at',
				'date_to_at',
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
			self::tableName().'.views' => $this->views,
			self::tableName().'.visits' => $this->visits,
		]);
		
		$query->andFilterWhere(['like', self::tableName().'.title', $this->title]);
		$query->andFilterWhere(['like', self::tableName().'.url', $this->url]);
		
		// Add filter time condition
		$query->andFilterWhere([
			'FROM_UNIXTIME('.self::tableName().'.date_from_at, "%d-%m-%Y")' => $this->date_from_at,
			'FROM_UNIXTIME('.self::tableName().'.date_to_at, "%d-%m-%Y")' => $this->date_to_at,
		]);
		
		return $dataProvider;
	}
}
