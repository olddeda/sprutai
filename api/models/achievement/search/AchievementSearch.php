<?php
namespace api\models\achievement\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

use api\models\achievement\Achievement;

/**
 * AchievementSearch represents the model behind the search form about `app\models\achievement\Achievement`.
 */
class AchievementSearch extends Achievement
{

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['id', 'type', 'level', 'sequence', 'status'], 'integer'],
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
        $query = self::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'params' => $params,
                'attributes' => [
                    'id',
                    'type',
                    'title',
                    'level',
                    'sequence',
                    'status',
                    'created_at',
                    'updated_at'
                ],
                'defaultOrder' => [
                    'type' => SORT_ASC,
                    'level' => SORT_ASC,
                ]
            ],
        ]);

        $this->load(ArrayHelper::getValue($params, 'filter', []), '');

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            self::tableName().'.type' => $this->type,
            self::tableName().'.level' => $this->level,
            self::tableName().'.sequence' => $this->sequence,
            self::tableName().'.status' => $this->status,
        ]);

        $query->andFilterWhere(['like', self::tableName().'.title', $this->title]);

        $dataProvider->pagination = false;

        return $dataProvider;
    }
}