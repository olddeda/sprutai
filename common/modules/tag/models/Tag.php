<?php
namespace common\modules\tag\models;

use Exception;

use Yii;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;
use yii\caching\DbDependency;

use common\modules\base\components\ActiveRecord;
use common\modules\base\behaviors\SlugifyBehavior;
use common\modules\base\helpers\enum\ModuleType;
use common\modules\base\helpers\enum\Status;
use common\modules\base\components\bitmask\BitmaskBehavior;
use common\modules\base\components\bitmask\BitmaskFieldsValidator;
use common\modules\base\components\Debug;

use common\modules\user\models\User;
use common\modules\user\models\query\UserQuery;

use common\modules\media\behaviors\MediaBehavior;
use common\modules\media\helpers\enum\Type as MediaType;
use common\modules\media\models\MediaImage;

use common\modules\seo\behaviors\SeoFields;
use common\modules\seo\models\SeoUri;

use common\modules\vote\behaviors\VoteBehavior;

use common\modules\catalog\models\CatalogFieldGroup;
use common\modules\catalog\models\CatalogFieldGroupTag;

use common\modules\tag\Module;
use common\modules\tag\helpers\enum\Type;
use common\modules\tag\models\query\TagQuery;
use common\modules\tag\models\query\TagModuleQuery;

/**
 * This is the model class for table "{{%tag}}".
 *
 * @property integer $id
 * @property integer $type
 * @property string $title
 * @property string $descr
 * @property string $text
 * @property string $telegram
 * @property boolean $is_none
 * @property boolean $is_system
 * @property boolean $is_vendor
 * @property boolean $is_type
 * @property boolean $is_platform
 * @property boolean $is_protocol
 * @property boolean $is_filter_group
 * @property boolean $is_filter
 * @property boolean $is_special
 * @property integer $sequence
 * @property boolean $multiple
 * @property boolean $visible_preview
 * @property integer $status
 * @property integer $created_by
 * @property integer $updated_by
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property array $links_ids
 * @property array $filters_ids
 * @property array $catalog_field_group_ids
 *
 * Defined relations:
 * @property Media $media
 * @property Tag[] $links
 * @property Tag[] $filters
 * @property TagModule[] $tagModule
 * @property TagModule[] $tagModuleFilters
 * @property CatalogFieldGroupTag[] $catalogFieldGroupTags
 * @property CatalogFieldGroup[] $catalogFieldGroups
 * @property User $createdBy
 * @property User $updatedBy
 */
class Tag extends ActiveRecord
{
    /** @var array  */
    public static $typeColors = [
        'is_none' => 'primary',
        'is_system' => 'blue',
        'is_filter_group' => 'filter_group',
        'is_filter' => 'filter',
    ];

	/** @var array */
	private $_links_ids;
	
	/** @var array */
	private $_links_ids_old;
	
	/** @var array */
	private $_filters_ids;
	
	/** @var array */
	private $_filters_ids_old;

    /** @var array */
    private $_catalog_field_group_ids;

    /** @var array */
    private $_catalog_field_group_ids_old;
	
	/**
	 * Get module type
	 * @return int
	 */
	public static function moduleType(): int
    {
		return ModuleType::TAG;
	}
	
	/**
	 * @inheritdoc
	 */
	public static function tableName(): string
    {
		return '{{%tag}}';
	}
	
	/**
	 * Returns a list of behaviors that this component should behave as.
	 *
	 * @return array the behavior configurations.
	 */
	public function behaviors(): array
    {
		return ArrayHelper::merge(parent::behaviors(), [
			'image' => [
				'class' => MediaBehavior::class,
				'attribute' => 'image',
				'type' => MediaType::IMAGE,
			],
			'seo' => [
				'class' => SeoFields::class,
			],
			'vote' => [
				'class' => VoteBehavior::class,
			],
            'bitmask' => [
                'class' => BitmaskBehavior::class,
                'fields' => self::attributeTypesFields(),
                'bitmaskAttribute' => 'type',
            ],

		]);
	}

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [['type', 'title', 'status', 'created_at', 'updated_at'], 'required'],
            [['type', 'sequence', 'status', 'created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
            [['multiple', 'visible_preview'], 'boolean'],
            [['title', 'telegram'], 'string', 'max' => 255],
			[['descr'], 'string', 'max' => 10000],
			[['text'], 'string', 'max' => 100000],
			[['title'], 'unique', 'when' => function($data) {
        		return $data->status != Status::DELETED;
			}],
			[['links_ids', 'filters_ids', 'catalog_field_group_ids'], 'safe'],
            ['links_ids', 'validateLinks'],
            ['catalog_field_group_ids', 'validateCatalogFieldGroups'],
            [['is_none', 'is_system', 'is_vendor', 'is_type', 'is_platform', 'is_protocol', 'is_filter_group', 'is_filter', 'is_company', 'is_special'], BitmaskFieldsValidator::class, 'maskAttribute' => 'fields'],
            [['is_none', 'is_system', 'is_vendor', 'is_type', 'is_platform', 'is_protocol', 'is_filter_group', 'is_filter', 'is_company', 'is_special'], 'safe'],
        ];
    }

    /**
     * @param $attribute
     * @param $params
     */
    public function validateLinks($attribute, $params) {
        if ($this->is_filter) {
            return $this->addError($attribute, Yii::t('tag', 'error_link_filter'));
        }

        if ($this->is_filter_group) {
            if (is_array($this->_links_ids)) {
                foreach (self::find()->where(['in', 'id', $this->_links_ids])->andWhere(['status' => Status::ENABLED])->all() as $item) {
                    if ($item->is_filter_group) {
                        return $this->addError($attribute, Yii::t('tag', 'error_link_filter_to_group'));
                    }
                }
            }
        }
    }

    /**
     * @param $attribute
     * @param $params
     */
    public function validateCatalogFieldGroups($attribute, $params)
    {
        if (!$this->is_type) {
            return $this->addError($attribute, Yii::t('tag', 'error_link_catalog_field_group'));
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels(): array
    {
        return ArrayHelper::merge([
            'id' => Yii::t('tag', 'field_id'),
			'type' => Yii::t('tag', 'field_type'),
            'title' => Yii::t('tag', 'field_title'),
			'descr' => Yii::t('tag', 'field_descr'),
			'text' => Yii::t('tag', 'field_text'),
			'telegram' => Yii::t('tag', 'field_telegram'),
            'sequence' => Yii::t('tag', 'field_sequence'),
            'multiple' => Yii::t('tag', 'field_multiple'),
            'visible_preview' => Yii::t('tag', 'field_visible_preview'),
            'status' => Yii::t('tag', 'field_status'),
            'created_at' => Yii::t('tag', 'field_created_at'),
            'updated_at' => Yii::t('tag', 'field_updated_at'),
			'links_ids' => Yii::t('tag', 'field_links_ids'),
			'filters_ids' => Yii::t('tag', 'field_filters_ids'),
            'catalog_field_group_ids' => Yii::t('tag', 'field_catalog_field_group_ids'),
        ], self::attributeTypeLabels());
    }

    /**
     * @return array
     */
    public static function attributeTypeLabels(): array
    {
        $tmp = [];
        foreach (self::attributeTypes() as $field => $val) {
            $tmp[$field] = Type::getLabel($val);
        }
        return $tmp;
    }

    /**
     * @return array
     */
    public static function attributeTypesFields(): array
    {
        $tmp = [];
        foreach (self::attributeTypes() as $field => $val) {
            $tmp[$field] = [$val, ($val == Type::NONE ? true : false)];
        }
        return $tmp;
    }

    /**
     * @return array
     */
    public static function attributeTypes(): array
    {
        $tmp = [];
        foreach (Type::$list as $val => $key) {
            $tmp['is_'.$key] = $val;
        }
        return $tmp;
    }
	
	/**
	 * @inheritdoc
	 * @return TagQuery the active query used by this AR class.
	 */
	public static function find(): TagQuery
    {
		return new TagQuery(get_called_class());
	}

    /**
     * @return ActiveQuery
     */
	public function getMedia(): ActiveQuery
    {
        return $this->hasOne(MediaImage::class, ['module_id' => 'id'])->onCondition([
            'module_type' => $this->moduleType,
            'attribute' => 'image',
            'type' => MediaType::IMAGE,
            'is_main' => true,
            'status' => 1,
        ])->where([]);
    }
	
	/**
	 * Get created user model
	 * @return UserQuery
	 */
	public function getCreatedBy(): UserQuery
    {
		return $this->hasOne(User::class, ['id' => 'created_by']);
	}
	
	/**
	 * Get updated user model
	 * @return UserQuery
	 */
	public function getUpdatedBy(): UserQuery
    {
		return $this->hasOne(User::class, ['id' => 'updated_by']);
	}
	
	/**
	 * @return ActiveQuery
	 */
	public function getLinks(): ActiveQuery
    {
		return $this->hasMany(Tag::class, ['id' => 'tag_id'])->via('tagModule')->where([]);
	}
	
	/**
	 * @return ActiveQuery
	 */
	public function getFilters(): ActiveQuery
    {
		return $this->hasMany(Tag::class, ['id' => 'tag_id'])->via('tagModuleFilters');
	}
	
	/**
	 * @return ActiveQuery
	 */
	public function getTagModule(): ActiveQuery
    {
		return $this->hasMany(TagModule::class, ['module_id' => 'id'])->where([])->onCondition([
            'module_type' => self::moduleType(),
		]);
	}

    /**
     * @return ActiveQuery
     */
    public function getTagModules(): ActiveQuery
    {
        return $this->hasMany(TagModule::class, ['tag_id' => 'id'])->alias('tm')->where([]);
    }
	
	/**
	 * @return ActiveQuery
	 */
	public function getTagModuleFilters(): ActiveQuery
    {
		return $this->hasMany(TagModule::class, ['module_id' => 'id'])->onCondition([
			TagModule::tableName().'.module_type' => ModuleType::TAG_FILTER,
		]);
	}

    /**
     * @return ActiveQuery
     */
    public function getCatalogFieldGroupTags(): ActiveQuery
    {
        return $this->hasMany(CatalogFieldGroupTag::class, ['tag_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getCatalogFieldGroups(): ActiveQuery
    {
        return $this->hasMany(CatalogFieldGroup::class, ['id' => 'catalog_field_group_id'])->via('catalogFieldGroupTags');
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
				$query->andWhere($class::tableName().'.'.$userColumn.' = :'.$userColumn, [
					':'.$userColumn => Yii::$app->user->id,
				]);
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
	 * Get uri
	 * @return string
	 *
	 * @throws \ReflectionException
	 */
	public function getUri() {
		$slugify = $this->seo->slugify;
		
		$uriRoot = SeoUri::uriForModule($this->getModuleClass(), 0, 'index') ?: 'tag';
		
		$tmp = ['tag'];
		if ($this->type != Type::NONE)
			$tmp[] = Type::getItem($this->type);
		$uri = implode('/', $tmp);
		if ($uri)
			return $uri.'/'.$slugify;
		return $slugify;
	}

    /**
     * @param string $glue
     * @param string $empty
     *
     * @return string
     */
    public function getTypesName($glue = ', ', $empty = '-') {
        $tmp = [];
        foreach ($this->fields as $attr => $f) {
            if ($this->$attr)
                $tmp[] = Type::getLabel($f[0]);
        }
        return (count($tmp)) ? implode($glue, $tmp) : $empty;
    }
	
	/**
	 * Get tag color
	 * @return string
	 */
	public function getColor() {
	    $color = self::$typeColors['is_none'];
	    foreach (self::$typeColors as $f => $c) {
	        if ($this->$f) {
	            $color = $c;
            }
        }
        return $color;
    }
	
	/**
	 * @return array
	 */
	public function getLinksListData() {
		return ArrayHelper::map($this->links, 'id', 'title');
	}
	
	/**
	 * @return array
	 */
	public function getFiltersListData() {
		return ArrayHelper::map($this->filters, 'id', 'title');
	}
	
	/**
	 * @return array
	 */
	public function getLinksData() {
		$data = [];
		$tmp = $this->getLinksListData();
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
	 * @return array
	 */
	public function getFiltersData() {
		$data = [];
		$tmp = $this->getFiltersListData();
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
	 * @param bool $asArray
	 * @param string $glue
	 *
	 * @return array|null|string
	 */
	public function getLinksValues($asArray = false, $glue = ', ') {
		$tmp = $this->links;
		if (is_array($tmp)) {
			$values = ArrayHelper::getColumn($tmp, 'title');
			return ($asArray) ? $values : implode($glue, $values);
		}
		return ($asArray) ? [] : null;
	}
	
	/**
	 * @param bool $asArray
	 * @param string $glue
	 *
	 * @return array|null|string
	 */
	public function getFiltersValues($asArray = false, $glue = ', ') {
		$tmp = $this->filters;
		if (is_array($tmp)) {
			$values = ArrayHelper::getColumn($tmp, 'title');
			return ($asArray) ? $values : implode($glue, $values);
		}
		return ($asArray) ? [] : null;
	}

    /**
     * @param bool $asArray
     * @param string $glue
     *
     * @return array|null|string
     */
    public function getCatalogFieldGroupsValues($asArray = false, $glue = ', ') {
        $tmp = $this->catalogFieldGroups;
        if (is_array($tmp)) {
            $values = ArrayHelper::getColumn($tmp, 'title');
            return ($asArray) ? $values : implode($glue, $values);
        }
        return ($asArray) ? [] : null;
    }
	
	/**
	 * @return array
	 */
	public function getLinks_ids(): array
    {
		if (is_null($this->_links_ids)) {
			$this->_links_ids = [];
			foreach ($this->links as $item)
			    $this->_links_ids[] = $item->id;
		}
		return $this->_links_ids;
	}
	
	/**
	 * @return array
	 */
	public function getFilters_ids(): array
    {
		if (is_null($this->_filters_ids)) {
			$this->_filters_ids = [];
			if ($this->filters) {
				foreach ($this->filters as $item)
					$this->_filters_ids[] = $item->id;
			}
		}
		return $this->_filters_ids;
	}

    /**
     * @return array
     */
    public function getCatalog_field_group_ids(): array
    {
        if (is_null($this->_catalog_field_group_ids)) {
            $this->_catalog_field_group_ids = [];
            if ($this->catalogFieldGroups) {
                foreach ($this->catalogFieldGroups as $item)
                    $this->_catalog_field_group_ids[] = $item->id;
            }
        }
        return $this->_catalog_field_group_ids;
    }
	
	/**
	 * @return array
	 */
	public function getLinks_ids_old() {
		if (is_null($this->_links_ids_old)) {
			$this->_links_ids_old = [];
			if ($this->links) {
				foreach ($this->links as $item)
					$this->_links_ids_old[] = $item->id;
			}
		}
		return $this->_links_ids_old;
	}
	
	/**
	 * @return array
	 */
	public function getFilters_ids_old(): array
    {
		if (is_null($this->_filters_ids_old)) {
			$this->_filters_ids_old = [];
			if ($this->filters) {
				foreach ($this->filters as $item)
					$this->_filters_ids_old[] = $item->id;
			}
		}
		return $this->_filters_ids_old;
	}

    /**
     * @return array
     */
    public function getCatalog_field_group_ids_old(): array
    {
        if (is_null($this->_catalog_field_group_ids_old)) {
            $this->_catalog_field_group_ids_old = [];
            if ($this->catalogFieldGroups) {
                foreach ($this->catalogFieldGroups as $item)
                    $this->_catalog_field_group_ids_old[] = $item->id;
            }
        }
        return $this->_catalog_field_group_ids_old;
    }
	
	/**
	 * @param ?array $val
	 */
	public function setLinks_ids($val) {
		$this->_links_ids = (is_array($val)) ? $val : [];
	}
	
	/**
	 * @param array $val
	 */
	public function setFilters_ids($val) {
		$this->_filters_ids = (is_array($val)) ? $val : [];
	}

    /**
     * @param array $val
     */
    public function setCatalog_field_group_ids($val) {
        $this->_catalog_field_group_ids = (is_array($val)) ? $val : [];
    }

    /**
     * @param string $key
     * @param string $val
     * @param string $orderBy
     * @param array $types
     *
     * @return mixed
     * @throws Exception
     */
    static public function listDataType($key = 'id', $val = 'title', $orderBy = 'title', $types = []) {
        return self::listData($key, $val, $orderBy, [], [], [
            [
                self::tableName().'.type',
                array_sum($types),
                '&',
            ]
        ]);
    }
	
	/**
	 * @param array $condition
     * @param array $compare
	 *
	 * @return mixed
	 * @throws \Throwable
	 */
	static public function listWithColors($condition = [], $compare = []): array
    {
		$query = self::find();
		$query->andWhere(['not in', 'status', Status::TEMP]);
		if ($condition)
			$query->andWhere($condition);
        if ($compare) {
            foreach ($compare as $c) {
                $query->andFilterCompare($c[0], $c[1], $c[2]);
            }
        }
		$query->orderBy([
			'type' => SORT_DESC,
			'title' => SORT_ASC,
		]);
		
		$tmp = [];
		foreach ($query->all() as $data) {
			$tmp[] = [
				'id' => $data->id,
				'title' => $data->title,
				'color' => $data->getColor(),
			];
		}
		
		return $tmp;
	}

    /**
     * @param array $types
     *
     * @return mixed
     * @throws \Throwable
     */
    static public function listWithColorsType($types = []) {
        return self::listWithColors([], [
            [
                self::tableName().'.type',
                array_sum($types),
                '&',
            ]
        ]);
    }

    /**
     * @param int $type
     * @param string $title
     *
     * @return false|string|null
     */
    static public function findIdsByTypeAndTitle(int $type, string $title) {
        return self::find()->select('id')->andFilterCompare('type', $type, '&')->andFilterWhere(['like', 'title', $title])->column();
    }

    /**
     * @inheritdoc
     */
	public function beforeSave($insert) {

		// Set sequence
		if (!$this->sequence) {
			$this->sequence = self::lastSequence(['type' => $this->type]);
		}
		
		return parent::beforeSave($insert);
	}

    /**
     * @inheritdoc
     * @throws \yii\db\Exception
     */
	public function afterSave($insert, $changedAttributes)
    {
		parent::afterSave($insert, $changedAttributes);

		if (is_array($this->getLinks_ids_old()) && is_array($this->getLinks_ids())) {
            TagModule::updateLinks($this->getLinks_ids_old(), $this->getLinks_ids(), self::moduleType(), $this->id);
        }

        if (is_array($this->getCatalog_field_group_ids_old()) && is_array($this->getCatalog_field_group_ids())) {
            CatalogFieldGroupTag::updateLinks($this->getCatalog_field_group_ids_old(), $this->getCatalog_field_group_ids(), $this->id);
        }
	}
}
