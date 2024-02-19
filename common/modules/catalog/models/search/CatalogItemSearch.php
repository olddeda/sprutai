<?php
namespace common\modules\catalog\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

use common\modules\base\helpers\enum\Status;

use common\modules\catalog\models\CatalogItem;

/**
 * CatalogItemSearch represents the model behind the search form about `common\modules\catalog\models\CatalogItem`.
 */
class CatalogItemSearch extends Shortener
{
	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[['id', 'status', 'created_by', 'updated_by',], 'integer'],
			[['title', 'description', 'created_at', 'updated_at'], 'safe'],
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
		
		// Add conditions that should always apply here
		$dataProvider = new ActiveDataProvider([
			'query' => $query,
		]);
		
		// Set data provider sort
		$dataProvider->setSort([
			'defaultOrder' => [
				'id' => SORT_DESC,
			],
		]);
		
		$this->load($params);
		
		if (!$this->validate()) {
			return $dataProvider;
		}
		
		$query->andFilterWhere([
			self::tableName().'.id' => $this->id,
			self::tableName().'.status' => $this->status,
			self::tableName().'.counter' => $this->counter,
		]);
        
        $query->andFilterWhere(['like', 'title', $this->title]);
        $query->andFilterWhere(['like', 'url', $this->url]);
        $query->andFilterWhere(['like', 'description', $this->description]);
        
        if ($this->expiration_at) {
            $query->andFilterWhere([
                'FROM_UNIXTIME('.self::tableName().'.expiration_at, "%d-%m-%Y")' => $this->expiration_at,
            ]);
        }
        
        if ($this->created_at) {
            $query->andFilterWhere([
                'FROM_UNIXTIME('.self::tableName().'.created_at, "%d-%m-%Y")' => $this->created_at,
            ]);
        }
        
        if ($this->updated_at) {
            $query->andFilterWhere([
                'FROM_UNIXTIME('.self::tableName().'.updated_at, "%d-%m-%Y")' => $this->updated_at,
            ]);
        }
		
		return $dataProvider;
	}
}
