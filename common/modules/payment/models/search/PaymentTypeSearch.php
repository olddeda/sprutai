<?php
namespace common\modules\payment\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

use common\modules\payment\models\PaymentType;

/**
 * PaymentTypeSearch represents the model behind the search form of `common\modules\payment\models\PaymentType`.
 */
class PaymentTypeSearch extends PaymentType
{
	/**
	 * {@inheritdoc}
	 */
	public function rules() {
		return [
			[['id', 'kind', 'status', 'created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
			[['price', 'price_tax'], 'number'],
			[['price_fixed', 'physical'], 'boolean'],
			[['title', 'descr'], 'safe'],
		];
	}
	
	/**
	 * {@inheritdoc}
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
		$query = PaymentType::find();
		
		$dataProvider = new ActiveDataProvider([
			'query' => $query,
		]);
		
		$this->load($params);
		
		if (!$this->validate()) {
			return $dataProvider;
		}
		
		// grid filtering conditions
		$query->andFilterWhere([
			'id' => $this->id,
			'kind' => $this->kind,
			'status' => $this->status,
			'price_fixed' => $this->price_fixed,
			'physical' => $this->physical,
		]);
		
		$query->andFilterWhere(['like', 'title', $this->title]);
		$query->andFilterWhere(['like', 'descr', $this->descr]);
		$query->andFilterWhere(['like', 'price', $this->price]);
		$query->andFilterWhere(['like', 'price_tax', $this->price_tax]);
		
		return $dataProvider;
	}
}
