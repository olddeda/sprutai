<?php
namespace common\modules\company\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

use common\modules\rbac\helpers\enum\Role;

use common\modules\company\models\CompanyAddress;


/**
 * CompanyAddressSearch represents the model behind the search form about `common\modules\company\models\CompanyAddress`.
 */
class CompanyAddressSearch extends CompanyAddress
{
	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[['is_primary'], 'integer'],
			[['address'], 'string'],
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
		$query = CompanyAddress::find();
		
		// Add conditions that should always apply here
		$dataProvider = new ActiveDataProvider([
			'query' => $query,
		]);
		
		// Set data provider sort
		$dataProvider->setSort([
			'attributes' => [
				'is_primary',
				'address',
			],
			'defaultOrder' => [
				'is_primary' => SORT_DESC,
			],
		]);
		
		$this->load($params);
		
		if (!$this->validate()) {
			return $dataProvider;
		}
		
		$query->andFilterWhere(['like', 'address', $this->address]);
		
		return $dataProvider;
	}
}
