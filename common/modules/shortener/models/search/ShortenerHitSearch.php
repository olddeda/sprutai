<?php
namespace common\modules\shortener\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

use common\modules\base\helpers\enum\Status;

use common\modules\shortener\models\ShortenerHit;

/**
 * ShortenerHitSearch represents the model behind the search form about `common\modules\shortener\models\ShortenerHit`.
 */
class ShortenerHitSearch extends ShortenerHit
{
	/**
	 * @inheritdoc
	 */
	public function rules() {
        return [
            [['link_id'], 'integer'],
            [['datetime', 'ip', 'user_agent', 'country', 'city', 'os', 'os_version', 'browser', 'browser_version'], 'safe'],
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
				'created_at' => SORT_DESC,
			],
		]);
		
		$this->load($params);
		
		if (!$this->validate()) {
			return $dataProvider;
		}
		
        $query->andFilterWhere([
            'link_id' => $this->link_id,
            'os_version' => $this->os_version,
            'browser_version' => $this->browser_version,
        ]);
        
        $query
            ->andFilterWhere(['like', 'ip', $this->ip])
            ->andFilterWhere(['like', 'user_agent', $this->user_agent])
            ->andFilterWhere(['like', 'ountry', $this->country])
            ->andFilterWhere(['like', 'city', $this->city])
            ->andFilterWhere(['like', 'os', $this->os])
            ->andFilterWhere(['like', 'browser', $this->browser]);
		
		return $dataProvider;
	}
}
