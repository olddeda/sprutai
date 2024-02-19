<?php

namespace common\modules\telegram\models\search;

use common\modules\base\components\Debug;
use common\modules\telegram\helpers\enum\Role;
use common\modules\telegram\helpers\Helpers;
use common\modules\telegram\models\TelegramRegion;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

use common\modules\telegram\models\TelegramUser;
use common\modules\telegram\helpers\enum\Range;

/**
 * TelegramUserSearch represents the model behind the search form of `common\modules\telegram\models\TelegramUser`.
 */
class TelegramUserSearch extends TelegramUser
{
	public $fullname;
	public $role;
	public $region_title;
	
    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['id', 'region_id', 'status', 'created_at', 'updated_at'], 'integer'],
            [['username', 'first_name', 'last_name', 'phone', 'email', 'fullname', 'role'], 'safe'],
			[['region_title'], 'safe'],
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
        $query = TelegramUser::find();
        $query->joinWith([
        	'region' => function($query) {
        		$query->where([]);
			},
		], true);
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
	
		// Set data provider sort
		$dataProvider->setSort([
			'attributes' => [
				'id',
				'username',
				'first_name',
				'last_name',
				'phone',
				'email',
				'status',
				'lastvisit_at',
				'created_at',
				'fullname' => [
					'asc' => ['CONCAT('.TelegramUser::tableName().'.first_name, " ", '.TelegramUser::tableName().'.last_name)' => SORT_ASC],
					'desc' => ['CONCAT('.TelegramUser::tableName().'.first_name, " ", '.TelegramUser::tableName().'.last_name)' => SORT_DESC],
				],
				'region_id' => [
					'asc' => [TelegramRegion::tableName().'.title' => SORT_ASC],
					'desc' => [TelegramRegion::tableName().'.title' => SORT_DESC],
				],
				'role' => [
					'asc' => ['is_customer' => SORT_ASC],
					'desc' => ['is_executor' => SORT_DESC],
				],
			],
			'defaultOrder' => [
				'lastvisit_at' => SORT_DESC,
			],
		]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
			self::tableName().'.id' => $this->id,
			self::tableName().'.status' => $this->status,
			self::tableName().'.created_at' => $this->created_at,
			self::tableName().'.updated_at' => $this->updated_at,
			self::tableName().'.region_id' => $this->region_id,
        ]);
        
        $query->andFilterWhere(['like', self::tableName().'.username', $this->username]);
		$query->andFilterWhere(['like', self::tableName().'.first_name', $this->first_name]);
		$query->andFilterWhere(['like', self::tableName().'.last_name', $this->last_name]);
		$query->andFilterWhere(['like', self::tableName().'.phone', $this->phone]);
		$query->andFilterWhere(['like', self::tableName().'.email', $this->email]);
		$query->andFilterWhere(['like', 'CONCAT('.TelegramUser::tableName().'.first_name, " ", '.TelegramUser::tableName().'.last_name)', $this->fullname]);
		
		if ($this->role == Role::CUSTOMER)
			$query->andFilterWhere([self::tableName().'.is_customer' => true]);
		else if ($this->role == Role::EXECUTOR)
			$query->andFilterWhere([self::tableName().'.is_executor' => true]);

        return $dataProvider;
    }
}
