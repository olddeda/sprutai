<?php
namespace api\models\content\search;

use api\models\catalog\CatalogItem;
use api\models\content\Content;
use api\models\favorite\Favorite;
use common\modules\base\components\ActiveQuery;
use common\modules\content\helpers\enum\Status;
use common\modules\content\helpers\enum\Type as ContentType;
use common\modules\content\models\ContentStat;
use common\modules\rbac\helpers\enum\Role;
use common\modules\tag\models\Tag;
use common\modules\user\models\User;
use common\modules\vote\models\Vote;
use common\modules\vote\Module as VoteModule;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

/**
 * ContentSearch represents the model behind the search form about `app\models\content\Content`.
 */
class ContentSearch extends Content
{
    /** @var bool */
    public $isModeAdmin = false;

	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[['id', 'status', 'created_at', 'updated_at'], 'integer'],
            [['title', 'video_url', 'tags_title', 'catalog_items_title'], 'string'],
            [['type', 'sort', 'date_at', 'count_catalog_items'], 'safe'],
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
     * @throws InvalidConfigException
     */
	public function search($params) {

	    /** @var VoteModule $voteModule */
	    $voteModule = Yii::$app->getModule('vote');

	    /** @var ActiveQuery $query */
		$query = Content::find()
            ->with([
                'media',
                'statistics',
                'seoRelation',
                'commentsUsers' => function($query) {
                    $query->with([
                        'profile',
                        'telegram'
                    ]);
                },
            ])
            ->joinWith([
                'company',
                'author' => function($query) {
                    $query->with([
                        'profile',
                        'telegram'
                    ]);
                },
                'stat',
                'favorites',
            ])
            ->where([
                '<>', self::tableName().'.status', Status::DELETED
            ])
            ->votes()
        ;

        $groupByDisabled = true;

        $orderBy = [];
		
		$pinned = ArrayHelper::getValue($params, 'pinned', false);
		if ($pinned) {
            $orderBy[self::tableName().'.pinned'] = SORT_DESC;
        }
		
        $sort = Yii::$app->request->get('sort');
        if ($sort == 'popular') {
            //$orderBy['contentVoteAggregate.positive'] = SORT_DESC;
            $orderBy[self::tableName().'.date_at'] = SORT_DESC;
        }
        else if ($sort == 'discussed') {
            $query->andWhere('comments > 0');
            $orderBy[ContentStat::tableName().'.comments'] = SORT_DESC;
        }
        else if ($sort == 'subscribed') {
            $tagsIds = ArrayHelper::getColumn(Tag::find()->votes()->voted(Vote::TAG_FAVORITE)->asArray()->all(), 'id');
            $authorIds = ArrayHelper::getColumn(User::find()->votes()->voted(Vote::USER_FAVORITE)->asArray()->all(), 'id');
            $query->andWhere([
                'or',
                ['in', Tag::tableName().'.id', $tagsIds],
                ['in', Content::tableName().'.author_id', $authorIds],
            ]);
            $orderBy[self::tableName().'.date_at'] = SORT_DESC;
        }
        elseif ($sort == 'favorite') {
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
        if (count($orderBy)) {
            $query->orderBy($orderBy);
        }

        $sortParams = [
            'params' => Yii::$app->request->getQueryParams(),
            'attributes' => [
                '*',
                'id',
                'title',
                'status',
                'date_at',
                'tags_title' => [
                    'asc' => [Tag::tableName().'.title' => SORT_ASC],
                    'desc' => [Tag::tableName().'.title' => SORT_DESC],
                ],
                'catalog_items_title' => [
                    'asc' => [CatalogItem::tableName().'.title' => SORT_ASC],
                    'desc' => [CatalogItem::tableName().'.title' => SORT_DESC],
                ],
            ],
            'defaultOrder' => [
                'date_at' => SORT_DESC
            ]
        ];

        $this->load(ArrayHelper::getValue($params, 'filter', []), '');
		
		// Create data provider
		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'pagination' => [
				'defaultPageSize' => 10,
			],
            'sort' => (!count($orderBy)) ? $sortParams : [],
		]);

		if (!$this->validate()) {
			return $dataProvider;
		}

        if (in_array($sort, [
            'tags_title',
            '-tags_title',
        ]) || $this->tags_title) {
            $query->joinWith('tags');
            $groupByDisabled = false;
        }

        if (in_array($sort, [
            'catalog_items_title',
            '-catalog_items_title'
        ]) || $this->catalog_items_title) {
            $query->joinWith('catalogItems');
            $groupByDisabled = false;
        }

        if (!$this->isModeAdmin && !$this->status && !Yii::$app->user->hasRole([Role::SUPERADMIN, Role::ADMIN, Role::EDITOR])) {
            $this->status = Status::ENABLED;
        }

        if ($this->type) {
            if (is_array($this->type)) {
                $query->andWhere(['in', self::tableName().'.type', $this->type]);
            }
            else {
                $query->andFilterWhere([
                    self::tableName().'.type' => $this->type,
                ]);
            }
        }
        else {
            $query->andWhere(['in', self::tableName().'.type', [
                ContentType::NEWS,
                ContentType::ARTICLE,
                ContentType::BLOG,
                ContentType::VIDEO,
                //ContentType::PLUGIN,
                //ContentType::PROJECT,
                //ContentType::PORTFOLIO,
                //ContentType::EVENT,
            ]]);
        }

		$query->andFilterWhere([
            self::tableName().'.id' => $this->id,
            self::tableName().'.status' => $this->status,
		]);

        $query->andFilterWhere(['like', self::tableName().'.title', $this->title]);
        $query->andFilterWhere(['like', self::tableName().'.video_url', $this->video_url]);

        if ($this->tags_title) {
            $query->andFilterWhere(['like', Tag::tableName().'.title', $this->tags_title]);
        }

        if ($this->catalog_items_title) {
            $query->andFilterWhere(['like', CatalogItem::tableName().'.title', $this->catalog_items_title]);
        }

        if (is_array($this->date_at) && (isset($this->date_at['start']) || isset($this->date_at['end'])) && $this->date_at['start'] && $this->date_at['end']) {
            $start = $this->date_at['start'];
            $end = $this->date_at['end'];
            $query->andFilterWhere([
                'between',
               'FROM_UNIXTIME('.self::tableName().'.date_at, "%d-%m-%Y")',
                $start,
                $end,
            ]);
        }
        else if ($this->date_at && is_scalar($this->date_at)) {
            $query->andFilterWhere([
                'FROM_UNIXTIME('.self::tableName().'.date_at, "%d-%m-%Y")' => $this->date_at,
            ]);
        }

        if ($groupByDisabled) {
            $dataProvider->query->groupBy = null;
        }

		return $dataProvider;
	}
}