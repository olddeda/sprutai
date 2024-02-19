<?php
namespace common\modules\company\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

use common\modules\content\helpers\enum\Status;

use common\modules\tag\models\Tag;

use common\modules\company\models\Company;
use common\modules\company\models\CompanyDiscount;

/**
 * CompanyDiscountSearch represents the model behind the search form about `common\modules\company\models\CompanyDiscount`.
 */
class CompanyDiscountSearch extends CompanyDiscount
{
	/**
	 * @var string
	 */
	public $company_title;
	
	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[['id', 'company_id', 'status'], 'integer'],
			[['infinitely'], 'boolean'],
			[['discount', 'discount_to', 'promocode', 'tags_ids', 'date_start_at', 'date_end_at', 'company_title'], 'safe'],
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
		
		$query->joinWith([
			'tags' => function ($query) {
				$query->where([]);
			},
			'company',
		]);
		
		$query->andWhere(['not in', self::tableName().'.status', [Status::TEMP]]);
		
		$query->groupBy('id');
		
		// Add conditions that should always apply here
		$dataProvider = new ActiveDataProvider([
			'query' => $query,
		]);
		
		// Set data provider sort
		$dataProvider->setSort([
			'attributes' => [
				'id',
				'discount',
                'discount_to',
				'promocode',
				'infinitely',
				'status',
				'date_start_at',
				'date_end_at',
				'tags_ids' => [
					'asc' => [Tag::tableName().'.title' => SORT_ASC],
					'desc' => [Tag::tableName().'.title' => SORT_DESC],
				],
				'company_title' => [
					'asc' => [Company::tableName().'.title' => SORT_ASC],
					'desc' => [Company::tableName().'.title' => SORT_DESC],
				],
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
			self::tableName().'.infinitely' => $this->infinitely,
			Tag::tableName().'.id' => $this->tags_ids,
		]);
		
		$query->andFilterWhere(['like', 'discount', $this->discount]);
        $query->andFilterWhere(['like', 'discount_to', $this->discount_to]);
		$query->andFilterWhere(['like', 'promocode', $this->promocode]);
		$query->andFilterWhere(['like', Company::tableName().'.title', $this->company_title]);
		
		$query->andFilterWhere([
			'FROM_UNIXTIME('.self::tableName().'.date_start_at, "%d-%m-%Y")' => $this->date_start_at,
		]);
		
		$query->andFilterWhere([
			'FROM_UNIXTIME('.self::tableName().'.date_end_at, "%d-%m-%Y")' => $this->date_end_at,
		]);
		
		return $dataProvider;
	}
}
