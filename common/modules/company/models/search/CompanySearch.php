<?php
namespace common\modules\company\models\search;

use common\modules\tag\models\Tag;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

use common\modules\base\helpers\enum\Status;

use common\modules\company\models\Company;
use common\modules\company\models\CompanyUser;

/**
 * CompanySearch represents the model behind the search form about `common\modules\company\models\Company`.
 */
class CompanySearch extends Company
{
	public $tag_title;
	
	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[['id', 'type', 'status', 'created_at', 'updated_at'], 'integer'],
			[['title', 'descr', 'text', 'site', 'email', 'phone', 'tag_title'], 'safe'],
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
		$query = Company::find();
		
		// Add relations
		$query->joinWith([
			'users',
			'tag',
		]);
		
		// Add status condition
		$query->andWhere(['NOT IN', self::tableName().'.status', [
			Status::DELETED,
			Status::TEMP,
		]]);
		
		if (!Yii::$app->user->isSuperAdmin && !Yii::$app->user->isAdmin && !Yii::$app->user->isEditor) {
			$query->andWhere(CompanyUser::tableName().'.user_id = :user_id', [
				':user_id' => Yii::$app->user->id,
			]);
		}

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
		]);
		
		// Set data provider sort
		$dataProvider->setSort([
			'attributes' => [
				'id',
				'status',
				'title',
				'site',
				'email',
				'phone',
				'tag_title' => [
					'asc' => [Tag::tableName().'.title' => SORT_ASC],
					'desc' => [Tag::tableName().'.title' => SORT_DESC],
				],
			],
			'defaultOrder' => [
				'title' => SORT_ASC,
			],
		]);

		$this->load($params);

		if (!$this->validate()) {
			return $dataProvider;
		}

		$query->andFilterWhere([
			self::tableName().'.id' => $this->id,
			self::tableName().'.status' => $this->status,
			self::tableName().'.created_at' => $this->created_at,
			self::tableName().'.updated_at' => $this->updated_at,
		]);
		
		if ($this->type) {
			$query->andWhere('('.self::tableName().'.type & :type) = :type', [
				'type' => $this->type,
			]);
		}

		$query->andFilterWhere(['like', self::tableName().'.title', $this->title]);
		$query->andFilterWhere(['like', self::tableName().'.site', $this->site]);
		$query->andFilterWhere(['like', self::tableName().'.email', $this->email]);
		$query->andFilterWhere(['like', self::tableName().'.phone', $this->phone]);
		$query->andFilterWhere(['like', Tag::tableName().'.title', $this->tag_title]);

		return $dataProvider;
	}

}
