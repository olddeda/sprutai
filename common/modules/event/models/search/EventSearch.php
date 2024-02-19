<?php
namespace common\modules\event\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

use common\modules\event\models\Event;

use common\modules\user\models\User;
use common\modules\user\models\UserProfile;
use common\modules\event\models\EventType;

/**
 * EventSearch represents the model behind the search form of `common\modules\event\models\Event`.
 */
class EventSearch extends Event
{
	public $user_fio;
	

    /**
     * {@inheritdoc}
     */
    public function rules(){
        return [
            [['id', 'module_type', 'module_id', 'user_id', 'status'], 'integer'],
            [['text', 'user_fio', 'date_at'], 'safe'],
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
        $query = Event::find();

        $query->joinWith([
            'user' => function ($query) {
                $query->joinWith([
                	'profile',
                ]);
            },
        ]);
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        // Set data provider sort
        $dataProvider->setSort([
            'attributes' => [
                'id',
                'text',
                'status',
                'date_at',
                'user_fio' => [
                    'asc' => ['CONCAT('.UserProfile::tableName().'.last_name, " ", '.UserProfile::tableName().'.first_name, " ", '.UserProfile::tableName().'.middle_name)' => SORT_ASC],
                    'desc' => ['CONCAT('.UserProfile::tableName().'.last_name, " ", '.UserProfile::tableName().'.first_name, " ", '.UserProfile::tableName().'.middle_name)' => SORT_DESC],
                    'default' => SORT_ASC
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
        ]);
    
		$query->andFilterWhere(['like', self::tableName().'.text', $this->text]);
	    
        $query->andFilterWhere(['FROM_UNIXTIME('.self::tableName().'.date_at, "%d-%m-%Y")' => $this->date_at]);

        return $dataProvider;
    }
}
