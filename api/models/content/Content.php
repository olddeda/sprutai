<?php
namespace api\models\content;

use Yii;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;

use common\modules\base\helpers\enum\ModuleType;

use common\modules\content\helpers\enum\Type as ContentType;
use common\modules\content\models\Content as BaseModel;

use common\modules\seo\models\Seo;

use common\modules\statistics\helpers\enum\Type as TypeStatistics;

use common\modules\vote\models\Vote;

use api\models\catalog\CatalogItem;
use api\models\company\Company;
use api\models\content\query\ContentQuery;
use api\models\favorite\Favorite;
use api\models\tag\Tag;
use api\models\user\User;
use api\traits\ImageTrait;

/**
 * Class Content
 * @package api\models\content
 */

/**
 * @OA\Schema(
 *     @OA\Property(property="id", type="integer", description="ID"),
 *     @OA\Property(property="module_type", type="integer", description="Тип модуля"),
 *     @OA\Property(property="module_id", type="integer", description="ID модуля"),
 *     @OA\Property(property="type", type="string", description="Тип", enum={"news", "article", "blog", "project", "plugin", "portfolio", "event"}),
 *     @OA\Property(property="slug", type="string", description="Идентификатор"),
 *     @OA\Property(property="title", type="string", description="Название"),
 *     @OA\Property(property="descr", type="string", description="Краткое описание"),
 *     @OA\Property(property="video_url", type="string", description="Ссылка на ролик"),
 *     @OA\Property(property="date_at", type="integer", description="Дата и время"),
 *     @OA\Property(property="tags_ids", type="array", @OA\Items(type="integer"), description="IDs тегов"),
 *     @OA\Property(property="catalog_items_ids", type="array", @OA\Items(type="integer"), description="IDs устройств"),
 *     @OA\Property(property="image", ref="#/components/schemas/Image", description="Изображение"),
 *     @OA\Property(property="owner", type="object",
 *         @OA\Property(property="id", type="integer", description="ID"),
 *         @OA\Property(property="type", type="string", description="Тип", enum={"user", "company"}),
 *         @OA\Property(property="name", type="string", description="Имя"),
 *         @OA\Property(property="username", type="string", description="Юзернейм"),
 *         @OA\Property(property="is_online", type="boolean", description="Признак онлайна"),
 *         @OA\Property(property="is_company", type="boolean", description="Признак компании"),
 *         @OA\Property(property="image", ref="#/components/schemas/Image", description="Изображение"),
 *     ),
 *     @OA\Property(property="stats", type="object",
 *         @OA\Property(property="comments", type="integer", description="Колличество комментарию"),
 *         @OA\Property(property="show", type="integer", description="Колличество просмотров"),
 *         @OA\Property(property="visit", type="integer", description="Колличество визитов"),
 *         @OA\Property(property="likes", type="integer", description="Количество лайков"),
 *         @OA\Property(property="dislikes", type="integer", description="Количество дизлайков"),
 *         @OA\Property(property="catalog_items", type="integer", description="Количество устройств в статье")
 *     ),
 *     @OA\Property(property="history", ref="#/components/schemas/ContentHistory", description="Последняя запись в истории (отдается при запросе ?expand=history)"),
 *     @OA\Property(property="text", type="string", description="Полное описание (отдается при запросе ?expand=text)"),
 *     @OA\Property(property="seo", type="object", description="SEO (отдается при запросе ?expand=seo)",
 *         @OA\Property(property="title", type="string", description="Заголовок"),
 *         @OA\Property(property="h1", type="string", description="H1"),
 *         @OA\Property(property="keywords", type="string", description="Ключевые слова"),
 *         @OA\Property(property="description", type="string", description="Описание"),
 *         @OA\Property(property="slugify", type="string", description="Идентификатор"),
 *         @OA\Property(property="url", type="string", description="Ссылка"),
 *     )
 * )
 */
class Content extends BaseModel
{
    use ImageTrait;

    /**
     * @var integer
     */
	public $count_catalog_items;

    /**
     * @var string
     */
	public $tags_title;

    /**
     * @var string
     */
	public $catalog_items_title;

	/**
	 * @param array $row
	 *
	 * @return Content
	 */
	public static function instantiate($row) {
	    $type = ArrayHelper::getValue($row, 'type', null);
		switch ($type) {
			case News::type():
				return new News();
            case Article::type():
                return new Article();
            case Blog::type():
                return new Blog();
            case Page::type():
                return new Page();
            case Video::type():
                return new Video();
            case Qa::type():
                return new Qa();
			default:
				return new self();
		}
	}

    /**
     * @return array
     */
	public function rules() {
        return ArrayHelper::merge(parent::rules(), [
            [['count_catalog_items'], 'safe'],
        ]);
    }

    /**
	 * @inheritdoc
	 *
	 * The default implementation returns the names of the columns whose values have been populated into this record.
	 */
	public function fields() {
		return [
			'id',
            'module_type',
            'module_id',
			'slug',
			'title' => function($data) {
		        return html_entity_decode($data->title);
            },
			'descr',
            'video_url',
			'type' => function($data) {
				return $data->getType();
			},
            'image' => function($data) {
                return $data->mediaImageFor('image');
            },
            'comments' => function ($data) {
                return [
                    'users' => $data->commentsUsers,
                ];
            },
			'owner',
            'stats' => function($data) {
                $like = $data->getUserValue(Vote::CONTENT_VOTE);
                $vote = $data->getVoteAggregate(Vote::CONTENT_VOTE);

                return ArrayHelper::merge($data->stats, [
                    'comments' => $data->stat ? $data->stat->comments : 0,
                    'likes' => $vote->positive ? $vote->positive : 0,
                    'dislikes' => $vote->negative ? $vote->negative : 0,
                    'catalog_items' => (int)$this->count_catalog_items,
                    'has_like' => $like === 1 ? true : false,
                    'has_dislike' => $like === 0 ? true : false,
                ]);
            },
            'status',
            'user_id' => function ($data) {
		        return $this->author_id;
            },
			'date_at',
            'favorites',
            'tags_ids',
            'catalog_items_ids',
		];
	}

	/**
	 * @inheritdoc
	 *
	 * The default implementation returns the names of the relations that have been populated into this record.
	 */
	public function extraFields() {
		return [
		    'text' => function($data) {
		        $text = $data->getTextParsed('text');
                $text = preg_replace('/(<[^>]+) style=".*?"/i', '$1', $text);
                $text = str_replace('<p></p>', '', $text);
		        return $text;
            },
            'seo' => function($data) {
                return $data->getSeoFields();
            },
            'tags' => function($data) {
                return $data->tags;
            },
            'catalog_items' => function($data) {
                return $data->catalogItems;
            },
            'history' => function ($data) {
		        return $data->history;
            },
        ];
	}

    /**
     * @inheritdoc
     * @return ContentQuery the active query used by this AR class.
     */
    public static function find() {
        $query = new ContentQuery(get_called_class(), ['type' => static::type()]);
        return $query->withData();
    }

    /**
     * @return ActiveQuery
     */
    public function getHistory() {
        return $this->hasOne(ContentHistory::class, ['content_id' => 'id'])->where([])->orderBy(['id' => SORT_DESC]);
    }
	
	/**
	 * @return ActiveQuery
	 */
	public function getAuthor() {
		return $this->hasOne(User::class, ['id' => 'author_id'])->joinWith([
		    'profile',
            'mediaAvatar' => function($query) {
                $query->alias('ma')->where([]);
            }
        ])->where([]);
	}
	
	/**
	 * @return ActiveQuery
	 */
	public function getCompany() {
		return $this->hasOne(Company::class, ['id' => 'company_id'])->where([]);
	}
    
    /**
     * @return ActiveQuery
     */
    public function getSeoRelation() {
        return $this->hasOne(Seo::class, ['module_id' => 'id'])->onCondition([
            Seo::tableName().'.module_type' => ModuleType::CONTENT
        ])->where([]);
    }

    /**
     * @return ActiveQuery
     */
    public function getFavorites() {
        return $this->hasMany(Favorite::class, ['module_id' => 'id'])->onCondition([
            Favorite::tableName().'.module_type' => ModuleType::CONTENT,
            Favorite::tableName().'.user_id' => Yii::$app->user->id,
        ]);
    }

    /**
     * @return ActiveQuery
     */
    public function getCommentsUsers() {
        return $this->hasMany(User::class, ['id' => 'created_by'])->via('comments')
            ->joinWith([
                'profile' => function($query) {},
                'mediaAvatar' => function($query) {
                    $query->alias('ma');
                },
                'contentsStat' => function($query) {
                    $query->alias('cs')->where([]);
                }
            ])
            ->where([])
            ->orderBy(['cs.subscribers' => SORT_DESC])
            ->groupBy(User::tableName().'.id')
            ->limit(10);
        ;
    }

    /**
     * @return ActiveQuery
     */
    public function getTags() {
        return $this->hasMany(Tag::class, ['id' => 'tag_id'])->via('tagModule')->where([]);
    }

    /**
     * @return ActiveQuery
     */
    public function getCatalogItems() {
        return $this->hasMany(CatalogItem::class, ['id' => 'module_id'])->with(['vendor'])->via('contentModuleCatalogItems')->where([]);
    }
	
	/**
	 * @return array
	 */
	public function getOwner() {
		$isCompany = $this->getIsCompany();
		
		$id = $isCompany ? $this->company_id : $this->author_id;
		$type = $isCompany ? 'company' : 'user';
		$name = $isCompany ? $this->company->title : $this->author->getAuthorName();
		$username = $isCompany ? '' : $this->author->username;
		$image = $isCompany ? $this->company->getMediaImage() : $this->author->getMediaImage();
		$isOnline = $isCompany ? false : $this->author->getIsOnline();
		
		return [
			'id' => $id,
            'type' => $type,
			'name' => $name,
            'username' => $username,
			'image' => $image,
            'is_online' => $isOnline,
			'is_company' => $isCompany
		];
	}

    /**
     * @return mixed
     */
    public function getType() {
        return str_replace('type_', '', ContentType::getItem($this->type));
    }

    /**
     * @return string
     */
	public function getSlug() {
		return $this->seoRelation ? $this->seoRelation->slugify : '';
	}
	
	/**
	 * @return array|null
	 */
	public function getMediaImage() {
		$image = $this->image;
		if ($image) {
			$imageInfo = $image->getImageInfo(true);
			return [
				'path' => $imageInfo['http'],
				'file' => $imageInfo['file'],
			];
		}
		return null;
	}

    /**
     * @return array
     * @throws \yii\db\Exception
     */
	public function getStats() {
		return [
			'comments' => $this->stat ? $this->stat->comments : 0,
			'show' => $this->getStatisticsVal(TypeStatistics::SHOW),
			'visit' => $this->getStatisticsVal(TypeStatistics::VISIT),
			'outgoing' => $this->getStatisticsVal(TypeStatistics::OUTGOING),
		];
	}

    /**
     * @return array
     */
	public function getSeoFields() {
	    $seo = $this->seoRelation;
	    return [
	        'title' => $seo ? $seo->title ?: $this->title : '',
            'h1' => $seo && $seo->h1 ? $seo->h1 : '',
            'keywords' => $seo ? $seo->keywords : '',
            'description' => $seo ? $seo->description : '',
            'slugify' => $seo ? $seo->slugify : '',
            'url' => $seo ? str_replace('type_', '', ContentType::getItem($this->type)).'/'.$seo->slugify : ''
        ];
    }

    /**
     * @param bool $insert
     *
     * @return bool
     * @throws \yii\base\InvalidConfigException
     */
    public function beforeSave($insert) {
        if (!ArrayHelper::getValue(Yii::$app->request->post(), 'tags_ids')) {
            $this->setTags_ids([]);
        }

        if (!ArrayHelper::getValue(Yii::$app->request->post(), 'catalog_items_ids')) {
            $this->catalog_items_ids = [];
        }

        return parent::beforeSave($insert);
    }
}