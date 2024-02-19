<?php
namespace api\models\favorite\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

use common\modules\base\helpers\enum\Status;
use common\modules\base\helpers\enum\ModuleType;
use common\modules\content\models\Content;

use api\models\favorite\FavoriteGroup;
use api\models\favorite\Favorite;

/**
 * FavoriteGroupSearch represents the model behind the search form about `app\models\favorite\FavoriteGroup`.
 */
class FavoriteGroupSearch extends FavoriteGroup
{
    /** @var int */
    public $type;

    /**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[['id', 'module_type', 'module_id', 'user_id', 'sequence', 'count', 'count_total', 'type'], 'integer'],
            [['title'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
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
	public function search($params = []) {

        $this->load(ArrayHelper::getValue($params, 'filter', []), '');

	    $usersIds = [1];
	    if (!Yii::$app->user->isGuest) {
	        $usersIds[] = Yii::$app->user->id;
        }

        $queryJoin = Content::tableName().' AS m ON m.id = f.module_id AND m.status = '.Status::ENABLED;
        $countJoin = 'LEFT JOIN '.$queryJoin;
        $countAndWhere = '';
	    if ($this->type) {
	        if ($this->module_type == ModuleType::CONTENT) {
	            $countAndWhere = 'AND m.type = '.$this->type;
            }
        }

        $query = self::find()
            ->select([
                self::tableName().'.*',
                '(
                    SELECT COUNT(*)
                    FROM '.Favorite::tableName().' AS f
                    '.$countJoin.'
                    WHERE f.group_id = '.FavoriteGroup::tableName().'.id
                    AND f.user_id = '.(Yii::$app->user->id ? Yii::$app->user->id : 0).'
                    '.$countAndWhere.'
                ) AS count',
                '(
                    SELECT COUNT(*)
                    FROM (
                        SELECT DISTINCT m.id
                        FROM '.Favorite::tableName().' AS f
                        '.$countJoin.'
                        WHERE f.module_type = '.$this->module_type.'
                        AND f.user_id = '.(Yii::$app->user->id ? Yii::$app->user->id : 0).'
                        '.$countAndWhere.'
                    ) AS c
                ) AS count_total',
            ])
            ->joinWith([
                'items'
            ])
            ->where([
                'AND',
                ['in', self::tableName().'.user_id', $usersIds],
            ])
        ;

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'pagination' => false,
            'sort' => [
                'params' => $params,
                'attributes' => [
                    'id',
                    'module_type',
                    'user_id',
                    'title',
                    'sequence',
                    'created_at',
                    'updated_at'
                ],
                'defaultOrder' => [
                    'sequence' => SORT_ASC
                ]
            ],
		]);

		if (!$this->validate()) {
			return $dataProvider;
		}

        $query->andFilterWhere([
            self::tableName().'.module_type' => $this->module_type
        ]);

		return $dataProvider;
	}
}