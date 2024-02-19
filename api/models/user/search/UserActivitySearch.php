<?php
namespace api\models\user\search;

use common\modules\base\components\Debug;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

use api\models\user\UserActivity;
use function Clue\StreamFilter\fun;

/**
 * UserActivitySearch represents the model behind the search form about `app\models\user\UserActivity`.
 */
class UserActivitySearch extends UserActivity
{

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['id', 'type', 'module_type', 'module_id', 'user_id', 'from_user_id'], 'integer'],
            [['date_at', 'created_at', 'updated_at'], 'safe'],
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
        $query = self::find()
            ->with([
                'user' => function ($query) {
                    $query->with(['mediaAvatar', 'profile', 'telegram']);
                },
                'userFrom' => function ($query) {
                    $query->with(['mediaAvatar', 'profile', 'telegram']);
                },
                'userModule' => function ($query) {
                    $query->with(['mediaAvatar', 'profile', 'telegram']);
                },
                'content' => function ($query) {
                    $query->select('*')->with([
                        'seoRelation',
                    ]);
                },
                'parentContent' => function ($query) {
                    $query->select('*')->with([
                        'seoRelation',
                    ]);
                },
                'comment' => function ($query) {
                    $query->votes();
                },
                'parentComment' => function ($query) {
                    $query->votes();
                },
                'catalogItem' => function ($query) {
                    $query->with([
                        'seoRelation',
                        'vendor',
                    ]);
                },
                'parentCatalogItem' => function ($query) {
                    $query->with([
                        'seoRelation',
                        'vendor',
                    ]);
                },
                'achievementUser' => function ($query) {
                },
                'parentAchievement' => function ($query) {
                },
            ])
        ;

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'params' => $params,
                'attributes' => [
                    'id',
                    'type',
                    'module_type',
                    'module_id',
                    'user_id',
                    'from_user_id',
                    'date_at',
                    'created_at',
                    'updated_at'
                ],
                'defaultOrder' => [
                   'date_at' => SORT_DESC,
                ]
            ],
            'pagination' => [
                'pageSize' => 20
            ]
        ]);

        $this->load(ArrayHelper::getValue($params, 'filter', []), '');

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            self::tableName().'.type' => $this->type,
            self::tableName().'.module_type' => $this->module_type,
            self::tableName().'.module_id' => $this->module_id,
            self::tableName().'.user_id' => $this->user_id,
            self::tableName().'.from_user_id' => $this->from_user_id,
        ]);

        return $dataProvider;
    }
}