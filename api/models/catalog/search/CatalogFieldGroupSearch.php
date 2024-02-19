<?php
namespace api\models\catalog\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

use common\modules\base\helpers\enum\Status;

use api\models\catalog\CatalogFieldGroup;

/**
 * CatalogFieldGroupSearch represents the model behind the search form about `app\models\catalog\CatalogFieldGroup`.
 */
class CatalogFieldGroupSearch extends CatalogFieldGroup
{
    /**
	 * @inheritdoc
	 */
	public function rules(): array {
		return [
			[['id', 'status'], 'integer'],
            [['title'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function scenarios(): array
    {
		return Model::scenarios();
	}

	/**
	 * Creates data provider instance with search query applied
	 *
	 * @param array $params
	 *
	 * @return ActiveDataProvider
     */
	public function search($params = []): ActiveDataProvider
    {
        $query = self::find()->where(['<>', self::tableName().'.status', Status::DELETED]);

		// Create data provider
		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'pagination' => [
                'pageSizeLimit' => [1, 150],
			],
            'sort' => [
                'params' => $params,
                'attributes' => [
                    'id',
                    'title',
                    'status',
                    'created_at',
                    'updated_at'
                ],
                'defaultOrder' => [
                    'title' => SORT_ASC
                ]
            ],
		]);

		$this->load(ArrayHelper::getValue($params, 'filter', []), '');

		if (!$this->validate()) {
			return $dataProvider;
		}

        $query->andFilterWhere([
            self::tableName().'.status' => $this->status,
        ]);

        $query->andFilterWhere(['like', self::tableName().'.id', $this->id]);
        $query->andFilterWhere(['like', self::tableName().'.title', $this->title]);

        if (is_array($this->created_at) && (isset($this->created_at['start']) || isset($this->created_at['end'])) && $this->created_at['start'] && $this->created_at['end']) {
            $start = $this->created_at['start'];
            $end = $this->created_at['end'];
            $query->andFilterWhere([
                'between',
                'FROM_UNIXTIME('.self::tableName().'.created_at, "%d-%m-%Y")',
                $start,
                $end,
            ]);
        }
        else if ($this->created_at && is_scalar($this->created_at)) {
            $query->andFilterWhere([
                'FROM_UNIXTIME('.self::tableName().'.created_at, "%d-%m-%Y")' => $this->created_at,
            ]);
        }

		return $dataProvider;
	}
}