<?php
namespace api\models\comment\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

use common\modules\comments\helpers\enum\Status;

use common\modules\user\models\User;

use common\modules\vote\models\Vote;

use api\models\comment\Comment;

/**
 * CommentSearch represents the model behind the search form about `app\models\comment\Comment`.
 */
class CommentSearch extends Comment
{
	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[['id', 'status', 'created_at', 'updated_at'], 'integer'],
            [['content'], 'string'],
            [['module_id'], 'safe'],
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
     * @param integer $moduleType
     * @param integer $moduleId
     * @param array $params
     *
     * @return ActiveDataProvider
     */
	public function search($moduleType, $moduleId, $params = []) {
		$query = Comment::find()->joinWith([
            'company' => function($query) {
				$query->where([]);
			},
			'author' => function($query) {
				$query->joinWith([
				    'profile',
                    'mediaAvatar' => function($query) {
                        $query->alias('ma')->where([]);
                    }
                ]);
			},
		])->where(self::tableName().'.module_type = :module_type AND '.self::tableName().'.entity_id = :entity_id', [
		    ':module_type' => (int)$moduleType,
			':entity_id' => (int)$moduleId,
		])->andWhere(['in', self::tableName().'.status', [Status::ENABLED, Status::DELETED]])->orderBy([
            self::tableName().'.parent_id' => SORT_ASC,
		    self::tableName().'.created_at' => SORT_ASC
        ])->votes();
		
		// Create data provider
		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'pagination' => [
				'pageSize' => 100
			],
		]);

		$this->load($params);

		if (!$this->validate()) {
			return $dataProvider;
		}

		return $dataProvider;
	}
}