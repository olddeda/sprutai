<?php
namespace api\models\favorite\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

use api\models\favorite\Favorite;

/**
 * FavoriteSearch represents the model behind the search form about `app\models\favorite\Favorite`.
 */
class FavoriteSearch extends Favorite
{

    /**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[['id', 'group_id', 'module_type', 'module_id', 'user_id'], 'integer'],
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
            ->with([
                'group',
                'user' => function($query) {
                    $query->with(['profile']);
                }
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
                    'module_id',
                    'user_id',
                    'created_at',
                    'updated_at'
                ],
                'defaultOrder' => [
                    'created_at' => SORT_DESC
                ]
            ],
		]);

        $this->load(ArrayHelper::getValue($params, 'filter', []), '');

		if (!$this->validate()) {
			return $dataProvider;
		}

        $query->andFilterWhere([
            self::tableName().'.module_type' => $this->module_type,
            self::tableName().'.module_id' => $this->module_id,
            self::tableName().'.user_id' => $this->user_id,
        ]);

		return $dataProvider;
	}
}