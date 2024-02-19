<?php
namespace common\modules\catalog\models;

use Yii;
use yii\base\InvalidConfigException;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;

use common\modules\base\behaviors\ArrayFieldBehavior;
use common\modules\base\components\ActiveRecord;
use common\modules\base\helpers\enum\ModuleType;
use common\modules\base\helpers\enum\Status;

use common\modules\catalog\models\query\CatalogItemQuery;
use common\modules\comments\models\Comment;

use common\modules\company\models\Company;
use common\modules\company\models\CompanyCatalogItem;

use common\modules\content\models\Content;
use common\modules\content\models\ContentModule;

use common\modules\media\behaviors\MediaBehavior;
use common\modules\media\helpers\enum\Type as MediaType;
use common\modules\media\models\MediaImage;

use common\modules\seo\behaviors\SeoFields;

use common\modules\tag\helpers\enum\Type as TagType;
use common\modules\tag\models\query\TagModuleQuery;
use common\modules\tag\models\query\TagQuery;
use common\modules\tag\models\Tag;
use common\modules\tag\models\TagModule;

use common\modules\user\models\User;

use common\modules\vote\behaviors\VoteBehavior;

use api\models\favorite\Favorite;

/**
 * This is the model class for table "{{%catalog_item}}".
 *
 * @property integer $id
 * @property integer $vendor_id
 * @property string $title
 * @property string $model
 * @property string $url
 * @property string $documentation_url
 * @property string $comment
 * @property string $system_manufacturer
 * @property string $system_model
 * @property double $price
 * @property double $weight
 * @property double $length
 * @property double $width
 * @property double $height
 * @property boolean $available
 * @property integer $yandex_id
 * @property integer $in_stock
 * @property integer $sequence
 * @property boolean $is_sale
 * @property boolean $is_sprut
 * @property integer $sprut_type
 * @property array $sprut_content_json
 * @property integer $status
 * @property integer $created_by
 * @property integer $updated_by
 * @property integer $created_at
 * @property integer $updated_at
 * @property array $tags_ids
 *
 * Defined relations:
 * @property Tag $vendor
 * @property Tag[] $tags
 * @property Tag[] $platforms
 * @property Tag[] $protocols
 * @property Tag[] $types
 * @property CatalogItemField[] $fields
 * @property Company $company
 * @property Company[] $companies
 * @property User $createdBy
 * @property User $updatedBy
 */
class CatalogItem extends ActiveRecord
{
    /**
     * @var array
     */
    public $_tags_ids;

    /**
     * @var array
     */
    public $_tags_ids_old;

    /**
     * @var array
     */
    public $data_fields;

	/**
	 * @inheritdoc
	 */
	public static function tableName() {
		return '{{%catalog_item}}';
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
                'class' => MediaBehavior::class,
                'attribute' => 'image_mobile',
                'relation' => 'mediaMobile',
                'type' => MediaType::IMAGE,
            ],
            [
                'class' => MediaBehavior::class,
                'attribute' => 'image_desktop',
                'relation' => 'mediaDesktop',
                'type' => MediaType::IMAGE,
            ],
            [
                'class' => ArrayFieldBehavior::class,
                'attribute' => 'data',
            ],
            [
                'class' => ArrayFieldBehavior::class,
                'attribute' => 'sprut_content_json',
            ],
            [
                'class' => SeoFields::class,
            ],
            [
                'class' => VoteBehavior::class,
            ],
        ]);
	}

    /**
     * @return array
     */
	public function scenarios()
    {
        return [
            'default' => ['vendor_id', 'title', 'description', 'model', 'url', 'documentation_url', 'system_manufacturer', 'system_model', 'yandex_id', 'comment', 'in_stock', 'sequence', 'is_sale', 'is_sprut', 'sprut_type', 'sprut_content_json', 'status', 'tags_ids', 'files', 'data', 'data_fields']
        ];
    }

    /**
	 * @inheritdoc
	 */
	public function rules()
    {
		return [
            [['vendor_id', 'title', 'status'], 'required'],
		    [['yandex_id', 'in_stock', 'sequence', 'sprut_type', 'status', 'created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
            [['price', 'weight', 'length', 'width', 'height'], 'number'],
            [['is_sale', 'is_sprut'], 'boolean'],
            [['title', 'url', 'documentation_url', 'model'], 'string', 'max' => 255],
            [['system_manufacturer', 'system_model'], 'string', 'max' => 255],
            [['comment'], 'string', 'max' => 1000],
            [['description'], 'string', 'max' => 10000],
            [['url', 'documentation_url'], 'url', 'defaultScheme' => 'http', 'skipOnEmpty' => true],
            [['vendor_id'], 'exist', 'skipOnError' => true, 'targetClass' => Tag::class, 'targetAttribute' => ['vendor_id' => 'id']],
            [['media', 'tags_ids', 'data', 'data_fields', 'sprut_content_json'], 'safe'],
		];
	}

    /**
	 * @inheritdoc
	 */
	public function attributeLabels() {
		return [
			'id' => Yii::t('catalog-item', 'field_id'),
            'vendor_id' => Yii::t('catalog-item', 'field_vendor_id'),
            'title' => Yii::t('catalog-item', 'field_title'),
            'model' => Yii::t('catalog-item', 'field_model'),
            'url' => Yii::t('catalog-item', 'field_url'),
            'documentation_url' => Yii::t('catalog-item', 'field_documentation_url'),
			'comment' => Yii::t('catalog-item', 'field_comment'),
            'system_manufacturer' => Yii::t('catalog-item', 'field_system_manufacturer'),
            'system_model' => Yii::t('catalog-item', 'field_system_model'),
			'status' => Yii::t('catalog-item', 'field_status'),
			'created_by' => Yii::t('catalog-item', 'field_created_by'),
			'updated_by' => Yii::t('catalog-item', 'field_updated_by'),
			'created_at' => Yii::t('catalog-item', 'field_created_at'),
			'updated_at' => Yii::t('catalog-item', 'field_updated_at'),
		];
	}

	/**
	 * Get module type
	 * @return int
	 */
	public static function moduleType() {
		return ModuleType::CATALOG_ITEM;
	}
	
	/**
	 * @inheritdoc
	 * @return CatalogItemQuery the active query used by this AR class.
	 */
	public static function find() {
		return new CatalogItemQuery(get_called_class());
	}

    /**
     * @return TagModuleQuery
     */
    public function getTagModuleTypes() {
        return $this->hasMany(TagModule::class, ['module_id' => 'id'])->alias('tag_module_types')->andOnCondition([
            'tag_module_types.module_type' => self::moduleType(),
        ])->where([]);
    }

    /**
     * @return TagModuleQuery
     */
    public function getTagModulePlatforms() {
        return $this->hasMany(TagModule::class, ['module_id' => 'id'])->alias('tag_module_platforms')->andOnCondition([
            'tag_module_platforms.module_type' => self::moduleType(),
        ])->where([]);
    }

    /**
     * @return TagModuleQuery
     */
    public function getTagModuleProtocols() {
        return $this->hasMany(TagModule::class, ['module_id' => 'id'])->alias('tag_module_protocols')->andOnCondition([
            'tag_module_protocols.module_type' => self::moduleType(),
        ])->where([]);
    }

    /**
     * @return ActiveQuery
     */
    public function getVendor() {
        return $this->hasOne(Tag::class, ['id' => 'vendor_id'])->where([]);
    }

    /**
     * @return TagQuery|ActiveQuery
     */
    public function getTypes() {
        return $this->hasMany(Tag::class, ['id' => 'tag_id'])
            ->alias('types')
            ->via('tagModuleTypes')
            ->where([])
            ->andOnCondition('types.type & '.TagType::TYPE);
    }

    /**
     * @return TagQuery|ActiveQuery
     */
    public function getPlatforms() {
        return $this->hasMany(Tag::class, ['id' => 'tag_id'])
            ->alias('platforms')
            ->via('tagModulePlatforms')
            ->where([])
            ->andOnCondition('platforms.type & '.TagType::PLATFORM);
    }

    /**
     * @return TagQuery|ActiveQuery
     */
    public function getProtocols() {
        return $this->hasMany(Tag::class, ['id' => 'tag_id'])
            ->alias('protocols')
            ->via('tagModuleProtocols')
            ->where([])
            ->andOnCondition('protocols.type & '.TagType::PROTOCOL);
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
        return $this->hasMany(TagModule::class, ['module_id' => 'id'])->where([])->onCondition([
            TagModule::tableName().'.module_type' => self::moduleType(),
        ]);
    }

    /**
     * @return ActiveQuery
     */
    public function getFields(): ActiveQuery
    {
        return $this->hasMany(CatalogItemField::class, ['catalog_item_id' => 'id'])->onCondition([
            CatalogItemField::tableName().'.status' => Status::ENABLED,
        ])->where([]);
    }

    /**
     * @return ActiveQuery
     */
    public function getComments() {
        return $this->hasMany(Comment::class, ['entity_id' => 'id'])->alias('comments')->onCondition([
            'comments.module_type' => ModuleType::CATALOG_ITEM,
            'comments.status' => Status::ENABLED,
        ])->where([]);
    }

    /**
     * @return ActiveQuery
     */
    public function getStat() {
        return $this->hasOne(CatalogItemStat::class, ['catalog_item_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getContentModule() {
        return $this->hasMany(ContentModule::class, ['module_id' => 'id'])->where([])->onCondition([
            ContentModule::tableName().'.module_type' => self::moduleType(),
        ]);
    }

    /**
     * @return ActiveQuery
     */
    public function getContents() {
        return $this->hasMany(Content::class, ['id' => 'content_id'])->via('contentModule')->where([
            Content::tableName().'.status' => Status::ENABLED,
        ]);
    }

    /**
     * @return ActiveQuery
     */
    public function getCompanyCatalogItems() {
        return $this->hasMany(CompanyCatalogItem::class, ['catalog_item_id' => 'id'])->where([]);
    }

    /**
     * @return ActiveQuery
     */
    public function getCompanies() {
        return $this->hasMany(Company::class, ['id' => 'company_id'])->via('companyCatalogItems')->where([
            Company::tableName().'.status' => Status::ENABLED,
        ]);
    }

    /**
     * @return ActiveQuery
     */
    public function getCompany() {
        return $this->hasOne(Company::class, ['id' => 'company_id'])->via('companyCatalogItems')->where([
            Company::tableName().'.status' => Status::ENABLED,
        ]);
    }

    /**
     * @return ActiveQuery
     */
    public function getFavoritesHave() {
        return $this->hasMany(Favorite::class, ['module_id' => 'id'])->onCondition([
            Favorite::tableName().'.module_type' => ModuleType::CATALOG_ITEM,
            Favorite::tableName().'.group_id' => 4,
        ]);
    }

    /**
     * @return ActiveQuery
     */
    public function getFavoritesDevices() {
        return $this->hasMany(Favorite::class, ['module_id' => 'id'])->alias('fh')->onCondition([
            'fh.module_type' => ModuleType::CATALOG_ITEM,
            'fh.group_id' => 4,
        ]);
    }

    /**
     * @return ActiveQuery
     */
    public function getFavoritesHaveOwn() {
        return $this->hasOne(Favorite::class, ['module_id' => 'id'])->alias('fhu')->onCondition([
            'fhu.module_type' => ModuleType::CATALOG_ITEM,
            'fhu.group_id' => 4,
            'fhu.user_id' => Yii::$app->user->id,
        ]);
    }

	/**
	 * @return ActiveQuery
	 */
	public function getCreatedBy() {
		return $this->hasOne(User::class, ['id' => 'created_by']);
	}

	/**
	 * @return ActiveQuery
	 */
	public function getUpdatedBy() {
		return $this->hasOne(User::class, ['id' => 'updated_by']);
	}

    /**
     * @return ActiveQuery
     */
    public function getMediaMobile(): ActiveQuery
    {
        return $this->hasOne(MediaImage::class, ['module_id' => 'id'])->onCondition([
            'module_type' => $this->moduleType,
            'attribute' => 'image_mobile',
            'type' => MediaType::IMAGE,
            'is_main' => true,
            'status' => 1,
        ])->where([]);
    }

    /**
     * @return ActiveQuery
     */
    public function getMediaDesktop(): ActiveQuery
    {
        return $this->hasOne(MediaImage::class, ['module_id' => 'id'])->onCondition([
            'module_type' => $this->moduleType,
            'attribute' => 'image_desktop',
            'type' => MediaType::IMAGE,
            'is_main' => true,
            'status' => 1,
        ])->where([]);
    }

    /**
     * @return string
     */
    public function getSlugify_title() {
	    if ($this->vendor) {
            $title = trim(str_replace(strtolower($this->vendor->title), '', strtolower($this->title)));
            $title = $this->vendor->title.'-'.$title;
            if (strlen($this->model) && strpos(strtolower($title), strtolower($this->model)) === -1)
                $title .= '-'.$this->model;
            return $title;
        }
	    return $this->title;
    }

    /**
     * @return string
     */
    public function getTitle_vendor_model() {
        $tmp = "";
        if ($this->vendor) {
            $tmp .= $this->vendor->title." - ";
        }
        $tmp .= $this->title;
        if ($this->model) {
            $tmp .= " (".$this->model.")";
        }
        return $tmp;
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
        if (is_string($val)) {
            $val = explode(',', $val);
        }
        $this->_tags_ids = (is_null($val)) ? [] : $val;
    }
    
    /**
     * Get created date
     * @param string $format
     *
     * @return string
     * @throws InvalidConfigException
     */
    public function getCreated_date($format = 'dd-MM-yyyy') {
        if (!$this->created_at)
            $this->created_at = time();
        return Yii::$app->formatter->asDate($this->created_at, $format);
    }
    
    /**
     * Set created date
     * @param $val
     */
    public function setCreated_date($val) {
        $val .= ' 00:00:00';
        $this->created_at = strtotime($val);
    }

    /**
     * @return int|string
     */
    public function getAvailable() {
        $countOrders = CatalogItemOrder::find()->where([
            'catalog_item_id' => $this->id,
        ])->count();
        $count = $this->in_stock - $countOrders;
        if ($count < 0) {
            $count = 0;
        }
        return $count;
    }

    /**
     * @inheritdoc
     */
    public function afterFind() {
        parent::afterFind();

        // Set tags
        if ($this->isRelationPopulated('tags'))
            $this->_tags_ids_old = $this->getTags_ids();
    }

    public function beforeSave($insert) {
        return parent::beforeSave($insert); // TODO: Change the autogenerated stub
    }

    /**
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes) {
        parent::afterSave($insert, $changedAttributes);

        if (!$this->_tags_ids) {
            $this->_tags_ids = [];
        }

        if (!in_array($this->vendor_id, $this->_tags_ids)) {
            $this->_tags_ids[] = $this->vendor_id;
        }

        $this->_tags_ids = array_map('intval', array_unique($this->_tags_ids));

        TagModule::updateLinks($this->_tags_ids_old, $this->_tags_ids, self::moduleType(), $this->id);

        self::getDb()->createCommand()->delete(CatalogItemField::tableName(), [
            'catalog_item_id' => $this->id,
        ])->execute();

        if (is_array($this->data_fields)) {

            $fields = [];
            foreach ($this->data_fields as $fieldData => $val) {
                if (strpos($fieldData, '|') === false) {
                    continue;
                }

                list($fieldId, $name) = explode('|', $fieldData);

                /** @var CatalogField $field */
                $field = CatalogField::findById($fieldId, true, 'catalog-field', ['group']);

                /** @var CatalogFieldGroupTag $fieldGroupTags */
                $fieldGroupTags = CatalogFieldGroupTag::findAll([
                    'catalog_field_group_id' => $field->group->id,
                ]);
                if (is_null($fieldGroupTags)) {
                    continue;
                }

                /** @var CatalogFieldGroupTag $fieldGroupTag */
                foreach ($fieldGroupTags as $fieldGroupTag) {
                    $fields[] = [
                        'catalog_item_id' => $this->id,
                        'catalog_field_group_id' => $field->catalog_field_group_id,
                        'catalog_field_id' => $field->id,
                        'tag_id' => $fieldGroupTag->tag_id,
                        'type' => $field->type,
                        'format' => $field->format,
                        'name' => $name,
                        'value' => $val,
                        'title' => $field->title,
                        'identifier' => $field->identifier,
                        'unit' => $field->unit,
                        'sequence' => $field->sequence,
                        'status' => Status::ENABLED,
                    ];
                }
            }

            if (count($fields)) {
                self::getDb()->createCommand()->batchInsert(CatalogItemField::tableName(), [
                    'catalog_item_id',
                    'catalog_field_group_id',
                    'catalog_field_id',
                    'tag_id',
                    'type',
                    'format',
                    'name',
                    'value',
                    'title',
                    'identifier',
                    'unit',
                    'sequence',
                    'status',
                ], $fields)->execute();
            }
        }
    }
}
