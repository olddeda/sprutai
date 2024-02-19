<?php
namespace common\modules\tag\models\search;

use common\modules\base\components\bitmask\BitmaskBehavior;
use common\modules\base\components\Debug;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

use common\modules\base\helpers\enum\Status;

use common\modules\tag\models\Tag;

/**
 * TagSearch represents the model behind the search form about `common\modules\tag\models\Tag`.
 */
class TagSearch extends Tag
{
	/**
	 * @inheritdoc
	 */
	public function init() {
		parent::init();
		unset($this->type);
	}
	
	/**
	 * @return array
	 */
	public function behaviors(): array
    {
		$behaviors = parent::behaviors();
		unset($behaviors['sluggable']);
		return $behaviors;
	}
	
	/**
	 * @inheritdoc
	 */
	public function rules(): array
    {
		return [
			[['id', 'type', 'status', 'created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
			[['title', 'created_at', 'updated_at'], 'safe'],
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
		$query = Tag::find();
		$query->where(['not in', self::tableName().'.status', [Status::TEMP, Status::DELETED]]);
		
		// add conditions that should always apply here
		$dataProvider = new ActiveDataProvider([
			'query' => $query,
		]);
		
		// Set data provider sort
		$dataProvider->setSort([
			'attributes' => [
				'id',
				'type',
				'title',
				'status',
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
			'id' => $this->id,
			'status' => $this->status,
		]);

		if ($this->type) {
            $query->andFilterCompare(Tag::tableName().'.type', $this->type, '&');
        }

		$query->andFilterWhere(['like', self::tableName().'.title', $this->title]);
		
		// Add filter time condition
		$query->andFilterWhere([
			'FROM_UNIXTIME('.self::tableName().'.created_at, "%d-%m-%Y")' => $this->created_at,
			'FROM_UNIXTIME('.self::tableName().'.updated_at, "%d-%m-%Y")' => $this->updated_at,
		]);
		
		return $dataProvider;
	}
}
