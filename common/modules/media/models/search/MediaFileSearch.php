<?php
namespace common\modules\media\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

use common\modules\base\components\Debug;
use common\modules\base\helpers\enum\Status;

use common\modules\media\models\MediaFile;
use common\modules\media\helpers\enum\Type;

/**
 * MediaFileSearch represents the model behind the search form about `common\modules\media\models\MediaFile`.
 */
class MediaFileSearch extends MediaImage
{
	public $width_and_height;

	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[['id', 'module_type', 'module_id', 'size', 'status', 'created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
			[['title'], 'safe'],
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
		$query = MediaFile::find();
		$query->where = [];
		$query->params = [];

		// Add type condition
		$query->andWhere(MediaFile::tableName().'.type = :type', [
			':type' => Type::FILE,
		]);

		// Add status condition
		$query->andWhere(['in', MediaFile::tableName().'.status', [
			Status::ENABLED,
			Status::DELETED,
		]]);

		// Create data provider
		$dataProvider = new ActiveDataProvider([
			'query' => $query,
		]);

		// Set data provider sort
		$dataProvider->setSort([
			'attributes' => [
				'id',
				'module_type',
				'module_id',
				'title',
				'size',
				'status',
			]
		]);

		$this->load($params);

		if (!$this->validate()) {
			return $dataProvider;
		}

		$query->andFilterWhere([
			'id' => $this->id,
			'module_type' => $this->module_type,
			'module_id' => $this->module_id,
			'size' => $this->size,
			'status' => $this->status,
			'created_by' => $this->created_by,
			'updated_by' => $this->updated_by,
			'created_at' => $this->created_at,
			'updated_at' => $this->updated_at,
		]);

		$query->andFilterWhere(['like', 'title', $this->title]);

		return $dataProvider;
	}
}
