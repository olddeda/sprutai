<?php
namespace common\modules\content\models;

use common\modules\base\behaviors\PurifyBehavior;
use common\modules\base\behaviors\tree\adjacency\AdjacencyListBehavior;
use common\modules\base\behaviors\tree\adjacency\AdjacencyTrait;
use common\modules\base\components\ActiveRecord;
use common\modules\base\components\Debug;
use common\modules\base\components\Helper;
use common\modules\base\extensions\yandexturbo\YandexTurboBehavior;
use common\modules\base\helpers\enum\ModuleType;
use common\modules\base\helpers\purifier\HTMLPurifier_URIScheme_tg;
use common\modules\catalog\models\CatalogItem;
use common\modules\catalog\models\CatalogItemStat;
use common\modules\catalog\models\query\CatalogItemQuery;
use common\modules\comments\models\Comment;
use common\modules\comments\models\query\CommentQuery;
use common\modules\company\models\Company;
use common\modules\content\helpers\enum\Status;
use common\modules\content\helpers\enum\Type;
use common\modules\content\models\query\ContentModuleQuery;
use common\modules\content\models\query\ContentQuery;
use common\modules\media\behaviors\MediaBehavior;
use common\modules\media\helpers\enum\Mode;
use common\modules\media\helpers\enum\Type as MediaType;
use common\modules\payment\models\PaymentType;
use common\modules\payment\models\PaymentTypeModule;
use common\modules\payment\models\query\PaymentTypeModuleQuery;
use common\modules\payment\models\query\PaymentTypeQuery;
use common\modules\plugin\models\Plugin;
use common\modules\project\models\Project;
use common\modules\rbac\helpers\enum\Role;
use common\modules\seo\behaviors\SeoFields;
use common\modules\social\models\SocialItem;
use common\modules\tag\models\query\TagModuleQuery;
use common\modules\tag\models\query\TagQuery;
use common\modules\tag\models\Tag;
use common\modules\tag\models\TagModule;
use common\modules\tag\models\Tags;
use common\modules\user\models\User;
use common\modules\user\models\UserProfile;
use common\modules\vote\behaviors\VoteBehavior;
use common\modules\vote\models\Vote;
use common\modules\vote\models\VoteAggregate;
use Yii;
use yii\base\InvalidConfigException;
use yii\caching\DbDependency;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;
use yii\helpers\StringHelper;
use yii\helpers\Url;
use yii\web\Application;
use yii\web\NotFoundHttpException;

/**
 * This is the model class for table "{{%content}}".
 *
 * @property integer $id
 * @property integer $module_type
 * @property integer $module_id
 * @property integer $type
 * @property integer $company_id
 * @property integer $content_id
 * @property integer $author_id
 * @property string $title
 * @property string $descr
 * @property string $text
 * @property string $text_new
 * @property string $content
 * @property string $layout
 * @property string source_name
 * @property string source_url
 * @property string video_url
 * @property integer $is_main
 * @property integer $page_type
 * @property integer $page_path
 * @property boolean $pinned
 * @property integer $pinned_sequence
 * @property integer $notification
 * @property boolean $change_catalog_links
 * @property integer $status
 * @property integer $created_by
 * @property integer $updated_by
 * @property integer $date_at
 * @property integer $published_at
 * @property integer $created_at
 * @property integer $updated_at
 *
 * Defined relations:
 * @property Content $parent
 * @property ContentStat $stat
 * @property ContentOwner $owner
 * @property ContentUnique $unique
 * @property Company $company
 * @property User $createdBy
 * @property User $updatedBy
 * @property User $author
 * @property Tags[] $tags
 * @property MediaBehavior $image
 * @property PaymentType $paymentType
 * @property PaymentType[] $paymentTypes
 * @property Comment[] $comments
 * @property SocialItem $socialItem
 * @property CatalogItem[] $catalogItems
 */
class Content extends ActiveRecord
{
	use AdjacencyTrait;

    public $is_backup = false;
	
	/**
	 * @var array
	 */
	private $_tags_ids;
	
	/**
	 * @var array
	 */
	private $_tags_ids_old;

    /**
     * @var array
     */
    public $catalog_items_ids = [];

    /**
	 * @var array
	 */
    private $_payment_types_ids;

    /**
	 * @var array
	 */
    private $_payment_types_ids_old;
	
	/**
	 * @var array
	 */
	static private $_users;
	
	/**
	 * @var array
	 */
	static private $_treeData;
	
	/**
	 * @var array
	 */
	public $validate_author_fields;
	
	/**
	 * @param array $row
	 *
	 * @return Content
	 */
	public static function instantiate($row) {
		switch ($row['type']) {
			case Page::type():
				return new Page();
			case News::type():
				return new News();
			case Article::type():
				return new Article();
			case Project::type():
				return new Project();
			case Plugin::type():
				return new Plugin();
			case Blog::type():
				return new Blog();
			case Instruction::type():
				return new Instruction();
			case Question::type():
				return new Question();
			case Portfolio::type():
				return new Portfolio();
			case Event::type():
				return new Event();
			case Shortcut::type():
				return new Shortcut();
			default:
				return new self;
		}
	}

	/**
	 * Initializes the object.
	 */
	public function init() {
		$this->type = static::type();
		parent::init();
	}

	/**
	 * @inheritdoc
	 */
	public static function tableName() {
		return '{{%content}}';
	}

	/**
	 * Returns a list of behaviors that this component should behave as.
	 *
	 * @return array the behavior configurations.
	 */
	public function behaviors() {
		return ArrayHelper::merge(parent::behaviors(), [
			[
				'class' => MediaBehavior::class,
				'attribute' => 'image',
				'type' => MediaType::IMAGE,
			],
			[
				'class' => AdjacencyListBehavior::class,
				'parentAttribute' => 'content_id',
				'sortable' => false,
			],
			[
				'class' => SeoFields::class,
			],
			'vote' => [
				'class' => VoteBehavior::class,
			],
			'purifier' => [
				'class' => PurifyBehavior::class,
				'attributes' => ['text'],
				'config' => function ($config) {
                    
                    \HTMLPurifier_URISchemeRegistry::instance()->register('tg', new HTMLPurifier_URIScheme_tg);
		        
                    $config->set('URI.AllowedSchemes', [
                        'http' => true,
                        'https' => true,
                        'mailto' => true,
                        'ftp' => true,
                        'nntp' => true,
                        'news' => true,
                        'tel' => true,
                        'tg' => true,
                        '*' => true,
                    ]);
                    
					$def = $config->getHTMLDefinition(true);
					$def->addAttribute('iframe', 'width', 'Text');
					$def->addElement('iframe', 'Inline', 'Inline', 'Common');
					$def->addAttribute('iframe', 'width', 'Text');
					$def->addAttribute('iframe', 'height', 'Text');
					$def->addAttribute('iframe', 'src', 'Text');
					$def->addAttribute('iframe', 'frameborder', 'Text');
					$def->addAttribute('iframe', 'allowfullscreen', 'Text');
					$def->addAttribute('a', 'target', 'Text');
                    $def->addAttribute('a', 'src', 'Text');
					$def->addElement('video', 'Inline', 'Inline', 'Common');
					$def->addAttribute('video', 'src', 'Text');
					$def->addAttribute('video', 'controls', 'Text');
					$def->addAttribute('video', 'playsinline', 'Text');
					$def->addAttribute('video', 'muted', 'Text');
					$def->addAttribute('video', 'preload', 'Text');
					$def->addAttribute('video', 'autoplay', 'Text');
					$def->addAttribute('video', 'width', 'Text');
					$def->addAttribute('video', 'height', 'Text');
					$def->addAttribute('video', 'loop', 'Text');
					$def->addElement('source', 'Inline', 'Inline', 'Common');
					$def->addAttribute('source', 'src', 'Text');
					$def->addAttribute('source', 'type', 'Text');
					
					
				}
			],
			[
				'class' => YandexTurboBehavior::class,
				'scope' => function (\yii\db\ActiveQuery $query) {
					$query->andWhere(['status' => Status::ENABLED]);
					$query->orderBy(['created_at' => SORT_DESC]);
				},
				'dataClosure' => function (self $model) {
					return [
						'title' => $model->title,
						'link' => $model->getUrl(true),
						'description' => $model->descr,
						'content' => $model->getContent(true),
						'pubDate' => (new \DateTime($this->created_at))->format(\DateTime::RFC822),
					];
				}
			],
		]);
	}

	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[['title', 'text', 'status'], 'required', 'on' => self::SCENARIO_DEFAULT],
            [['type', 'status'], 'required', 'on' => self::SCENARIO_TEMP],
			[['module_type', 'module_id', 'type', 'company_id', 'content_id', 'author_id', 'type', 'is_main', 'page_type', 'pinned_sequence', 'status', 'date_at', 'published_at', 'created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
			[['title', 'layout', 'page_path', 'source_name', 'source_url', 'video_url'], 'string', 'max' => 255],
			[['descr'], 'string', 'max' => 10000],
			[['text', 'text_new'], 'string', 'max' => 1000000],
			[['source_url', 'video_url'], 'url', 'defaultScheme' => 'http', 'skipOnEmpty' => true],
			[['pinned', 'notification', 'change_catalog_links'], 'boolean'],
			[['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id']],
			[['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['updated_by' => 'id']],
			[['tags_ids', 'catalog_items_ids', 'payment_types_ids', 'date', 'datetime', 'rating'], 'safe'],
			[['title', 'descr', 'source_name', 'source_url'], 'filter', 'filter' => '\yii\helpers\HtmlPurifier::process'],
            [['files'], 'safe'],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels() {
		return [
			'id' => Yii::t('content', 'field_id'),
			'type' => Yii::t('content', 'field_type'),
			'company_id' => Yii::t('content', 'field_company_id'),
			'content_id' => Yii::t('content', 'field_content_id'),
			'author_id' => Yii::t('content', 'field_author_id'),
			'title' => Yii::t('content', 'field_title'),
			'category' => Yii::t('content', 'field_category'),
			'tag' => Yii::t('content', 'field_tag'),
			'descr' => Yii::t('content', 'field_descr'),
			'text' => Yii::t('content', 'field_text'),
			'date' => Yii::t('content', 'field_date'),
			'datetime' => Yii::t('content', 'field_datetime'),
			'layout' => Yii::t('content', 'field_layout'),
			'is_main' => Yii::t('content', 'field_is_main'),
			'page_type' => Yii::t('content', 'field_page_type'),
			'page_path' => Yii::t('content', 'field_page_path'),
			'source_name' => Yii::t('content', 'field_source_name'),
			'source_url' => Yii::t('content', 'field_source_url'),
			'pinned' => Yii::t('content', 'field_pinned'),
			'pinned_sequence' => Yii::t('content', 'field_pinned_sequence'),
			'video_url' => Yii::t('content', 'field_video_url'),
			'tags' => Yii::t('content', 'field_tags'),
			'tags_ids' => Yii::t('content', 'field_tags_ids'),
            'payment_types_ids' => Yii::t('content', 'field_payment_types_ids'),
			'rating '=> Yii::t('content', 'field_rating'),
			'image' => Yii::t('content', 'field_image'),
			'video' => Yii::t('content', 'field_video'),
			'status' => Yii::t('content', 'field_status'),
			'created_by' => Yii::t('content', 'field_created_by'),
			'updated_by' => Yii::t('content', 'field_updated_by'),
			'date_at' => Yii::t('content', 'field_date_at'),
			'created_at' => Yii::t('content', 'field_created_at'),
			'updated_at' => Yii::t('content', 'field_updated_at'),
			'published_at' => Yii::t('content', 'field_published_at'),
			'author_type' => Yii::t('content', 'field_author_type'),
			'notification' => Yii::t('content', 'field_notification'),
            'change_catalog_links' => Yii::t('content', 'field_change_catalog_links'),
		];
	}

	/**
	 * Get module type
	 * @return int
	 */
	public static function moduleType() {
		return ModuleType::CONTENT;
	}

	/**
	 * Get model type
	 * @return int
	 */
	static public function type() {
		return Type::NONE;
	}
	
	/**
	 * @param $query
	 */
	public static function prepareQuery($query) {
		if (Yii::$app instanceof Application)
			$query->votes();
	}

	/**
	 * @return array
	 */
	public function transactions() {
		return [
			self::SCENARIO_DEFAULT => self::OP_INSERT | self::OP_UPDATE,
		];
	}
	
	/**
	 * @inheritdoc
	 * @return ContentQuery the active query used by this AR class.
	 */
	public static function find() {
		return new ContentQuery(get_called_class(), ['type' => static::type()]);
	}
	
	/**
	 * Find model by hash
	 * @param string $hash
	 *
	 * @return array|Media|null
	 */
	public static function findByHash($hash, $allowRoles = []) {
		
		// Create query
		$query = self::find()->where('MD5(CONCAT(id, created_at)) = :hash', [
			':hash' => $hash
		]);
		
		// Add owner condition
		if (!Yii::$app->user->isSuperAdmin && !Yii::$app->user->isAdmin && !Yii::$app->user->isEditor && !Yii::$app->user->hasRole($allowRoles)) {
			$query->andWhere('created_by = :created_by', [
				':created_by' => Yii::$app->user->id,
			]);
		}
		
		return $query->one();
	}

    /**
     * Find one by column
     *
     * @param $column
     * @param $value
     * @param bool $except
     * @param string $messageCategory
     * @param array $relations
     * @param bool $cache
     * @param bool $own
     * @param null $conditions
     * @param array $skipFields
     *
     * @param null $callback
     *
     * @return mixed|null
     * @throws NotFoundHttpException
     * @throws \Throwable
     */
	static public function findByColumn($column, $value, $except = false, $messageCategory = 'base', $relations = [], $cache = false, $own = false, $conditions = null, $skipFields = [], $callback = null) {
		$class = get_called_class();
		$model = new $class;
		$query = $class::find();
		
		self::prepareQuery($query);
		
		$query->andWhere($class::tableName().'.'.$column.' = :'.$column, [
			':'.$column => $value,
		]);
		
		
		if (is_array($relations) && count($relations))
			$query->joinWith($relations);
		
		// Add owner user condition
		if ($own) {
			if (!Yii::$app->user->isSuperAdmin && !Yii::$app->user->isAdmin && !Yii::$app->user->isEditor) {
				$userColumn = (in_array('user_id', $model->attributes())) ? 'user_id' : 'created_by';
				if (!in_array($userColumn, $skipFields)) {
					$query->andWhere($class::tableName().'.'.$userColumn.' = :'.$userColumn, [
						':'.$userColumn => Yii::$app->user->id,
					]);
				}
			}
		}
		
		if (!is_null($conditions)) {
			$query->andWhere($conditions);
		}
		
		$model = null;
		if ($cache) {
			$dependency = new DbDependency();
			$dependency->sql = 'SELECT MAX(updated_at) FROM '.$class::tableName();
			$model = self::getDb()->cache(function ($db) use($query) {
				return $query->one();
			}, Yii::$app->params['cache.duration'], $dependency);
		}
		else
			$model = $query->one();
		
		if ($model === null && $except)
			throw new NotFoundHttpException(Yii::t($messageCategory, 'error_not_exists'));
		
		return $model;
	}
	
	/**
	 * List articles by tag
	 * @param $tag
	 * @param int $limit
	 *
	 * @return array|Article[]|\yii\db\ActiveRecord[]
	 */
	public static function listByTag($tag, $limit = 10) {
		$query = self::find();
		$query->joinWith(['tags']);
		$query->andWhere(Tag::tableName().'.title = :tag', [
			':tag' => $tag,
		]);
		$query->limit($limit);
		$query->orderBy([self::tableName().'.date_at' => SORT_DESC]);
		
		return $query->all();
	}
	
	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getParent() {
		return $this->hasOne(Content::class, ['id' => 'content_id']);
	}
	
	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getStat() {
		return $this->hasOne(ContentStat::class, ['content_id' => 'id']);
	}
	
	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getAuthorStat() {
		return $this->hasOne(ContentAuthorStat::class, ['author_id' => 'author_id']);
	}
	
	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getCompanyStat() {
		return $this->hasOne(ContentCompanyStat::class, ['company_id' => 'company_id']);
	}
	
	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getUnique() {
		return $this->hasOne(ContentUnique::class, ['content_id' => 'id'])->orderBy([
			ContentUnique::tableName().'.updated_at' => SORT_DESC,
		]);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getCreatedBy() {
		return $this->hasOne(User::class, ['id' => 'created_by']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getUpdatedBy() {
		return $this->hasOne(User::class, ['id' => 'updated_by']);
	}
	
	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getAuthor() {
		return $this->hasOne(User::class, ['id' => 'author_id']);
	}
	
	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getCompany() {
		return $this->hasOne(Company::class, ['id' => 'company_id'])->where([])->votes();
	}
	
	/**
	 * @return TagQuery
	 */
	public function getTags() {
		return $this->hasMany(Tag::class, ['id' => 'tag_id'])->via('tagModule')->where([]);
	}
	
	/**
	 * @return TagModuleQuery
	 */
	public function getTagModule() {
		return $this->hasMany(TagModule::class, ['module_id' => 'id'])->onCondition([
			TagModule::tableName().'.module_type' => self::moduleType()
		])->where([]);
	}

    /**
     * @return PaymentTypeQuery
     */
    public function getPaymentTypes() {
        return $this->hasMany(PaymentType::class, ['id' => 'payment_type_id'])->via('paymentTypeModule')->where([]);
    }
	
	/**
	 * @return PaymentTypeQuery
	 */
	public function getPaymentType() {
		return $this->hasOne(PaymentType::class, ['id' => 'payment_type_id'])->via('paymentTypeModule')->where([]);
	}

    /**
     * @return PaymentTypeModuleQuery
     */
    public function getPaymentTypeModule() {
        return $this->hasMany(PaymentTypeModule::class, ['module_id' => 'id'])->onCondition([
        	PaymentTypeModule::tableName().'.module_type' => self::moduleType(),
        ])->where([]);
    }
	
	/**
	 * @return CommentQuery
	 */
	public function getComments() {
		return $this->hasMany(Comment::class, ['entity_id' => 'id'])->alias('comments')->onCondition([
			'comments.module_type' => ModuleType::CONTENT,
			'comments.status' => Status::ENABLED,
		])->where([]);
	}

    /**
     * @return \yii\db\ActiveQuery
     */
	public function getCommentsUsers() {
	    return $this->hasOne(User::class, ['id' => 'created_by'])->via('comments')->where([])->groupBy('id');
    }
	
	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getSocialItem() {
		return $this->hasOne(SocialItem::class, ['module_id' => 'id'])->onCondition([
			SocialItem::tableName().'.module_type' => ModuleType::CONTENT,
		]);
	}

    /**
     * @return ContentModuleQuery
     */
    public function getContentModule() {
        return $this->hasMany(ContentModule::class, ['content_id' => 'id'])->where([]);
    }

    /**
     * @return ContentModuleQuery
     */
    public function getContentModuleCatalogItems() {
        return $this->hasMany(ContentModule::class, ['content_id' => 'id'])->onCondition([
            ContentModule::tableName().'.module_type' => ModuleType::CATALOG_ITEM,
        ])->where([]);
    }

    /**
     * @return CatalogItemQuery
     */
    public function getCatalogItems() {
        return $this->hasMany(CatalogItem::class, ['id' => 'module_id'])->with(['vendor'])->via('contentModuleCatalogItems')->where([]);
    }
	
	/**
	 * @return null|string
	 * @throws \ReflectionException
	 */
	public function getUriModuleName() {
		return 'content/'.Inflector::camel2id($this->getModuleClass());
	}
	
	/**
	 * @return string
	 */
	public function getAuthorName() {
		return $this->author->getAuthorName();
	}

    /**
     * @return ContentOwner
     */
	public function getOwner() {
		$isCompany = $this->getIsCompany();
		
		$contentType = str_replace('type_', '', Type::getItem($this->type));
		if ($isCompany && $contentType != 'news')
			$contentType .= 's';
		
		$title = ($this->getIsCompany()) ? $this->company->title : $this->author->getAuthorName();
		
		$url = Url::to(['/user/content/'.$contentType, 'id' => $this->author_id], true);
		if ($isCompany) {
			$url = Url::to(['/'.$this->company->getUriModuleName().'/'.$contentType, 'id' => $this->company_id], true);
			if ($contentType == 'portfolios')
				$url = Url::to(['/companies/portfolio/index', 'company_id' => $this->company_id], true);
		}
		
		$type = $isCompany ? Yii::t('content', 'author_type_company') : Yii::t('content', 'author_type_user');
		
		$res = new ContentOwner();
		$res->id = $isCompany ? $this->company_id : $this->author_id;
		$res->title = $title;
		$res->url = $url;
		$res->type = $type;
		$res->isCompany = $isCompany;
		
		return $res;
	}
	
	/**
	 * @return bool
	 */
	public function getIsOwn() {
		if (Yii::$app->user->getIsAdmin() || Yii::$app->user->getIsEditor())
			return true;
		return $this->author_id == Yii::$app->user->id;
	}
	
	/**
	 * Get title with type
	 * @return string
	 */
	public function getTitleType() {
		return Type::getLabel($this->type).': '.$this->title;
	}
	
	/**
	 * Get date formatted
     * @param string $format
     * @return string
     * @throws InvalidConfigException
     */
	public function getDate($format = 'dd-MM-yyyy') {
		if (!$this->date_at)
			$this->date_at = time();
		return Yii::$app->formatter->asDate($this->date_at, $format);
	}
	
	/**
	 * Set date
	 * @param $val
	 */
	public function setDate($val) {
		$val .= ' '.date('H:i:s');
		$this->date_at = strtotime($val);
	}
	
	/**
	 * Get date formatted
	 * @param string $format
	 * @return string
	 * @throws InvalidConfigException
	 */
	public function getDatetime($format = 'dd-MM-yyyy HH:mm') {
		if (!$this->date_at)
			$this->date_at = time();
		return Yii::$app->formatter->asDate($this->date_at, $format);
	}
	
	/**
	 * @return string
	 * @throws InvalidConfigException
	 */
	public function getDateTimeHuman() {
		return $this->getDatetime('dd MMMM yyyy, HH:mm');
	}
	
	/**
	 * Set date
	 * @param $val
	 */
	public function setDatetime($val) {
		$val .= ':00';
		$this->date_at = strtotime($val);
	}
	
	/**
	 * Get text without tags
	 * @return string
	 */
	public function getTextClear() {
		$text = $this->text;
		$text = preg_replace('#<[^>]+>#', ' ', $text);
		$text = strip_tags($text);
		$text = str_replace("  ", " ", $text);
		$text = str_replace(" .", ".", $text);
		$text = str_replace(" ,", ",", $text);
		$text = str_replace(" )", ")", $text);
		$text = str_replace("\t", " ", $text);
		$text = str_replace("\n", " ", $text);
		$text = str_replace("\r", " ", $text);
		$text = preg_replace('/\s\s+/', ' ', $text);
		$text = trim($text);
		return HtmlPurifier::process($text);
	}

    /**
     * @return string
     */
	public function getText_current() {
	    return (!is_null($this->text_new)) ? $this->text_new : $this->text;
    }
	
	/**
	 * Get last sequence
	 *
	 * @param null $condition
	 * @param null $joinWith
	 *
	 * @return int
	 * @throws InvalidConfigException
	 */
	static public function lastSequence($condition = null, $joinWith = null) {
		$class = get_called_class();
		return parent::lastSequence(['type' => $class::type()]);
	}
	
	/**
	 * @param bool $withEmpty
	 *
	 * @return array
	 */
	static public function treeListData($withEmpty = true) {
		if (!self::$_treeData)
			self::$_treeData = self::tree();
		
		$tmp = [];
		if ($withEmpty)
			$tmp[0] = Yii::t('content', 'content_parent_none');
		
		return ArrayHelper::merge($tmp, self::$_treeData);
	}
	
	/**
	 * @return array
	 */
	public function getTagsListData() {
		return ArrayHelper::map($this->tags, 'id', 'title');
	}
	
	/**
	 * Get tags
	 * @return array
	 */
	public function getTagsData() {
		$data = [];
		$tmp = $this->getTagsListData();
		if (is_array($tmp)) {
			foreach ($tmp as $id => $title) {
				$data[] = [
					'id' => $id,
					'title' => $title,
				];
			}
		}
		return $data;
	}
	
	/**
	 * Get tags values
	 * @param bool $asArray
	 * @param string $glue
	 *
	 * @return array|null|string
	 */
	public function getTagsValues($asArray = false, $glue = ', ') {
		$tmp = $this->tags;
		if (is_array($tmp)) {
			$values = ArrayHelper::getColumn($tmp, 'title');
			return ($asArray) ? $values : implode($glue, $values);
		}
		return ($asArray) ? [] : null;
	}
	
	/**
	 * Get tags ids
	 * @return array
	 */
	public function getTags_ids() {
		if (is_null($this->_tags_ids)) {
			$this->_tags_ids = [];
			$tags = $this->tags;
			if ($tags) {
				foreach ($tags as $item)
					$this->_tags_ids[] = $item->id;
			}
		}
		return $this->_tags_ids;
	}
	
	/**
	 * Set tags ids
	 * @param $val
	 */
	public function setTags_ids($val) {
		$this->_tags_ids = (is_null($val)) ? [] : $val;
	}

    /**
     * Get payment types ids
     * @return array
     */
    public function getPayment_types_ids() {
        if (is_null($this->_payment_types_ids)) {
            $this->_payment_types_ids = [];
            $paymentTypes = $this->paymentTypes;
            if ($paymentTypes) {
                foreach ($paymentTypes as $item)
                    $this->_payment_types_ids[] = $item->id;
            }
        }
        return $this->_payment_types_ids;
    }

    /**
     * Set payment types ids
     * @param $val
     */
    public function setPayment_types_ids($val) {
        $this->_payment_types_ids = (is_null($val)) ? [] : $val;
    }
	
	/**
	 * @param bool $forRss
	 *
	 * @return null|string
	 */
    public function getContent($forRss = false) {
    	$text = $this->text;
    	if ($forRss) {
			$text = html_entity_decode($text);
			$text = Helper::removeAttribute($text, 'id');
		}
    	return $text;
	}

	/**
	 * @return string
	 */
	public function getTypeName() {
 		return str_replace('type_', '', Type::getItem($this->type));
	}
	
	/**
	 * Check is published social
	 * @return bool
	 */
	public function getIsPublished() {
 		return ($this->socialItem) ? !is_null($this->socialItem->post_telegram_at) : false;
	}
	
	/**
	 * @return bool
	 */
	public function getIsCompany() {
		return ($this->company_id) ? true : false;
	}

    /**
     * @return string|string[]|null
     */
    public function getText_with_links() {
        return $this->text;
        $url = '/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/';
        return preg_replace($url, '<a href="$0" target="_blank" title="$0">$0</a>', $this->text);
    }

    /**
     * @param $field
     *
     * @return string
     */
    public function getTextParsed($field) {
        $text = $this->getAttribute($field);
        $text = urldecode($text);

        $replace = $this->getContentLinks($field);

        if (count($replace)) {
            foreach ($replace as $r) {
                $url = '';
                if ($r['type'] == 'content') {
                    $item = Content::find()->where(['id' => $r['id']])->one();
                    if (!is_null($item)) {
                        $url = $item->getUrl(true);
                    }
                }
                if ($r['type'] == 'company') {
                    $item = Company::find()->where(['id' => $r['id']])->one();
                    if (!is_null($item)) {
                        $url = $item->getUrl(true);
                    }
                }
                if ($r['type'] == 'catalogitem') {
                    $item = CatalogItem::find()->where(['id' => $r['id']])->one();
                    if (!is_null($item)) {
                        if (!$this->change_catalog_links && is_array($item->data) && isset($item->data['shops']) && count($item->data['shops'])) {
                            $shops = $item->data['shops'];
                            $idx = array_search(293, array_column($shops, 'shop_id'));
                            if ($idx !== false) {
                                $shop = $shops[$idx];
                                $url = $shop['short_url'];
                            }
                            else {
                                $shop = $shops[array_rand($shops, 1)];
                                $url = $shop['short_url'];
                            }

                        }
                        else {
                            $url = 'http://v2.sprut.ai/catalog/item/' . $item->seo->slugify;
                        }
                    }
                }
                $text = str_replace($r['url'], $url, $text);
            }
        }

        return $text;
    }

    /**
     * @param string $field
     *
     * @return array
     */
    public function getContentLinks($field = 'text') {
        $text = $this->getAttribute($field);
        $text = urldecode($text);
        preg_match_all('/\[([a-z]+):(\d+)+\]+/m', $text, $search);

        $tmp = [];
        if (count($search)) {
            foreach ($search[0] as $key => $url) {
                $id = (int)$search[2][$key];
                $type = $search[1][$key];
                $moduleType = null;
                if ($type == 'content') {
                    $moduleType = ModuleType::CONTENT;
                }
                else if ($type == 'company') {
                    $moduleType = ModuleType::COMPANY;
                }
                else if ($type == 'catalogitem') {
                    $moduleType = ModuleType::CATALOG_ITEM;
                }

                if ($moduleType) {
                    $tmp[] = [
                        'id' => $id,
                        'module_type' => $moduleType,
                        'type' => $type,
                        'url' => $url,
                    ];
                }
            }
        }
        return $tmp;
    }

	/**
	 * Check and return author empty fields
	 * @return array|boolean
	 */
	public function checkAuthorCompleteFields() {
		$err = [];
		if (!$this->getIsCompany() && $this->author) {
			if (!$this->author->avatar->mediaImage || $this->author->avatar->mediaImage->status != 1)
				$err[] = 'avatar';
			if (strlen($this->author->profile->last_name) < 4)
				$err[] = 'last_name';
			if (strlen($this->author->profile->first_name) < 4)
				$err[] = 'first_name';
			if (!$this->author->telegram || !$this->author->telegram->username)
				$err[] = 'telegram';
			if (!$this->author->address || !$this->author->address->address)
				$err[] = 'address';
		}
		return $err;
	}
	
	/**
	 * Get store users
	 * @return array
	 */
	public static function users() {
		if (is_null(self::$_users)) {
			$dependency = new DbDependency;
			$dependency->sql = 'SELECT MAX(updated_at) FROM '.User::tableName();
			
			//self::$_users = Yii::$app->cache->getOrSet('store-users', function() {
				
				$result = [];
				
				// Create query
				$query = new Query();
				$query->select('u.id, u.email, up.phone, u.username, up.first_name, up.last_name, up.middle_name');
				$query->from(self::tableName().' c');
				$query->leftJoin(User::tableName().' u', 'u.id = c.created_by');
				$query->leftJoin(UserProfile::tableName().' up', 'up.user_id = u.id');
				$query->where(['not in', 'c.status', [Status::TEMP, Status::DELETED]]);
				$query->groupBy('u.id');
				
				// Get rows
				$rows = $query->all(self::getDb());
				if ($rows) {
					foreach ($rows as $row) {
						$tmp = [];
						if (strlen($row['last_name']))
							$tmp[] = $row['last_name'];
						if (strlen($row['first_name']))
							$tmp[] = $row['first_name'];
						if (strlen($row['middle_name']))
							$tmp[] = $row['middle_name'];
						if (count($tmp))
							$row['fio'] = implode(' ', $tmp);
						else
							$row['fio'] = $row['username'];
						
						$result[$row['id']] = $row['fio'];
					}
				}
				
				return $result;
				self::$_users = $result;
				
			//}, Yii::$app->params['cache.duration'], $dependency);
		}
		
		return self::$_users;
	}

    public function getDataJsonLD() {
	    $type = null;
        if ($this->type == Type::ARTICLE) {
            $type = 'Article';
        }
	    else if ($this->type == Type::NEWS) {
            $type = 'NewsArticle';
        }
	    else if ($this->type == Type::BLOG) {
	        $type = 'BlogPosting';
        }

	    if (is_null($type)) {
	        return [];
        }

        $result = [
            '@context' => 'https://schema.org',
            '@type' => $type,
            'mainEntityOfPage' => (object)[
                '@type' => 'WebPage',
                '@id' => $this->getUrl(true),
            ],
            'headline' => $this->title,
            'description' => $this->descr,
            'datePublished' => Yii::$app->formatter->asDatetime($this->date_at, 'php:c'),
            'dateModified' => Yii::$app->formatter->asDatetime($this->updated_at, 'php:c'),
            'author' => (object)[
                '@type' => 'Person',
                'name' => $this->getAuthorName(),
            ],
            'publisher' => (object)[
                '@type' => 'Organization',
                'name' => 'Sprut.AI',
                'logo' => (object)[
                    'type' => 'ImageObject',
                    'url' => Url::to('/images/logo/logo.jpg', true),
                ],
            ],
        ];

        if ($this->image->getFileExists()) {
            $result['image'] = [
                0 => Url::to($this->image->getImageSrc(1000, 400, Mode::RESIZE), true)
            ];
        }

	    return $result;
    }

    /**
     * @return bool
     */
    public function getIsOwner() {
	    if (Yii::$app->user->hasRole([Role::SUPERADMIN, Role::ADMIN, Role::EDITOR])) {
	        return true;
        }
	    return Yii::$app->user->id == $this->author_id;
    }
	
	/**
	 * @inheritdoc
	 */
	public function afterFind() {
		parent::afterFind();
		
		// Set tags
		if ($this->isRelationPopulated('tags'))
			$this->_tags_ids_old = $this->getTags_ids();

		// Set payment types
		if ($this->isRelationPopulated('paymentTypes'))
			$this->_payment_types_ids_old = $this->getPayment_types_ids();

        if ($this->isRelationPopulated('contentModuleCatalogItems')) {
            $this->catalog_items_ids = ArrayHelper::getColumn($this->contentModuleCatalogItems, 'module_id');
        }
	}
	
	/**
	 * @inheritdoc
	 * @return bool
	 */
	public function beforeValidate() {
		if (!$this->company && $this->status == Status::MODERATED && $this->type != Type::VIDEO) {
			$this->validate_author_fields = $this->checkAuthorCompleteFields();
			if (count($this->validate_author_fields)) {
				$this->addError('validate_author_fields', 'Need author fill fields');
				return false;
			}
		}
		return parent::beforeValidate();
	}

    /**
     * @inheritdoc
     *
     * @param bool $insert
     *
     * @return bool
     * @throws InvalidConfigException
     */
	public function beforeSave($insert) {
		if ($insert && is_null($this->type)) {
            $this->type = static::type();
        }

		if (!Yii::$app->request->post('Seo') && $insert) {
		    $bodyParams = Yii::$app->request->getBodyParams();
            $bodyParams['Seo']['title'] = $this->title;
            Yii::$app->request->setBodyParams($bodyParams);
        }

		if (!$this->author_id)
			$this->author_id = Yii::$app->user->id;

		if (is_null($this->company_id)) {
		    $this->company_id = 0;
        }
		
		if (!$this->date_at)
			$this->date_at = time();
		
		$model = self::find()->where(['id' => $this->id])->one();
		$oldStatus = ($model) ? $model->status : false;
		$newStatus = $this->status;

		if ($oldStatus != Status::ENABLED && $newStatus == Status::ENABLED) {
		    if (!$this->published_at) {
                $this->published_at = time();

                if ($this->type != Type::VIDEO) {
                    $this->date_at = time();
                }
            }
		}

		if ($this->status == Status::ENABLED) {
            if (!$this->getIsOwner()) {
                $this->status = Status::MODERATED;
            }
        }

		if ($this->type == Type::VIDEO) {
		    $text = $this->text;
		    $text = str_replace('<p>', '', $text);
            $text = str_replace('</p>', "\r\n\r\n", $text);
            $text = str_replace('<br/>', "\r\n\\", $text);
            $text = str_replace('<br>', "\r\n\\", $text);
		    $this->descr = StringHelper::truncate(strip_tags($text), 200);
        }
		
		return parent::beforeSave($insert);
	}


	/**
	 * @inheritdoc
	 */
	public function afterSave($insert, $changedAttributes) {
		parent::afterSave($insert, $changedAttributes);

        $catalog_items_ids_old = $this->catalog_items_ids;

		// Update tags links
		TagModule::updateLinks($this->_tags_ids_old, $this->_tags_ids, self::moduleType(), $this->id, Type::NONE);
		
		// Update content tang links
		$tagsOld = (is_array($this->_tags_ids_old)) ? $this->_tags_ids_old : [];
		$tagsNew = (is_array($this->_tags_ids)) ? $this->_tags_ids : [];
		$tagsToAdd = array_values(array_diff($tagsNew, $tagsOld));
		$tagsToRemove = array_values(array_diff($tagsOld, $tagsNew));
		
		if (count($tagsToAdd) || count($tagsToRemove))
			ContentTag::updateLinks($this->id, $this->author_id, $this->company_id, $tagsNew);
		
		$oldAuthorId = (isset($changedAttributes['author_id']) && $changedAttributes['author_id']) ? $changedAttributes['author_id'] : false;
		$newAuthorId = $this->author_id;
		if ($oldAuthorId && $newAuthorId && $oldAuthorId != $newAuthorId) {
			ContentTag::updateLinks($this->id, $this->author_id, $this->company_id, $tagsNew);
		}

		// Update payment types links
        $this->unlinkAll('paymentTypes', true);

		$data = (Yii::$app instanceof \yii\web\Application) ? Yii::$app->request->post('PaymentTypeModules', []) : [];
        foreach (PaymentType::findAll($this->getPayment_types_ids()) as $m) {
        	$params = [
		        'module_type' => self::moduleType()
	        ];
        	
        	if (isset($data[$m->id])) {
        		$params['price'] = (double)preg_replace("/[^0-9]/", "", $data[$m->id]['price']);
		        $params['price_fixed'] = (boolean)$data[$m->id]['price_fixed'];
				$params['price_free'] = (isset($data[$m->id]['price_free'])) ? (boolean)$data[$m->id]['price_free'] : false;
				
				if ($params['price_free']) {
					$params['price'] = $m->price;
					$params['price_fixed'] = false;
				}
	        }
	        
            $this->link('paymentTypes', $m, $params);
        }
        
        // Send events
        $this->_event($changedAttributes);
        
		ContentAuthorStat::updateLinks($this->author);
		
		if (!is_null($this->company))
			ContentCompanyStat::updateLinks($this->company);
		
		$oldType = (isset($changedAttributes['type']) && $changedAttributes['type']) ? $changedAttributes['type'] : false;
		$newType = $this->type;
		if ($oldType !== false && $newType !== false && $newType != $oldType) {
			$this->_changeType($oldType, $newType);
		}

		$contentLinks = $this->getContentLinks('text');

        if (is_array($this->catalog_items_ids)) {
            foreach ($this->catalog_items_ids as $id) {
                $contentLinks[] = [
                    'id' => $id,
                    'module_type' => ModuleType::CATALOG_ITEM,
                ];
            }
        }

        $modules = [];
		if (is_array($contentLinks) && count($contentLinks)) {
		    foreach ($contentLinks as $c) {
		        $isFound = false;
		        foreach ($modules as $m) {
		            if ($m['module_type'] == $c['module_type'] && $m['module_id'] == $c['id']) {
		                $isFound = true;
		                break;
		            }
                }
		        if (!$isFound) {
                    $modules[] = [
                        'module_type' => $c['module_type'],
                        'module_id' => $c['id'],
                    ];
                }
            }
        }

        ContentModule::updateLinks($this->id, $modules);

		if (count($catalog_items_ids_old)) {
		    $catalogItems = CatalogItem::find()->where(['in', 'id', $catalog_items_ids_old])->all();
            if ($catalogItems) {
                foreach ($catalogItems as $catalogItem) {
                    CatalogItemStat::updateLinks($catalogItem);
                }
            }
        }
	}
	
	/**
	 * @param $changedAttributes
	 *
	 * @throws \ReflectionException
	 * @throws InvalidConfigException
	 */
	private function _event($changedAttributes) {
		$oldStatus = (isset($changedAttributes['status']) && $changedAttributes['status']) ? $changedAttributes['status'] : false;
		$newStatus = $this->status;
		$oldPublushedAt = (isset($changedAttributes['published_at']) && $changedAttributes['published_at']) ? $changedAttributes['published_at'] : null;
		
		if (in_array($oldStatus, [Status::TEMP, Status::DRAFT, Status::MODERATED]) && $newStatus == Status::ENABLED) {
			
			// Send to author about publication material
			$this->eventAuthorPublication($changedAttributes);
			
			// Send to subsribers author about new post
			$this->eventAuthorSubscribersPublication($changedAttributes);
			
			// Send to subscribers company about new post
			$this->eventCompanySubscribersPublication($changedAttributes);
		}
		
		if (in_array($oldStatus, [Status::MODERATED, Status::ENABLED]) && $newStatus == Status::DRAFT) {
			
			// Send to author about return to draft
			$this->eventAuthorDraft($changedAttributes);
		}
		
		if ($oldStatus != Status::MODERATED && $newStatus == Status::MODERATED && $this->type != Type::BLOG && $this->type != Type::QUESTION) {
			
			// Send to moderate chat
			$this->eventModerators($changedAttributes);
		}
	}
	
	/**
	 * Send to author about publication material
	 *
	 * @param $changedAttributes
	 *
	 * @throws \ReflectionException
	 */
	public function eventAuthorPublication($changedAttributes) {
	    $url = Url::to(['/'.$this->getUriModuleName().'/view', 'id' => $this->id], true);
        if ($this->type == Type::VIDEO) {
            $url = 'https://v2.sprut.ai/video/'.$this->seo->slugify;
        }

		if (in_array($this->type, [
			Type::ARTICLE,
			Type::NEWS,
			Type::BLOG,
			Type::PROJECT,
			Type::PLUGIN,
            Type::VIDEO,
		])) {
			$subject = Yii::t('notification', $this->getTypeName().'_moderated_subject');
			$message = Yii::t('notification', $this->getTypeName().'_moderated', [
				'url' => $url,
				'title' => $this->title,
			]);
			Yii::$app->notification->queue([$this->author_id], $subject, $message, $this->getTypeName());
		}
	}
	
	/**
	 * Send to author about return to draft
	 *
	 * @param $changedAttributes
	 */
	public function eventAuthorDraft($changedAttributes) {
	    $url = Url::to(['/content/'.$this->getTypeName().'/update', 'id' => $this->id], true);
        if ($this->type == Type::VIDEO) {
            $url = 'https://v2.sprut.ai/video/'.$this->id.'/update';
        }

		$subject = Yii::t('notification', $this->getTypeName().'_draft_moderate_subject');
		$message = Yii::t('notification', $this->getTypeName().'_draft_moderate', [
			'url' => $url,
			'title' => $this->title,
		]);
		
		Yii::$app->notification->queue([$this->author_id], $subject, $message, 'system');
	}
	
	/**
	 * Send to subscribers author about new post
	 *
	 * @param $changedAttributes
	 *
	 * @throws \ReflectionException
	 * @throws InvalidConfigException
	 */
	public function eventAuthorSubscribersPublication($changedAttributes) {
		$oldPublishedAt = (isset($changedAttributes['published_at']) && $changedAttributes['published_at']) ? $changedAttributes['published_at'] : null;
		
		if (!$oldPublishedAt && !$this->company_id && in_array($this->type, [
				Type::EVENT,
				Type::NEWS,
				Type::BLOG,
				Type::PROJECT,
				Type::PLUGIN,
		])) {
			$subscribersIds = User::find()->select(User::tableName().'.id')->subscribers(Vote::USER_FAVORITE, $this->author_id)->column();
			if (count($subscribersIds)) {
				$subject = Yii::t('notification', $this->getTypeName().'_subscribe_create_subject', [
					'author' => $this->getAuthorName(true),
				]);
				$message = Yii::t('notification', $this->getTypeName().'_subscribe_create', [
					'title' => $this->title,
					'url' => Url::to(['/'.$this->getUriModuleName().'/view', 'id' => $this->id], true),
					'author' => $this->getAuthorName(true),
					'author_url' => Url::toRoute(['/user/profile/view', 'id' => $this->author_id], true),
				]);
				
				Yii::$app->notification->queue($subscribersIds, $subject, $message, 'author');
			}
		}
	}
	
	/**
	 * Send to subscribers company about new post
	 *
	 * @param $changedAttributes
	 *
	 * @throws \ReflectionException
	 * @throws InvalidConfigException
	 */
	public function eventCompanySubscribersPublication($changedAttributes) {
		$oldPublishedAt = (isset($changedAttributes['published_at']) && $changedAttributes['published_at']) ? $changedAttributes['published_at'] : null;
		
		if (!$oldPublishedAt && $this->company_id && in_array($this->type, [
				Type::ARTICLE,
				Type::NEWS,
				Type::BLOG,
				Type::PROJECT,
				Type::PLUGIN,
		])) {
			$subscribersIds = User::find()->select(User::tableName().'.id')->subscribers(Vote::COMPANY_FAVORITE, $this->company_id)->column();
			
			$subject = Yii::t('notification-company', $this->getTypeName().'_subscribe_subject', [
				'author' => $this->getAuthorName(true),
			]);
			$message = Yii::t('notification-company', $this->getTypeName().'_subscribe', [
				'title' => $this->title,
				'url' => Url::to(['/'.$this->getUriModuleName().'/view', 'id' => $this->id], true),
				'company' => $this->company->title,
				'company_url' => Url::toRoute(['/companies/default/view', 'id' => $this->company_id], true),
			]);
			
			Yii::$app->notification->queue($subscribersIds, $subject, $message, 'author');
		}
		
		if ($this->type == Type::QUESTION && $this->company_id) {
			$usersIds = ArrayHelper::getColumn($this->company->users, 'id');
			if (count($usersIds)) {
				$subject = Yii::t('notification-company', 'question_new_subject', [
					'author' => $this->getAuthorName(true),
				]);
				$message = Yii::t('notification-company', 'question_new', [
					'title' => $this->title,
					'url' => Url::to(['/companies/question/view', 'company_id' => $this->company_id, 'id' => $this->id], true),
					'user' => $this->getAuthorName(true),
					'user_url' => Url::toRoute(['/user/profile/view', 'id' => $this->author_id], true),
				]);
				Yii::$app->notification->queue($usersIds, $subject, $message, 'author');
			}
		}
	}
	
	/**
	 * Send to moderate chat
	 *
	 * @param $changedAttributes
	 */
	public function eventModerators($changedAttributes) {
	    $url = Url::base('https').'/content/'.$this->getTypeName().'/'.$this->id;
	    if ($this->type == Type::VIDEO) {
	        $url = 'https://v2.sprut.ai/video/'.$this->seo->slugify;
        }

		$subject = Yii::t('notification', $this->getTypeName().'_need_moderate_subject');
		$message = Yii::t('notification', $this->getTypeName().'_need_moderate', [
			'url' => $url,
			'title' => $this->title,
			'fio' => $this->author->getAuthorName(true)
		]);

		Yii::$app->notification->queueTelegramIds(Yii::$app->getModule('telegram')->moderateIds, $message);
	}
	
	/**
	 * Change type model
	 * @param $oldType
	 * @param $newType
	 *
	 * @throws InvalidConfigException
	 */
	private function _changeType($oldType, $newType) {
		$this->_migrateVotes($oldType, $newType);
		$this->_migrateComments($oldType, $newType);
	}
	
	/**
	 * Migrate votes from old type to new type
	 *
	 * @param int $oldType
	 * @param int $newType
	 *
	 * @throws InvalidConfigException
	 */
	private function _migrateVotes(int $oldType, int $newType) {
		
		/** @var \common\modules\vote\Module $module */
		$module = Yii::$app->getModule('vote');
		
		$types = [];
		foreach ([$oldType, $newType] as $type) {
			foreach ($module->getEntitiesForClass($this->_getTypesClasses($type)) as $entity) {
				$settings = $module->getSettingsForEntity($entity);
				$types[$type][$settings['type']] = $entity;
			}
		}
		
		foreach ($types[$oldType] as $type => $entity) {
			if (isset($types[$newType][$type])) {
				$types['change'][$entity] = $types[$newType][$type];
				$types['changeEncoded'][$module->encodeEntity($entity)] = $module->encodeEntity($types[$newType][$type]);
			}
		}
		
		if (isset($types['changeEncoded'])) {
			foreach ($types['changeEncoded'] as $fromEntity => $toEntity) {
				Vote::updateAll([
					'entity' => $toEntity,
				], 'entity = :entity AND entity_id = :entity_id', [
					':entity' => $fromEntity,
					':entity_id' => $this->id,
				]);
				
				VoteAggregate::updateAll([
					'entity' => $toEntity,
				], 'entity = :entity AND entity_id = :entity_id', [
					':entity' => $fromEntity,
					':entity_id' => $this->id,
				]);
			}
		}
	}
	
	/**
	 * Migrate comments from old type to new type
	 *
	 * @param int $oldType
	 * @param int $newType
	 *
     */
	private function _migrateComments(int $oldType, int $newType) {
		
		/** @var \common\modules\comments\Module $module */
		$module = Yii::$app->getModule('comments');
		
		$fromEntity = $module->encryptEntity($this->_getTypesClasses($oldType));
		$toEntity = $module->encryptEntity($this->_getTypesClasses($newType));
		
		Comment::updateAll([
			'entity' => $toEntity,
		], 'entity = :entity AND entity_id = :entity_id', [
			':entity' => $fromEntity,
			':entity_id' => $this->id,
		]);
	}
	
	/**
	 * Get classes list or one by type
	 * @param null $type
	 *
	 * @return array|mixed|null
	 */
	private function _getTypesClasses($type = null) {
		$classes = [
			Type::ARTICLE => 'common\modules\content\models\Article',
			Type::NEWS => 'common\modules\content\models\News',
			Type::BLOG => 'common\modules\content\models\Blog',
			Type::PROJECT => 'common\modules\project\models\Project',
			Type::PLUGIN => 'common\modules\plugin\models\Plugin',
		];
		
		if (!is_null($type))
			return (isset($classes[$type])) ? $classes[$type] : null;
		
		return $classes;
	}
}
