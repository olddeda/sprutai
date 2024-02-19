<?php
namespace api\models\achievement\search;

use api\models\achievement\Achievement;
use common\modules\achievement\models\AchievementUserStat;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

use api\models\achievement\AchievementUser;

/**
 * AchievementUserSearch represents the model behind the search form about `app\models\achievement\AchievementUser`.
 */
class AchievementUserSearch extends AchievementUser
{

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['id', 'achievement_id', 'user_id'], 'integer'],
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
        $query = self::find()
            ->select([
                '*',
                '(
                    SELECT c.count 
                    FROM '.AchievementUserStat::tableName().' AS c
                    WHERE c.type = '.Achievement::tableName().'.type
                    AND c.user_id = '.AchievementUser::tableName().'.user_id
                ) AS count'
            ])
            ->joinWith([
                'achievement',
            ])
            ->with([
                'user' => function ($query) {
                    $query->with('profile');
                }
            ])
        ;

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'params' => $params,
                'attributes' => [
                    'id',
                    'achievement_id',
                    'user_id',
                    'created_at',
                    'updated_at'
                ],
                'defaultOrder' => [
                    'achievement_id' => SORT_ASC,
                ]
            ],
        ]);

        $this->load(ArrayHelper::getValue($params, 'filter', []), '');

        if (!$this->validate()) {
            return $dataProvider;
        }

        if ($this->user_id) {
            $dataProvider->pagination = false;
        }

        $query->andFilterWhere([
            self::tableName().'.achievement_id' => $this->achievement_id,
            self::tableName().'.user_id' => $this->user_id,
        ]);

        return $dataProvider;
    }
}