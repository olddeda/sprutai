<?php
namespace api\models\content\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

use common\modules\rbac\helpers\enum\Role;

use common\modules\content\helpers\enum\Status;

use api\models\content\ContentHistory;

/**
 * ContentHistorySearch represents the model behind the search form about `app\models\content\ContentHistory`.
 */
class ContentHistorySearch extends ContentHistory
{
    /** @var bool */
    public $isModeAdmin = false;

	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
		    [['content_id'], 'required'],
			[['id', 'content_id', 'user_id', 'created_at', 'updated_at'], 'integer'],
            [['json', 'status'], 'safe'],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function scenarios() {
		return Model::scenarios();
	}

	/**
	 * @param array $params
	 *
	 * @return ActiveDataProvider
     */
	public function search($params) {

	    /** @var ActiveQuery $query */
		$query = ContentHistory::find()
            ->where([
                '<>', self::tableName().'.status', Status::DELETED
            ])
        ;

        $this->load(ArrayHelper::getValue($params, 'filter', []), '');

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'pagination' => [
				'defaultPageSize' => 10,
                'pageSizeLimit' => [0, 10],
			],
            'sort' => [
                'params' => Yii::$app->request->getQueryParams(),
                'attributes' => [
                    'id',
                    'content_id',
                    'user_id',
                    'status',
                    'created_at',
                ],
                'defaultOrder' => [
                    'created_at' => SORT_DESC
                ]
            ],
		]);

		if (!$this->validate()) {
		    return $this;
		}

        if (!Yii::$app->user->hasRole([Role::SUPERADMIN, Role::ADMIN, Role::EDITOR])) {
            $this->user_id = Yii::$app->user->id;
        }

		$query->andFilterWhere([
            self::tableName().'.id' => $this->id,
            self::tableName().'.content_id' => $this->content_id,
            self::tableName().'.user_id' => $this->user_id,
		]);

        if (is_array($this->status)) {
            $query->andFilterWhere(['in', self::tableName().'.status', $this->status]);
        }
        else {
            $query->andFilterWhere([
                self::tableName().'.status' => $this->status,
            ]);
        }

		return $dataProvider;
	}
}