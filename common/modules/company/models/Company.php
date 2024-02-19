<?php
namespace common\modules\company\models;

use CdekSDK\CdekClient;
use common\modules\base\components\ActiveRecord;
use common\modules\base\components\bitmask\BitmaskBehavior;
use common\modules\base\extensions\phoneInput\PhoneInputBehavior;
use common\modules\base\extensions\phoneInput\PhoneInputValidator;
use common\modules\base\helpers\enum\ModuleType;
use common\modules\base\helpers\enum\Status;
use common\modules\base\validators\EitherValidator;
use common\modules\catalog\models\CatalogItem;
use common\modules\company\helpers\enum\Type;
use common\modules\company\models\query\CompanyQuery;
use common\modules\content\helpers\enum\Status as ContentStatus;
use common\modules\content\helpers\enum\Type as ContentType;
use common\modules\content\models\Article;
use common\modules\content\models\Blog;
use common\modules\content\models\Content;
use common\modules\content\models\ContentCompanyStat;
use common\modules\content\models\ContentTag;
use common\modules\content\models\News;
use common\modules\media\behaviors\MediaBehavior;
use common\modules\media\helpers\enum\Type as MediaType;
use common\modules\plugin\models\Plugin;
use common\modules\plugin\models\Portfolio;
use common\modules\project\models\Project;
use common\modules\tag\models\Tag;
use common\modules\vote\behaviors\VoteBehavior;
use GuzzleHttp\Client;
use Yii;
use yii\caching\DbDependency;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\Application;
use yii\web\NotFoundHttpException;

/**
 * This is the model class for table "{{%company}}".
 *
 * @property int $id
 * @property int $type
 * @property int $tag_id
 * @property string $title
 * @property string $descr
 * @property string $text
 * @property string $site
 * @property string $email
 * @property string $phone
 * @property string $telegram
 * @property string $instagram
 * @property string $facebook
 * @property string $vk
 * @property string $ok
 * @property integer $cdek_postcode
 * @property integer $cdek_country_id
 * @property integer $cdek_city_id
 * @property string $cdek_city_name
 * @property integer $cdek_tariff
 * @property string $cdek_account
 * @property string $cdek_secure_password
 * @property bool $cdek_test_mode
 * @property bool $cdek_enabled
 * @property bool $is_vendor
 * @property bool $is_integrator
 * @property bool $is_shop
 * @property int $status
 * @property int $created_by
 * @property int $updated_by
 * @property int $published_at
 * @property int $created_at
 * @property int $updated_at
 *
 * Defined relations:
 * @property CompanyUser[] $users
 * @property CompanyAddress $address
 * @property CompanyAddress[] $addresses
 * @property CompanyDiscount[] $discounts
 * @property CatalogItem[] $catalogItems
 * @property Tag $tag
 * @property Tag[] $tags
 * @property Content[] $contents
 * @property ContentCompanyStat $contentsStat
 * @property Article[] $contentsArticles
 * @property News[] $contentsNews
 * @property Blog[] $contentsBlogs
 * @property Project[] $contentsProjects
 * @property Plugin[] $contentsPlugins
 * @property Portfolio[] $contentsPortfolios
 */
class Company extends ActiveRecord
{
	/**
	 * @var array|null
	 */
	private $_users_ids;
	
    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return '{{%company}}';
    }
	
	/**
	 * @inheritdoc
	 */
	public function behaviors() {
		return ArrayHelper::merge(parent::behaviors(), [
			[
				'class' => MediaBehavior::class,
				'attribute' => 'logo',
				'type' => MediaType::IMAGE,
			],
			[
				'class' => VoteBehavior::class,
			],
			[
				'class' => PhoneInputBehavior::class,
			],
			[
				'class' => BitmaskBehavior::class,
				'fields' => [
					'is_vendor' => [Type::VENDOR, true],
					'is_integrator' => [Type::INTEGRATOR, false],
					'is_shop' => [Type::SHOP, false],
				],
				'bitmaskAttribute' => 'type',
			],
		]);
	}

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['type', 'title', 'descr', 'text'], 'required'],
            [['type', 'tag_id', 'cdek_postcode', 'cdek_country_id', 'cdek_city_id', 'cdek_tariff', 'status', 'published_at', 'created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
            [['title', 'site', 'email', 'phone', 'cdek_city_name', 'cdek_account', 'cdek_secure_password'], 'string', 'max' => 255],
			[['descr'], 'string', 'max' => 10000],
			[['text'], 'string', 'max' => 100000],
			[['site'], 'url', 'defaultScheme' => 'http', 'skipOnEmpty' => true],
			[['email'], 'email'],
			[['telegram', 'instagram', 'facebook', 'vk', 'ok'], 'string', 'max' => 100],
            [['cdek_test_mode', 'cdek_enabled'], 'boolean'],
			[['title', 'descr'], 'filter', 'filter' => '\yii\helpers\HtmlPurifier::process'],
			[['title', 'descr'], 'filter', 'filter' => '\yii\helpers\Html::encode'],
			[['phone'], PhoneInputValidator::class, 'skipOnEmpty' => true],
			[['is_vendor', 'is_integrator', 'is_shop'], EitherValidator::class],
			[['is_vendor', 'is_integrator', 'is_shop'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('company', 'field_id'),
			'type' => Yii::t('company', 'field_type'),
			'tag_id' => Yii::t('company', 'field_tag_id'),
            'title' => Yii::t('company', 'field_title'),
            'descr' => Yii::t('company', 'field_descr'),
            'text' => Yii::t('company', 'field_text'),
			'site' => Yii::t('company', 'field_site'),
			'phone' => Yii::t('company', 'field_phone'),
			'email' => Yii::t('company', 'field_email'),
			'telegram' => Yii::t('company', 'field_telegram'),
			'instagram' => Yii::t('company', 'field_instagram'),
			'facebook' => Yii::t('company', 'field_facebook'),
			'vk' => Yii::t('company', 'field_vk'),
			'ok' => Yii::t('company', 'field_ok'),
            'cdek_postcode' => Yii::t('company', 'field_cdek_postcode'),
            'cdek_country_id' => Yii::t('company', 'field_cdek_country_id'),
            'cdek_city_id' => Yii::t('company', 'field_cdek_city_id'),
            'cdek_city_name' => Yii::t('company', 'field_cdek_city_name'),
            'cdek_tariff' => Yii::t('company', 'field_cdek_tariff'),
            'cdek_account' => Yii::t('company', 'field_cdek_account'),
            'cdek_secure_password' => Yii::t('company', 'field_cdek_secure_password'),
            'cdek_test_mode' => Yii::t('company', 'field_cdek_test_mode'),
            'cdek_enabled' => Yii::t('company', 'field_cdek_enabled'),
			'address' => Yii::t('company', 'field_address'),
            'status' => Yii::t('company', 'field_status'),
			'is_vendor' => Yii::t('company', 'field_is_vendor'),
			'is_integrator' => Yii::t('company', 'field_is_integrator'),
			'is_shop' => Yii::t('company', 'field_is_shop'),
			'published_at' => Yii::t('company', 'field_published_at'),
            'created_by' => Yii::t('company', 'field_created_by'),
            'updated_by' => Yii::t('company', 'field_updated_by'),
            'created_at' => Yii::t('company', 'field_created_at'),
            'updated_at' => Yii::t('company', 'field_updated_at'),
        ];
    }

    /**
     * {@inheritdoc}
     * @return CompanyQuery the active query used by this AR class.
     */
    public static function find() {
        return new CompanyQuery(get_called_class());
    }
 
	/**
	 * Find companies by user id
	 * @param $userId
	 *
	 * @return array|\yii\db\ActiveRecord|null
	 */
    public static function findByUserId($userId) {
    	return self::find()->joinWith(['users'])->andWhere([
    		CompanyUser::tableName().'.user_id' => $userId,
		])->groupBy([
			self::tableName().'.id',
		])->all();
	}
	
	/**
	 * @return ActiveQuery
	 */
	public function getUsers() {
		return $this->hasMany(CompanyUser::class, ['company_id' => 'id'])->where([]);
	}
	
	/**
	 * Get address relation
	 * @return ActiveQuery
	 */
	public function getAddress() {
		return $this->hasOne(CompanyAddress::class, ['company_id' => 'id'])->andOnCondition(['is_primary' => true])->where([]);
	}
	
	/**
	 * Get addresses relation
	 * @return ActiveQuery
	 */
	public function getAddresses() {
		return $this->hasMany(CompanyAddress::class, ['company_id' => 'id']);
	}

    /**
     * @return ActiveQuery
     */
    public function getCompanyCatalogItems() {
        return $this->hasMany(CompanyCatalogItem::class, ['company_id' => 'id'])->where([]);
    }

    /**
     * @return ActiveQuery
     */
    public function getCatalogItems() {
        return $this->hasMany(CatalogItem::class, ['catalog_item_id' => 'id'])->via('companyCatalogItems');
    }
	
	/**
	 * Get discounts relation
	 * @return ActiveQuery
	 */
	public function getDiscounts() {
		return $this->hasMany(CompanyDiscount::class, ['company_id' => 'id'])->onCondition(
		    CompanyDiscount::tableName().'.infinitely = 0 AND '.CompanyDiscount::tableName().'.date_start_at <= :date_start AND '.CompanyDiscount::tableName().'.date_end_at >= :date_end AND '.CompanyDiscount::tableName().'.status = :status', [
			':date_start' => time(),
			':date_end' => time(),
            ':status' => Status::ENABLED,
		])->orOnCondition([
		    CompanyDiscount::tableName().'.infinitely' => 1,
            CompanyDiscount::tableName().'.status' => Status::ENABLED,
        ])->where([]);
	}
	
	/**
	 * Get content relation
	 * @return ActiveQuery
	 */
	public function getContents() {
		return $this->hasMany(Content::class, ['author_id' => 'id'])->onCondition([
			Content::tableName().'.status' => ContentStatus::ENABLED,
		])->onCondition([
			'in',
			Content::tableName().'.type',
			[
				ContentType::ARTICLE,
				ContentType::NEWS,
				ContentType::BLOG,
			]
		])->where([]);
	}
	
	/**
	 * Get content stat relation
	 * @return ActiveQuery
	 */
	public function getContentsStat() {
		return $this->hasOne(ContentCompanyStat::class, ['company_id' => 'id']);
	}
	
	/**
	 * Get content aricles relation
	 * @return ActiveQuery
	 */
	public function getContentsArticles() {
		return $this->hasMany(Content::class, ['company_id' => 'id'])->onCondition([
			'articles.status' => ContentStatus::ENABLED,
			'articles.type' => ContentType::ARTICLE,
		])->alias('articles')->where([]);
	}
	
	/**
	 * Get content news relation
	 * @return ActiveQuery
	 */
	public function getContentsNews() {
		return $this->hasMany(Content::class, ['company_id' => 'id'])->onCondition([
			'news.status' => ContentStatus::ENABLED,
			'news.type' => ContentType::NEWS,
		])->alias('news')->where([]);
	}
	
	/**
	 * Get content blogs relation
	 * @return ActiveQuery
	 */
	public function getContentsBlogs() {
		return $this->hasMany(Content::class, ['company_id' => 'id'])->onCondition([
			'blogs.status' => ContentStatus::ENABLED,
			'blogs.type' => ContentType::BLOG,
		])->alias('blogs')->where([]);
	}
	
	/**
	 * Get content projects relation
	 * @return ActiveQuery
	 */
	public function getContentsProjects() {
		return $this->hasMany(Content::class, ['company_id' => 'id'])->onCondition([
			'projects.status' => ContentStatus::ENABLED,
			'projects.type' => ContentType::PROJECT,
		])->alias('projects')->where([]);
	}
	
	/**
	 * Get content plugins relation
	 * @return ActiveQuery
	 */
	public function getContentsPlugins() {
		return $this->hasMany(Content::class, ['company_id' => 'id'])->onCondition([
			'plugins.status' => ContentStatus::ENABLED,
			'plugins.type' => ContentType::PLUGIN,
		])->alias('plugins')->where([]);
	}
	
	/**
	 * Get content plugins relation
	 * @return ActiveQuery
	 */
	public function getContentsPortfolios() {
		return $this->hasMany(Content::class, ['company_id' => 'id'])->onCondition([
			'portfolios.status' => ContentStatus::ENABLED,
			'portfolios.type' => ContentType::PORTFOLIO,
		])->alias('portfolios')->where([]);
	}
	
	/**
	 * @return \common\modules\tag\models\query\TagModuleQuery
	 */
	public function getContentTag() {
		return $this->hasMany(ContentTag::class, ['company_id' => 'id']);
	}
	
	/**
	 * @return \common\modules\tag\models\query\TagQuery
	 */
	public function getTags() {
		return $this->hasMany(Tag::class, ['id' => 'tag_id'])->via('contentTag')->orderBy([
			Tag::tableName().'.title' => SORT_ASC,
		])->where([]);
	}
	
	/**
	 * Get tag relation
	 * @return ActiveQuery
	 */
	public function getTag() {
		return $this->hasOne(Tag::class, ['id' => 'tag_id'])->where([]);
	}
	
	/**
	 * Get users ids
	 *
	 * @return array
	 */
	public function getUsersIds() {
		if (is_null($this->_users_ids))
			$this->_users_ids = ($this->users) ? ArrayHelper::getColumn($this->users, 'user_id') : [];
		return $this->_users_ids;
	}

    /**
     * Get url
     *
     * @return string
     * @throws \ReflectionException
     */
    public function getUrl($scheme = false) {
        return Url::to('/'.self::getUriModuleName().'/'.$this->id, $scheme);
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
	static public function findByColumn($column, $value, $except = false, $messageCategory = 'company', $relations = [], $cache = false, $own = false, $conditions = null, $skipFields = [], $callback = null) {
		$query = self::find();
		
		self::prepareQuery($query);
		
		$query->andWhere(self::tableName().'.'.$column.' = :'.$column, [
			':'.$column => $value,
		]);
		
		
		$relations = ArrayHelper::merge($relations, [
			'users',
		]);
		
		if (is_array($relations) && count($relations))
			$query->joinWith($relations);
		
		// Add owner user condition
		if ($own) {
			if (!Yii::$app->user->isSuperAdmin && !Yii::$app->user->isAdmin && !Yii::$app->user->isEditor) {
				
				if ($column == 'id') {
					$companiesIds = CompanyUser::find()->select('company_id')->where([
						'user_id' => Yii::$app->user->id,
						'status' => Status::ENABLED,
					])->column();
					if (!in_array($value, $companiesIds)) {
						$query->andWhere(CompanyUser::tableName().'.user_id = :user_id AND '.CompanyUser::tableName().'.status = :user_status', [
							':user_id' => Yii::$app->user->id,
							':user_status' => Status::ENABLED,
						]);
					}
				}
				else {
					$query->andWhere(CompanyUser::tableName().'.user_id = :user_id AND '.CompanyUser::tableName().'.status = :user_status', [
						':user_id' => Yii::$app->user->id,
						':user_status' => Status::ENABLED,
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
			$dependency->sql = 'SELECT MAX(updated_at) FROM '.self::tableName();
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
	 * @param $query
	 */
	public static function prepareQuery($query) {
		if (Yii::$app instanceof Application)
			$query->votes();
	}
	
	/**
	 * Get module type
	 * @return int
	 */
	public static function moduleType() {
		return ModuleType::COMPANY;
	}
	
	/**
	 * @return string
	 */
	public function getUriModuleName() {
		return 'companies/default';
	}
	
	/**
	 * Check is published social
	 *
	 * @return bool
	 */
	public function getIsPublished() {
		return false;
	}
	
	/**
	 * Get type name
	 *
	 * @return string
	 */
	public function getTypeName() {
		return Type::getLabel($this->type);
	}
	
	public function getTypesName($glue = ', ', $empty = '-') {
		$tmp = [];
		foreach ($this->fields as $attr => $f) {
			if ($this->$attr)
				$tmp[] = Type::getLabel($f[0]);
		}
		return (count($tmp)) ? implode($glue, $tmp) : $empty;
	}
	
	/**
	 * Get type with title
	 * @param string $separator
	 *
	 * @return string
	 */
	public function getTypeWithTitle($separator = ': ') {
		return $this->getTypeName().$separator.$this->title;
	}
	
	/**
	 * Check is own company
	 *
	 * @return bool
	 */
	public function getIsOwn() {
		return in_array(Yii::$app->user->id, ArrayHelper::getColumn($this->users, 'id'));
	}

    /**
     * @return CdekClient
     */
	public function getCdekClient() {
	    return new CdekClient($this->cdek_account, $this->cdek_secure_password, new Client([
	        'base_uri' => $this->cdek_test_mode ? 'https://integration.edu.cdek.ru' : CdekClient::STANDARD_BASE_URL,
        ]));
    }
	
	/**
	 * @inheritdoc
	 */
	public function afterSave($insert, $changedAttributes) {
		parent::afterSave($insert, $changedAttributes);
		
		$oldStatus = (isset($changedAttributes['status']) && $changedAttributes['status']) ? $changedAttributes['status'] : false;
		$newStatus = $this->status;
		
		if ($oldStatus != Status::ENABLED && $newStatus == Status::ENABLED && !$this->published_at) {
			$this->published_at = time();
		}
	}
}
