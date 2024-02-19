<?php
namespace api\models\catalog\search;

use api\models\catalog\CatalogItem;
use api\models\catalog\CatalogItemOrder;
use api\models\company\Company;
use common\modules\base\helpers\enum\Status;
use common\modules\catalog\helpers\enum\StatusOrder;
use common\modules\company\models\CompanyUser;
use common\modules\payment\models\Payment;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

/**
 * CatalogItemOrderSearch represents the model behind the search form about `app\models\catalog\CatalogItemOrder`.
 */
class CatalogItemOrderSearch extends CatalogItemOrder
{
    public $payment_id;

    /**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[['id', 'payment_id', 'catalog_item_id', 'company_id', 'delivery_type', 'status'], 'integer'],
            [['fio', 'phone', 'email', 'address', 'delivery_code', 'license'], 'string'],
            [['price'], 'double'],
            [['created_at'], 'safe'],

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
            ->joinWith([
                'catalogItem',
                'payment',
            ])
            ->where(['<>', self::tableName().'.status', StatusOrder::DELETED]);

        if (!Yii::$app->user->isSuperAdmin && !Yii::$app->user->isAdmin && !Yii::$app->user->isEditor) {
            $ids = CompanyUser::find()
                ->select('company_id')
                ->where([
                    'user_id' => Yii::$app->user->id,
                    'status' => Status::ENABLED,
                ])->column();
            if (!count($ids)) {
                $ids = [-1];
            }
            $query->andWhere(['in', self::tableName().'.company_id', $ids]);
        }

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
                    'price',
                    'fio',
                    'email',
                    'phone',
                    'address',
                    'delivery_type',
                    'delivery_code',
                    'license',
                    'status',
                    'created_at',
                    'catalog_item_id' => [
                        'asc' => [CatalogItem::tableName().'.title' => SORT_ASC],
                        'desc' => [CatalogItem::tableName().'.title' => SORT_DESC],
                    ],
                    'payment_id' => [
                        'asc' => [Payment::tableName().'.id' => SORT_ASC],
                        'desc' => [Payment::tableName().'.id' => SORT_DESC],
                    ],
                    'company_id' => [
                        'asc' => [Company::tableName().'.title' => SORT_ASC],
                        'desc' => [Company::tableName().'.title' => SORT_DESC],
                    ],
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
            self::tableName().'.status' => $this->status,
            self::tableName().'.catalog_item_id' => $this->catalog_item_id,
            self::tableName().'.company_id' => $this->company_id,
            self::tableName().'.delivery_type' => $this->delivery_type,
        ]);

        $query->andFilterWhere(['like', self::tableName().'.id', $this->id]);
        $query->andFilterWhere(['like', self::tableName().'.price', $this->price]);
        $query->andFilterWhere(['like', self::tableName().'.fio', $this->fio]);
        $query->andFilterWhere(['like', self::tableName().'.phone', $this->phone]);
        $query->andFilterWhere(['like', self::tableName().'.email', $this->email]);
        $query->andFilterWhere(['like', self::tableName().'.address', $this->address]);
        $query->andFilterWhere(['like', self::tableName().'.delivery_code', $this->delivery_code]);
        $query->andFilterWhere(['like', self::tableName().'.license', $this->license]);
        $query->andFilterWhere(['like', Payment::tableName().'.id', $this->payment_id]);

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