<?php
namespace common\modules\plugin\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

use common\modules\content\helpers\enum\Status;

use common\modules\rbac\helpers\enum\Role;

use common\modules\plugin\models\Plugin;

/**
 * PluginSearch represents the model behind the search form about `common\modules\plugin\models\Plugin`.
 */
class PluginSearch extends Plugin
{

	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[['id', 'type', 'status', 'created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
			[['title', 'status', 'created_at', 'updated_at'], 'safe'],
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
		$query = Plugin::find()->joinWith([
			'mediaLogo',
		]);
		
		$query->andWhere(['not in', self::tableName().'.status', [Status::TEMP]]);
		
		if (!Yii::$app->user->hasRole([Role::SUPERADMIN, Role::ADMIN, Role::EDITOR])) {
			$query->andWhere('author_id = :author_id', [
				':author_id' => Yii::$app->user->id,
			]);
		}

		// add conditions that should always apply here
		$dataProvider = new ActiveDataProvider([
			'query' => $query,
		]);

		// Set data provider sort
		$dataProvider->setSort([
			'attributes' => [
				'id',
				'status',
				'created_at',
				'updated_at',
				'title',
			]
		]);

		$this->load($params);

		if (!$this->validate()) {
			return $dataProvider;
		}

		// grid filtering conditions
		$query->andFilterWhere([
			self::tableName().'.id' => $this->id,
			self::tableName().'.content_id' => $this->content_id,
			self::tableName().'.status' => $this->status,
		]);
		
		$query->andFilterWhere(['like', self::tableName().'.title', $this->title]);

		// Add filter time condition
		$query->andFilterWhere([
			'FROM_UNIXTIME('.self::tableName().'.created_at, "%d-%m-%Y")' => $this->created_at,
			'FROM_UNIXTIME('.self::tableName().'.updated_at, "%d-%m-%Y")' => $this->updated_at,
		]);

		return $dataProvider;
	}
}
