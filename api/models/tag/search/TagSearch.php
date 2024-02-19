<?php
namespace api\models\tag\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

use common\modules\base\helpers\enum\Status;

use api\models\tag\Tag;

/**
 * TagSearch represents the model behind the search form about `app\models\tag\Tag`.
 */
class TagSearch extends Tag
{
	/**
	 * @inheritdoc
	 */
	public function rules(): array
    {
		return [
			[['type', 'status', 'created_at', 'updated_at'], 'integer'],
            [['title'], 'string'],
            [['id'], 'safe'],
            [['status'], 'default', 'value' => Status::ENABLED],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function scenarios()
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
	public function search($params = [])
    {
		$query = Tag::find()->alias('c')
            ->joinWith([
                'links' => function ($query) {
                    $query->with([
                        'tagModule' => function ($query) {
                            $query->alias('ltm');
                        },
                        'links' => function($query) {
                            $query->alias('t');
                        },
                    ]);
                },
                'media' => function ($query) {
                    $query->alias('m');
                },
            ])
            ->with([
                'catalogFieldGroups' => function ($query) {
                    $query->with(['fields']);
                }
            ])
            ->where([]);

		$pagination = [
            'params' => Yii::$app->request->queryParams,
            'pageSizeLimit' => [1, 20, 50, 100],
        ];

		$perPage = Yii::$app->request->getQueryParam('per-page');
		if (!is_null($perPage) && intval($perPage) === 0) {
		    $pagination = false;
        }
		
		// Create data provider
		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'pagination' => $pagination,
            'sort' => [
                'params' => Yii::$app->request->queryParams,
                'defaultOrder' => [
                    'title' => SORT_ASC,
                ],
            ]
		]);

		$this->load($params);

		if (!$this->validate()) {
			return $dataProvider;
		}

		if (is_array($this->id)) {
		    $query->andFilterWhere(['in', 'c.id', $this->id]);
        }
		else {
		    $query->andFilterWhere(['c.id' => $this->id]);
        }

        $query->andFilterWhere(['c.status' => $this->status]);

        $query->andFilterWhere(['like', 'c.title', $this->title]);
        $query->andFilterCompare('c.type', $this->type, '&');

        if (is_array($this->created_at) && (isset($this->created_at['start']) || isset($this->created_at['end'])) && $this->created_at['start'] && $this->created_at['end']) {
            $start = $this->created_at['start'];
            $end = $this->created_at['end'];
            $query->andFilterWhere([
                'between',
                'FROM_UNIXTIME(c.date_at, "%d-%m-%Y")',
                $start,
                $end,
            ]);
        }
        else if ($this->created_at && is_scalar($this->created_at)) {
            $query->andFilterWhere([
                'FROM_UNIXTIME(c.date_at, "%d-%m-%Y")' => $this->created_at,
            ]);
        }

		return $dataProvider;
	}
}