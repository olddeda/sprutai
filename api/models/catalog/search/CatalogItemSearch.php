<?php
namespace api\models\catalog\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;
use yii\sphinx\Query;

use common\modules\base\helpers\enum\Status;
use common\modules\base\components\Debug;

use common\modules\tag\helpers\enum\Type as TagType;
use common\modules\tag\models\Tag;

use common\modules\base\helpers\enum\ModuleType;
use common\modules\catalog\helpers\enum\FieldFormat;
use common\modules\catalog\helpers\enum\FieldType;
use common\modules\catalog\models\CatalogField;
use common\modules\catalog\models\CatalogItemStat;
use common\modules\tag\models\TagModule;

use api\models\catalog\CatalogItem;
use api\models\catalog\CatalogItemField;
use api\models\favorite\Favorite;

/**
 * CatalogItemSearch represents the model behind the search form about `app\models\catalog\CatalogItem`.
 */
class CatalogItemSearch extends CatalogItem
{
    /** @var string */
    public $search;

    /** @var string */
	public $vendor_title;

	/** @var string */
	public $types_title;

    /** @var string */
    public $platforms_title;

    /** @var string */
    public $protocols_title;

	/** @var string */
	public $tags_title;

	/** @var integer */
	public $owner_user_id;

	/** @var integer */
    public $protocol_id;

    /** @var integer */
    public $compare_id;

    /** @var boolean */
    public $has_compare;

    /** @var boolean */
    public $has_spruthub_support;

    /**
     * @var array
     */
    public $fields;

    /**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[['id', 'is_sprut', 'status', 'type'], 'integer'],
            [['title', 'model', 'url', 'documentation_url', 'system_manufacturer', 'system_model'], 'string'],
            [['search', 'vendor_title', 'types_title', 'platforms_title', 'protocols_title'], 'safe'],
            [['tags_title', 'yandex_id', 'created_at', 'updated_at', 'owner_user_id', 'protocol_id', 'compare_id', 'fields'], 'safe'],
            [['count_compare'], 'integer'],
            [['has_compare', 'has_spruthub_support'], 'boolean'],
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

        $query = CatalogItem::find()
            ->with([
                'media',
                'commentsUsers',
                'seoRelation',
                'contents',
                'vendor' => function($query) {
                    $query->alias('vendor')->with([
                        'links'
                    ]);
                },
                'tags' => function($query) {
                    $query->alias('tags')->with([
                        'links' => function ($query) {
                            $query->with([
                                'links'
                            ]);
                        }
                    ]);
                },
            ])
            ->where(['<>', self::tableName().'.status', Status::DELETED])
        ;

        $joinFavorites = true;
        $exclude = ArrayHelper::getValue(Yii::$app->request->get(), 'stats');
        if (in_array('stats', explode(',', $exclude))) {
            $joinFavorites = false;
        }
        if ($joinFavorites) {
            $query->joinWith([
                'stat',
                'favorites',
                'favoritesHaveOwn',
            ]);
            $query->votes();
        }

        $sort = ArrayHelper::getValue($params, 'sort');

        $filters = ArrayHelper::getValue($params, 'filter', []);
        if (isset($filters['tags_title'])) {
            $tagsTitles = $filters['tags_title'];
            $tags = Tag::find()->where(['in', 'title', $tagsTitles])->all();
            if ($tags) {
                foreach ($tags as $tag) {
                    if ($tag->is_vendor) {
                        $filters['vendor_title'][] = $tag->title;
                        $filters['tags_title'] = array_diff($filters['tags_title'], [$tag->title]);
                    }
                    else if ($tag->is_type) {
                        $filters['types_title'][] = $tag->title;
                        $filters['tags_title'] = array_diff($filters['tags_title'], [$tag->title]);
                    }
                    else if ($tag->is_platform) {
                        $filters['platforms_title'][] = $tag->title;
                        $filters['tags_title'] = array_diff($filters['tags_title'], [$tag->title]);
                    }
                    else if ($tag->is_protocol) {
                        $filters['protocols_title'][] = $tag->title;
                        $filters['tags_title'] = array_diff($filters['tags_title'], [$tag->title]);
                    }
                }
            }
            $this->tags_title = $filters['tags_title'];
        }

        if (ArrayHelper::getValue($filters, 'vendor_title') || ArrayHelper::getValue($filters, 'vendors_title')  || strpos($sort, 'vendor_title') !== false) {
            $query->joinWith([
                'vendor' => function($query) {
                    $query->alias('vendor')->joinWith([
                        'media v_m'
                    ]);
                },
            ]);
        }

        if (ArrayHelper::getValue($params, 'filter.tags_title') || strpos($sort, 'tags_title') !== false) {
            $query->joinWith([
                'tags' => function($query) {
                    $query->alias('tags')->with([
                        'links',
                    ]);
                },
            ]);
        }

        foreach (['types' => 'types_title', 'platforms' => 'platforms_title', 'protocols' => 'protocols_title'] as $relation => $field) {
            if (ArrayHelper::getValue($filters, $field) || strpos($sort, $field) !== false) {
                $query->joinWith([
                    $relation
                ]);
            }
        }

        if (ArrayHelper::getValue($filters, 'protocol_id')) {
            $query->joinWith([
                'protocols'
            ]);
        }

        $hasSpruthubSupport = ArrayHelper::getValue($filters, 'has_spruthub_support');
        if ($hasSpruthubSupport != null) {
            $query->addSelect([
                'CAST((
                    SELECT COUNT(*) 
                    FROM '.TagModule::tableName().' AS t 
                    WHERE t.module_type = 81 
                    AND t.module_id = '.self::tableName().'.id 
                    AND t.tag_id = 94
                ) AS UNSIGNED) AS has_spruthub_support',
            ]);
            $query->andHaving([
                ($hasSpruthubSupport ? '>' : '='), 'has_spruthub_support', 0
            ]);
            $query->groupBy(self::tableName().'.id');
        }

		// Create data provider
		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'pagination' => [
                'pageSizeLimit' => [1, 500],
			],
            'sort' => [
                'params' => $params,
                'attributes' => [
                    'id',
                    'title',
                    'model',
                    'url',
                    'documentation_url',
                    'system_manufacturer',
                    'system_model',
                    'yandex_id',
                    'is_sprut',
                    'status',
                    'created_at',
                    'vendor_title' => [
                        'asc' => ['vendor.title' => SORT_ASC],
                        'desc' => ['vendor.title' => SORT_DESC],
                    ],
                    'types_title' => [
                        'asc' => ['types.title' => SORT_ASC],
                        'desc' => ['types.title' => SORT_DESC],
                    ],
                    'platforms_title' => [
                        'asc' => ['platforms.title' => SORT_ASC],
                        'desc' => ['platforms.title' => SORT_DESC],
                    ],
                    'protocols_title' => [
                        'asc' => ['protocols.title' => SORT_ASC],
                        'desc' => ['protocols.title' => SORT_DESC],
                    ],
                    'tags_title' => [
                        'asc' => ['tags.title' => SORT_ASC],
                        'desc' => ['tags.title' => SORT_DESC],
                    ],
                    'rating' => [
                        'asc' => [CatalogItemStat::tableName().'.rating' => SORT_ASC, CatalogItemStat::tableName().'.comments' => SORT_DESC],
                        'desc' => [CatalogItemStat::tableName().'.rating' => SORT_DESC, CatalogItemStat::tableName().'.comments' => SORT_DESC],
                    ],
                    'count_compare',
                    'has_compare' => [
                        'asc' => ['count_compare' => SORT_DESC],
                        'desc' => ['count_compare' => SORT_ASC],
                    ],
                ],
                'defaultOrder' => [
                    'id' => SORT_DESC
                ]
            ],
		]);

		$this->load($filters, '');

        if ($this->compare_id) {
            $query->addSelect([
                'CAST((
                    SELECT COUNT(*)
                    FROM '.Favorite::tableName().' AS f
                    LEFT JOIN '.Favorite::tableName().' AS ff ON ff.user_id = f.user_id AND ff.module_type = '.ModuleType::CATALOG_ITEM.' AND ff.module_id = '.(int)$this->compare_id.'
                    WHERE f.module_type = '.ModuleType::CATALOG_ITEM.'
                    AND f.module_id = '.CatalogItem::tableName().'.id
                    AND ff.id IS NOT NULL
                    AND f.module_id != '.(int)$this->compare_id.'
                ) AS UNSIGNED) AS count_compare'
            ]);
        }

		if (!$this->validate()) {
			return $dataProvider;
		}

		if (!$this->status && !Yii::$app->user->getIsAdmin())
		    $this->status = Status::ENABLED;

        $query->andFilterWhere(['like', self::tableName().'.id', $this->id]);
        $query->andFilterWhere(['like', self::tableName().'.title', $this->title]);
        $query->andFilterWhere(['like', self::tableName().'.model', $this->model]);
        $query->andFilterWhere(['like', self::tableName().'.url', $this->url]);
        $query->andFilterWhere(['like', self::tableName().'.documentation_url', $this->documentation_url]);
        $query->andFilterWhere(['like', self::tableName().'.system_manufacturer', $this->system_manufacturer]);
        $query->andFilterWhere(['like', self::tableName().'.system_model', $this->system_model]);
        $query->andFilterWhere(['like', self::tableName().'.yandex_id', $this->yandex_id]);
        $query->andFilterWhere([self::tableName().'.is_sprut' => $this->is_sprut]);
        $query->andFilterWhere([self::tableName().'.status' => $this->status]);
        $query->andFilterWhere(['protocols.id' => $this->protocol_id]);

        $this->addFiltersCondition($query, $this->vendor_title, 'vendor');
        $this->addFiltersCondition($query, $this->types_title, 'types');
        $this->addFiltersCondition($query, $this->platforms_title, 'platforms');
        $this->addFiltersCondition($query, $this->protocols_title, 'protocols');
        $this->addFiltersCondition($query, $this->tags_title, 'tags', true, true);

        $query->andFilterHaving(['like', 'count_compare', $this->count_compare]);

        if (in_array('has_compare', array_keys($filters))) {
            $query->andHaving([($this->has_compare ? '>' : '='), 'count_compare', 0]);
        }

        if (is_array($this->created_at) && (isset($this->created_at['start']) || isset($this->created_at['end'])) && $this->created_at['start'] && $this->created_at['end']) {
            $start = $this->created_at['start'];
            $end = $this->created_at['end'];
            $query->andFilterWhere([
                'between',
                'FROM_UNIXTIME(t.created_at, "%d-%m-%Y")',
                $start,
                $end,
            ]);
        }
        else if ($this->created_at && is_scalar($this->created_at)) {
            $query->andFilterWhere([
                'FROM_UNIXTIME(t.created_at, "%d-%m-%Y")' => $this->created_at,
            ]);
        }

        $ownerUser = ArrayHelper::getValue($params, 'owner_user_id');
        if ($ownerUser) {
            $query->joinWith(['favoritesDevices']);
            $query->andWhere('fh.user_id = :user_id', [
                ':user_id' => $ownerUser,
            ]);
        }

        if ($this->search) {
            $ids = (new Query())->select('id')->from('idx_catalog_item')->match(Yii::$app->sphinx->escapeMatchValue($this->search))->groupBy('id')->limit(100)->column();

            if (!count($ids)) {
                $ids = [-1];
            }
            $query->andFilterWhere([
                'in',
                self::tableName().'.id',
                $ids
            ]);

            $query->groupBy(self::tableName().'.id');
        }

        $sort = Yii::$app->request->get('sort');
        if ($sort == 'favorite') {
            if (!Yii::$app->user->isGuest) {
                $query->andWhere(Favorite::tableName().'.id IS NOT NULL');
                $orderBy[Favorite::tableName().'.created_at'] = SORT_DESC;
            }
            else {
                $query->andWhere(Favorite::tableName().'.id = - 1');
            }

            if ($favoriteId = (int)Yii::$app->request->get('favorite_id')) {

                $query->andWhere([Favorite::tableName().'.group_id' => $favoriteId]);
            }
        }

        $fields = ArrayHelper::getValue($filters, 'fields', []);
        if (count($fields)) {
            $query->joinWith(['fields']);
            $query->groupBy(self::tableName().'.id');
            $this->addFieldsCondition($query, $fields);
        }
        else {
            $query->with(['fields']);
        }

		return $dataProvider;
	}

    /**
     * @param Yii\db\Query $query
     * @param $filter
     * @param string $relation
     * @param false $compareCount
     */
	private function addFiltersCondition(yii\db\Query $query, $filter, string $relation, $compareCount = false)
    {
        if ($filter && is_array($filter)) {
            $query->andWhere(['in', $relation.'.title', $filter]);
            $query->groupBy(self::tableName().'.id');
            if ($compareCount) {
                $query->having('COUNT(DISTINCT '.$relation.'.id) >= '.count($filter));
            }
        }
        else {
            $query->andFilterWhere(['like', $relation.'.title', $filter]);
        }
    }

    /**
     * @param Yii\db\Query $query
     * @param array $fields
     */
	private function addFieldsCondition(yii\db\Query $query, array $fields)
    {
        foreach ($fields as $fieldName => $fieldValue) {
            $fieldIdentifier = str_replace('-from', '', $fieldName);
            $fieldIdentifier = str_replace('-to', '', $fieldIdentifier);

            if (strpos($fieldName, '-to') !== false && isset($fields[$fieldIdentifier.'-from'])) {
                continue;
            }

            $fieldIdentifier = str_replace('-from', '', $fieldName);
            $fieldIdentifier = str_replace('-to', '', $fieldIdentifier);

            $field = CatalogField::find()->where('identifier = :identifier', [':identifier' => $fieldIdentifier])->one();

            if (is_null($field)) {
                continue;
            }

            if ($field->type == FieldType::RANGE) {
                $this->addFieldRange($query, $fieldIdentifier, $fields);
            }
            elseif ($field->format == FieldFormat::NUMBER) {
                $this->addFieldBetween($query, $fieldIdentifier, $fields);
            }
            else {
                $this->addFieldLike($query, $fieldIdentifier, $fields);
            }
        }
    }

    /**
     * @param Yii\db\Query $query
     * @param string $fieldIdentifier
     * @param array $fields
     */
	private function addFieldRange(yii\db\Query $query, string $fieldIdentifier, array $fields)
    {
        $fieldFrom = $fieldIdentifier.'-from';
        $fieldTo = $fieldIdentifier.'-to';

        if (isset($fields[$fieldFrom])) {
            $alias = $fieldIdentifier.'_from';
            $query->leftJoin(CatalogItemField::tableName().' '.$alias, $alias.'.catalog_item_id = '.self::tableName().'.id');
            $query->andWhere([
                'AND',
                [$alias.'.name' => $fieldFrom],
                ['<=', $alias.'.value', (int)$fields[$fieldFrom]],
            ]);
        }

        if (isset($fields[$fieldTo])) {
            $alias = $fieldIdentifier.'_to';
            $query->leftJoin(CatalogItemField::tableName().' '.$alias, $alias.'.catalog_item_id = '.self::tableName().'.id');
            $query->andWhere([
                'AND',
                [$alias.'.name' => $fieldTo],
                ['>=', $alias.'.value', (int)$fields[$fieldTo]]
            ]);
        }
    }

    /**
     * @param Yii\db\Query $query
     * @param string $fieldIdentifier
     * @param array $fields
     */
    private function addFieldBetween(yii\db\Query $query, string $fieldIdentifier, array $fields)
    {
        $query->leftJoin(CatalogItemField::tableName().' '.$fieldIdentifier, $fieldIdentifier.'.catalog_item_id = '.self::tableName().'.id');

        $fieldFrom = $fieldIdentifier.'-from';
        $fieldTo = $fieldIdentifier.'-to';

        if (isset($fields[$fieldFrom]) && isset($fields[$fieldTo])) {
            $query->andWhere([
                'AND',
                [$fieldIdentifier.'.name' => $fieldIdentifier],
                ['BETWEEN', $fieldIdentifier.'.value', (int)$fields[$fieldFrom], (int)$fields[$fieldTo]],
            ]);
        }
        else if (isset($fields[$fieldFrom])) {
            $query->andWhere([
                'AND',
                [$fieldIdentifier.'.name' => $fieldIdentifier],
                ['>=', $fieldIdentifier.'.value', (int)$fields[$fieldFrom]],
            ]);
        }
        else if (isset($fields[$fieldTo])) {
            $query->andWhere([
                'AND',
                [$fieldIdentifier.'.name' => $fieldIdentifier],
                ['>=', $fieldIdentifier.'.value', (int)$fields[$fieldTo]],
            ]);
        }
    }

    /**
     * @param Yii\db\Query $query
     * @param string $fieldIdentifier
     * @param array $fields
     */
    private function addFieldLike(yii\db\Query $query, string $fieldIdentifier, array $fields)
    {
        $query->leftJoin(CatalogItemField::tableName().' '.$fieldIdentifier, $fieldIdentifier.'.catalog_item_id = '.self::tableName().'.id');

        $query->andWhere([
            'AND',
            [$fieldIdentifier.'.name' => $fieldIdentifier],
            ['LIKE', $fieldIdentifier.'.value', $fields[$fieldIdentifier]],
        ]);
    }
}