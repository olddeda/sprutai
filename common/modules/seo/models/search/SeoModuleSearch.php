<?php
namespace common\modules\seo\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

use common\modules\base\helpers\enum\Status;

use common\modules\seo\models\SeoModule;

/**
 * SeoModuleSearch represents the model behind the search form about `common\modules\seo\models\SeoModule`.
 */
class SeoModuleSearch extends SeoModule
{

	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[['id', 'module_type', 'status', 'created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
			[['module_class', 'slugify'], 'safe'],
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
		$query = SeoModule::find();
		$query->andWhere(['not in', self::tableName().'.status', [Status::TEMP]]);

		// add conditions that should always apply here
		$dataProvider = new ActiveDataProvider([
			'query' => $query,
		]);

		// Set data provider sort
		$dataProvider->setSort([
			'attributes' => [
				'id',
				'module_type',
				'module_class',
				'slugify',
				'status',
				'created_at',
				'updated_at',
			]
		]);

		$this->load($params);

		if (!$this->validate()) {
			return $dataProvider;
		}

		// grid filtering conditions
		$query->andFilterWhere([
			'id' => $this->id,
			'module_type' => $this->module_type,
			'status' => $this->status,
		]);
		
		$query->andFilterWhere(['like', self::tableName().'.module_class', $this->module_class]);
		$query->andFilterWhere(['like', self::tableName().'.slugify', $this->slugify]);

		return $dataProvider;
	}
}
