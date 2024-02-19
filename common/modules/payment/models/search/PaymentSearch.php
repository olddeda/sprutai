<?php
namespace common\modules\payment\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

use common\modules\payment\models\Payment;

use common\modules\user\models\User;
use common\modules\user\models\UserProfile;
use common\modules\user\models\UserAddress;
use common\modules\user\models\UserAccount;

use common\modules\payment\models\PaymentType;

/**
 * PaymentSearch represents the model behind the search form of `common\modules\payment\models\Payment`.
 */
class PaymentSearch extends Payment
{
	public $date;
	
    public $user_fio;
    public $user_lastname;
	public $user_firstname;
	public $user_middlename;
    public $user_username;
    public $user_email;
    public $user_phone;
    public $user_address;
    public $user_telegram;
    public $user_github;
    
    public $type_title;

    /**
     * {@inheritdoc}
     */
    public function rules(){
        return [
            [['id', 'module_type', 'module_id', 'payment_type_id', 'user_id', 'provider_id', 'status'], 'integer'],
            [['provider_error', 'descr', 'comment', 'user_fio', 'user_lastname', 'user_firstname', 'user_middlename', 'user_username', 'user_email', 'user_phone', 'user_address', 'user_telegram', 'user_github', 'date_at', 'type_title'], 'safe'],
            [['price'], 'number'],
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
        $query = Payment::find();
        
        $query->joinWith([
            'type',
            'user' => function ($query) {
                $query->joinWith([
                	'profile',
	                'address',
	                'telegram',
	                'github',
                ]);
            },
	        'moduleContent',
        ]);
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        // Set data provider sort
        $dataProvider->setSort([
            'attributes' => [
                'id',
                'descr',
                'comment',
                'status',
                'price',
                'price_tax',
                'date_at',
                'payment_type_id' => [
                    'asc' => [PaymentType::tableName().'.title' => SORT_ASC],
                    'desc' => [PaymentType::tableName().'.title' => SORT_DESC],
                ],
	            'type_title' => [
		            'asc' => [PaymentType::tableName().'.title' => SORT_ASC],
		            'desc' => [PaymentType::tableName().'.title' => SORT_DESC],
	            ],
	            'title' => [
		            'asc' => ['CONCAT(moduleContent.title, " ", '.PaymentType::tableName().'.title)' => SORT_ASC],
		            'desc' => ['CONCAT(moduleContent.title, " ", '.PaymentType::tableName().'.title)' => SORT_DESC],
	            ],
                'user_fio' => [
                    'asc' => ['CONCAT('.UserProfile::tableName().'.last_name, " ", '.UserProfile::tableName().'.first_name, " ", '.UserProfile::tableName().'.middle_name)' => SORT_ASC],
                    'desc' => ['CONCAT('.UserProfile::tableName().'.last_name, " ", '.UserProfile::tableName().'.first_name, " ", '.UserProfile::tableName().'.middle_name)' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'user_lastname' => [
                    'asc' => [UserProfile::tableName().'.last_name' => SORT_ASC],
                    'desc' => [UserProfile::tableName().'.last_name' => SORT_DESC],
                ],
	            'user_firstname' => [
		            'asc' => [UserProfile::tableName().'.first_name' => SORT_ASC],
		            'desc' => [UserProfile::tableName().'.first_name' => SORT_DESC],
	            ],
	            'user_middlename' => [
		            'asc' => [UserProfile::tableName().'.middle_name' => SORT_ASC],
		            'desc' => [UserProfile::tableName().'.middle_name' => SORT_DESC],
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
	            'user_address' => [
		            'asc' => [UserAddress::tableName().'.address' => SORT_ASC],
		            'desc' => [UserAddress::tableName().'.address' => SORT_DESC],
	            ],
	            'user_telegram' => [
		            'asc' => ['telegram.username' => SORT_ASC],
		            'desc' => ['telegram.username' => SORT_DESC],
	            ],
	            'user_github' => [
		            'asc' => ['github.username' => SORT_ASC],
		            'desc' => ['github.username' => SORT_DESC],
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

        // grid filtering conditions
        $query->andFilterWhere([
            self::tableName().'.id' => $this->id,
            self::tableName().'.user_id' => $this->user_id,
            self::tableName().'.status' => $this->status,
            self::tableName().'.payment_type_id' => $this->payment_type_id,
        ]);
    
		$query->andFilterWhere(['like', self::tableName().'.price', $this->price]);
		$query->andFilterWhere(['like', self::tableName().'.descr', $this->descr]);
		$query->andFilterWhere(['like', self::tableName().'.comment', $this->comment]);
        
        $query->andFilterWhere(['like', User::tableName().'.username', $this->user_username]);
        $query->andFilterWhere(['like', User::tableName().'.email', $this->user_email]);
	
        if ($this->user_lastname == '-')
	        $query->andWhere([UserProfile::tableName().'.last_name' => '']);
	    else
        	$query->andFilterWhere(['like', UserProfile::tableName().'.last_name', $this->user_lastname]);
	
	    if ($this->user_firstname == '-')
		    $query->andWhere([UserProfile::tableName().'.first_name' => '']);
	    else
	        $query->andFilterWhere(['like', UserProfile::tableName().'.first_name', $this->user_firstname]);
	
	    if ($this->user_middlename == '-')
		    $query->andWhere([UserProfile::tableName().'.middle_name' => '']);
	    else
	        $query->andFilterWhere(['like', UserProfile::tableName().'.middle_name', $this->user_middlename]);
	
	    if ($this->user_phone == '-')
		    $query->andWhere([UserProfile::tableName().'.phone' => NULL]);
	    else
		    $query->andFilterWhere(['like', UserProfile::tableName().'.phone', $this->user_phone]);
	    
	    if ($this->user_address == '-')
		    $query->andWhere([UserAddress::tableName().'.address' => NULL]);
	    else
		    $query->andFilterWhere(['like', UserAddress::tableName().'.address', $this->user_address]);
	
	    if ($this->user_telegram == '-')
		    $query->andWhere(['telegram.username' => NULL]);
	    else
		    $query->andFilterWhere(['like', 'telegram.username', $this->user_telegram]);
	
	    if ($this->user_github == '-')
		    $query->andWhere(['github.username' => NULL]);
	    else
		    $query->andFilterWhere(['like', 'github.username', $this->user_github]);
	
		// Add filter time condition
		$query->andFilterWhere([
			'FROM_UNIXTIME('.self::tableName().'.date_at, "%d-%m-%Y")' => $this->date_at,
		]);

        return $dataProvider;
    }
}
