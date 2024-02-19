<?php
namespace common\modules\company\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

use common\modules\base\helpers\enum\Status;

use common\modules\user\models\User;
use common\modules\user\models\UserProfile;

use common\modules\company\models\CompanyUser;

/**
 * CompanyUserSearch represents the model behind the search form about `common\modules\company\models\CompanyUser`.
 */
class CompanyUserSearch extends CompanyUser
{
	public $user_fio;
	public $user_username;
	public $user_email;
	public $user_phone;
	public $user_telegram;

	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[['id', 'company_id', 'user_id', 'status', 'created_at', 'updated_at'], 'integer'],
			[['user_fio', 'user_username', 'user_email', 'user_phone', 'user_telegram'], 'safe'],
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
		$query = CompanyUser::find();
		
		// Add relation
		$query->joinWith([
			'company',
			'user' => function($query) {
				$query->joinWith([
					'profile',
					'telegram',
				]);
			}
		]);
		
		// Add status condition
		$query->andWhere(['NOT IN', self::tableName().'.status', [
			Status::DELETED,
			Status::TEMP,
		]]);

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
		]);
		
		// Set data provider sort
		$dataProvider->setSort([
			'attributes' => [
				'id',
				'status',
				'user_fio' => [
					'asc' => ['CONCAT('.UserProfile::tableName().'.last_name, " ", '.UserProfile::tableName().'.first_name, " ", '.UserProfile::tableName().'.middle_name)' => SORT_ASC],
					'desc' => ['CONCAT('.UserProfile::tableName().'.last_name, " ", '.UserProfile::tableName().'.first_name, " ", '.UserProfile::tableName().'.middle_name)' => SORT_DESC],
					'default' => SORT_ASC
				],
				'user_username' => [
					'asc' => [User::tableName().'.username' => SORT_ASC],
					'desc' => [User::tableName().'.username' => SORT_DESC],
				],
				'user_email' => [
					'asc' => [User::tableName().'.email' => SORT_ASC],
					'desc' => [User::tableName().'.email' => SORT_DESC],
				],
				'user_phone' => [
					'asc' => [UserProfile::tableName().'.phone' => SORT_ASC],
					'desc' => [UserProfile::tableName().'.phone' => SORT_DESC],
				],
				'user_telegram' => [
					'asc' => ['telegram.username' => SORT_ASC],
					'desc' => ['telegram.username' => SORT_DESC],
				],
			],
			'defaultOrder' => [
				'user_fio' => SORT_ASC,
			],
		]);

		$this->load($params);

		if (!$this->validate()) {
			return $dataProvider;
		}

		$query->andFilterWhere([
			self::tableName().'.id' => $this->id,
			self::tableName().'.company_id' => $this->company_id,
			self::tableName().'.user_id' => $this->user_id,
			self::tableName().'.status' => $this->status,
			self::tableName().'.created_at' => $this->created_at,
			self::tableName().'.updated_at' => $this->updated_at,
		]);
		
		$query->andFilterWhere(['like', User::tableName().'.username', $this->user_username]);
		$query->andFilterWhere(['like', User::tableName().'.email', $this->user_email]);
		$query->andFilterWhere(['like', 'CONCAT('.UserProfile::tableName().'.last_name, " ", '.UserProfile::tableName().'.first_name, " ", '.UserProfile::tableName().'.middle_name)', $this->user_fio]);
		$query->andFilterWhere(['like', UserProfile::tableName().'.phone', $this->user_phone]);
		$query->andFilterWhere(['like', 'telegram.username', $this->user_telegram]);

		return $dataProvider;
	}

}
