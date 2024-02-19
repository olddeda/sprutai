<?php

namespace common\modules\user\models\search;

use common\modules\media\helpers\enum\Mode;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

use common\modules\company\models\Company;

use common\modules\user\Finder;
use common\modules\user\models\User;
use common\modules\user\models\UserProfile;

/**
 * UserSearch represents the model behind the search form about User.
 */
class UserSearch extends Model
{
	/**
	 * @var integer
	 */
	public $id;

	/**
	 * @var string
	 */
	public $fio;

    /**
	 * @var string
	 */
    public $username;

    /**
	 * @var string
	 */
    public $email;

	/**
	 * @var string
	 */
	public $phone;

    /**
	 * @var int
	 */
    public $created_at;

    /**
	 * @var Finder
	 */
    protected $finder;

    /**
     * @param Finder $finder
     * @param array $config
     */
    public function __construct(Finder $finder, $config = []) {
        $this->finder = $finder;
        parent::__construct($config);
    }

    /**
	 * @inheritdoc
	 */
    public function rules() {
        return [
            'fieldsSafe' => [['id', 'username', 'email', 'fio', 'phone', 'created_at'], 'safe'],
            'createdDefault' => ['created_at', 'default', 'value' => null],
        ];
    }

    /**
	 * @inheritdoc
	 */
    public function attributeLabels() {
        return [
			'id' => Yii::t('user', 'field_id'),
            'username' => Yii::t('user', 'field_username'),
            'email' => Yii::t('user', 'field_email'),
			'fio' => Yii::t('user', 'field_fio'),
			'phone' => Yii::t('user', 'field_phone'),
            'created_at' => Yii::t('user', 'field_created_at'),
        ];
    }

    /**
     * @param $params
     *
     * @return ActiveDataProvider
     */
    public function search($params) {
        $query = $this->finder->getUserQuery();
		$query->joinWith(['profile']);

		// Create data provider
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

		// Set data provider sort
		$dataProvider->setSort([
			'attributes' => [
				'id',
				'username',
				'email',
				'created_at',
				'fio' => [
					'asc' => ['CONCAT('.UserProfile::tableName().'.last_name, " ", '.UserProfile::tableName().'.first_name, " ", '.UserProfile::tableName().'.middle_name)' => SORT_ASC],
					'desc' => ['CONCAT('.UserProfile::tableName().'.last_name, " ", '.UserProfile::tableName().'.first_name, " ", '.UserProfile::tableName().'.middle_name)' => SORT_DESC],
				],
				'phone' => [
					'asc' => [UserProfile::tableName().'.phone' => SORT_ASC],
					'desc' => [UserProfile::tableName().'.phone' => SORT_DESC],
				],
			]
		]);

        if (!($this->load($params) && $this->validate())) {
			return $dataProvider;
		}
	
		// Grid filtering conditions
		$query->andFilterWhere([
			User::tableName().'.id' => $this->id,
		]);
		
        $query->andFilterWhere(['like', User::tableName().'.username', $this->username]);
		$query->andFilterWhere(['like', User::tableName().'.email', $this->email]);
		$query->andFilterWhere(['like', UserProfile::tableName().'.phone', $this->phone]);
		$query->andFilterWhere(['like', 'CONCAT('.UserProfile::tableName().'.last_name, " ", '.UserProfile::tableName().'.first_name, " ", '.UserProfile::tableName().'.middle_name)', $this->fio]);

		if ($this->created_at !== null) {
			$date = strtotime($this->created_at);
			$query->andFilterWhere(['between', 'created_at', $date, $date + 3600 * 24]);
		}

        return $dataProvider;
    }
}
