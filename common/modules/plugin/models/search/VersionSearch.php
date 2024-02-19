<?php
namespace common\modules\plugin\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Expression;

use common\modules\base\components\ActiveQuery;

use common\modules\rbac\helpers\enum\Role;

use common\modules\content\helpers\enum\Status;

use common\modules\plugin\models\Version;

/**
 * VersionSearch represents the model behind the search form of `common\modules\plugin\models\Version`.
 */
class VersionSearch extends Version
{
    /**
     * {@inheritdoc}
     */
    public function rules(){
        return [
            [['id', 'plugin_id', 'status'], 'integer'],
			[['latest'], 'boolean'],
            [['text', 'version', 'url', 'date_at'], 'safe'],
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
    	
    	/** @var ActiveQuery $query */
        $query = Version::find();
	
		$query->andWhere(['not in', self::tableName().'.status', [Status::TEMP]]);
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        // Set data provider sort
        $dataProvider->setSort([
            'attributes' => [
                'id',
                'url',
                'text',
                'latest',
                'status',
                'date_at',
				'version' => [
					'asc' => ["INET_ATON(SUBSTRING_INDEX(CONCAT(version,'.0.0.0'),'.',4))" => SORT_ASC],
					'desc' => ["INET_ATON(SUBSTRING_INDEX(CONCAT(version,'.0.0.0'),'.',4))" => SORT_DESC],
					'default' => SORT_DESC
				]
            ],
            'defaultOrder' => [
              	'version' => SORT_DESC,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            self::tableName().'.id' => $this->id,
            self::tableName().'.latest' => $this->latest,
            self::tableName().'.status' => $this->status,
        ]);
	
		$query->andFilterWhere(['like', self::tableName().'.version', $this->version]);
		$query->andFilterWhere(['like', self::tableName().'.url', $this->url]);
		$query->andFilterWhere(['like', self::tableName().'.text', $this->text]);
	    
        $query->andFilterWhere(['FROM_UNIXTIME('.self::tableName().'.date_at, "%d-%m-%Y")' => $this->date_at]);

        return $dataProvider;
    }
}
