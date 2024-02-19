<?php
namespace common\modules\content\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

use common\modules\base\components\Debug;

use common\modules\rbac\helpers\enum\Role;

use common\modules\tag\models\Tag;

use common\modules\user\models\User;
use common\modules\user\models\UserProfile;

use common\modules\company\models\Company;

use common\modules\content\helpers\enum\Status;
use common\modules\content\models\Blog;

/**
 * BlogSearch represents the model behind the search form about `common\modules\content\models\Blog`.
 */
class BlogSearch extends Blog
{
	public $author_fio;
	public $company_title;
	
	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[['id', 'author_id', 'type', 'is_main', 'status', 'created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
			[['title', 'status', 'category_id', 'tags_ids', 'created_at', 'updated_at', 'author_fio', 'company_title'], 'safe'],
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
			'author' => function ($query) {
				$query->joinWith(['profile']);
			},
			'media',
			'company',
		]);
		
		$query->andWhere(['not in', self::tableName().'.status', [Status::TEMP]]);
		
		if (!Yii::$app->user->hasRole([Role::SUPERADMIN, Role::ADMIN, Role::EDITOR]) && !isset($params['skip_author_id'])) {
			$query->andWhere('author_id = :author_id', [
				':author_id' => Yii::$app->user->id,
			]);
		}
		
		$query->groupBy('id');
		
		// Add conditions that should always apply here
		$dataProvider = new ActiveDataProvider([
			'query' => $query,
		]);
		
		// Set data provider sort
		$dataProvider->setSort([
			'attributes' => [
				'id',
				'title',
				'status',
				'date_at',
				'tags_ids' => [
					'asc' => [Tag::tableName().'.title' => SORT_ASC],
					'desc' => [Tag::tableName().'.title' => SORT_DESC],
				],
				'author_fio' => [
					'asc' => ['CONCAT('.UserProfile::tableName().'.last_name, " ", '.UserProfile::tableName().'.first_name, " ", '.UserProfile::tableName().'.middle_name)' => SORT_ASC],
					'desc' => ['CONCAT('.UserProfile::tableName().'.last_name, " ", '.UserProfile::tableName().'.first_name, " ", '.UserProfile::tableName().'.middle_name)' => SORT_DESC],
					'default' => SORT_ASC
				],
				'company_title' => [
					'asc' => [Company::tableName().'.title' => SORT_ASC],
					'desc' => [Company::tableName().'.title' => SORT_DESC],
				],
			],
			'defaultOrder' => [
				'date_at' => SORT_DESC,
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
		
		$query->andFilterWhere(['like', self::tableName().'.title', $this->title]);
		$query->andFilterWhere([Tag::tableName().'.id' => $this->tags_ids]);
		$query->andFilterWhere(['like', 'CONCAT('.UserProfile::tableName().'.last_name, " ", '.UserProfile::tableName().'.first_name, " ", '.UserProfile::tableName().'.middle_name)', $this->author_fio]);
		$query->andFilterWhere(['like', Company::tableName().'.title', $this->company_title]);
		
		// Add filter time condition
		$query->andFilterWhere([
			'FROM_UNIXTIME('.self::tableName().'.date_at, "%d-%m-%Y")' => $this->date_at,
		]);
		
		return $dataProvider;
	}
}
