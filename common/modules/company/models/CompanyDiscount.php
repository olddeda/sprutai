<?php
namespace common\modules\company\models;

use common\modules\base\components\Debug;
use Yii;
use yii\helpers\ArrayHelper;

use common\modules\base\components\ActiveRecord;
use common\modules\base\helpers\enum\ModuleType;
use common\modules\base\helpers\enum\Status;

use common\modules\tag\models\Tag;
use common\modules\tag\models\TagModule;
use common\modules\tag\helpers\enum\Type;

use common\modules\company\models\query\CompanyDiscountQuery;

/**
 * This is the model class for table "{{%company_discount}}".
 *
 * @property int $id
 * @property int $company_id
 * @property string $title
 * @property string $promocode
 * @property string $descr
 * @property boolean $infinitely
 * @property int $discount
 * @property int $discount_to
 * @property int $status
 * @property int $created_by
 * @property int $updated_by
 * @property int $date_start_at
 * @property int $date_end_at
 * @property int $created_at
 * @property int $updated_at
 *
 * Defined relations:
 * @property \common\modules\company\models\Company $company
 * @property \common\modules\tag\models\Tag[] $tags
 */
class CompanyDiscount extends ActiveRecord
{
	/**
	 * @var array
	 */
	private $_tags_ids;
	
	/**
	 * @var array
	 */
	private $_tags_ids_old;
	
    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return '{{%company_discount}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['company_id', 'promocode', 'discount', 'date_start', 'date_end'], 'required'],
            [['company_id', 'discount', 'discount_to', 'date_start_at', 'date_end_at', 'status', 'created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
            [['promocode'], 'string', 'max' => 255],
			[['descr'], 'string', 'max' => 500],
			[['infinitely'], 'boolean'],
			[['date_start', 'date_end'], 'required', 'when' => function($data) {
        		return $data->infinitely == false;
			}, 'whenClient' =>  "function (attribute, value) {
        		return $('#companydiscount-infinitely').is(':checked') == false;
    		}"],
			[['tags_ids', 'date_start', 'date_end'], 'safe'],
			[['tags_ids'], 'required', 'message' => Yii::t('company-discount', 'error_need_tags')],
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('company-discount', 'field_id'),
            'company_id' => Yii::t('company-discount', 'field_company_id'),
            'promocode' => Yii::t('company-discount', 'field_promocode'),
			'descr' => Yii::t('company-discount', 'field_descr'),
            'discount' => Yii::t('company-discount', 'field_discount'),
            'discount_to' => Yii::t('company-discount', 'field_discount_to'),
			'infinitely' => Yii::t('company-discount', 'field_infinitely'),
            'status' => Yii::t('company-discount', 'field_status'),
            'created_by' => Yii::t('company-discount', 'field_created_by'),
            'updated_by' => Yii::t('company-discount', 'field_updated_by'),
			'date_start_at' => Yii::t('company-discount', 'field_date_start_at'),
			'date_end_at' => Yii::t('company-discount', 'field_date_end_at'),
            'created_at' => Yii::t('company-discount', 'field_created_at'),
            'updated_at' => Yii::t('company-discount', 'field_updated_at'),
			'date_start' => Yii::t('company-discount', 'field_date_start'),
			'date_end' => Yii::t('company-discount', 'field_date_end'),
			'tags_ids' => Yii::t('company-discount', 'field_tags_ids'),
        ];
    }
	
	/**
	 * Get module type
	 * @return int
	 */
	public static function moduleType() {
		return ModuleType::COMPANY_DISCOUNT;
	}

    /**
     * {@inheritdoc}
     * @return \common\modules\company\models\query\CompanyDiscountQuery the active query used by this AR class.
     */
    public static function find() {
        return new CompanyDiscountQuery(get_called_class());
    }
	
	/**
	 * @return CompanyDiscountQuery
	 */
    public static function findActive() {
    	return self::find()
			->joinWith(['tags', 'company'])
			->andWhere(self::tableName().'.infinitely = 0 AND '.self::tableName().'.date_start_at <= :date_start AND '.self::tableName().'.date_end_at >= :date_end AND '.self::tableName().'.status = :status', [
				':date_start' => time(),
				':date_end' => time(),
                ':status' => Status::ENABLED,
			])->orWhere([
				self::tableName().'.infinitely' => 1,
                self::tableName().'.status' => Status::ENABLED,
			])->groupBy(self::tableName().'.id');
	}
	
	/**
	 * Find tags by ids
	 *
	 * @param $tagsIds
	 *
	 * @return CompanyDiscountQuery
	 */
    public static function findByTagsIds($tagsIds) {
    	return self::findActive()->andWhere([
			'in', Tag::tableName().'.id', $tagsIds,
		])->all();
	}
	
	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getCompany() {
		return $this->hasOne(Company::class, ['id' => 'company_id']);
	}
	
	/**
	 * @return \common\modules\tag\models\query\TagQuery
	 */
	public function getTags() {
		return $this->hasMany(Tag::class, ['id' => 'tag_id'])->via('tagModule')->where([]);
	}
	
	/**
	 * @return \common\modules\tag\models\query\TagModuleQuery
	 */
	public function getTagModule() {
		return $this->hasMany(TagModule::class, ['module_id' => 'id'])->onCondition([
			TagModule::tableName().'.module_type' => self::moduleType(),
			TagModule::tableName().'.status' => Status::ENABLED,
		])->where([]);
	}
	
	/**
	 * Get title
	 * @return string
	 */
	public function getTitle() {
	    $discount = ($this->discount_to) ? Yii::t('company-discount', 'from_to', [
	        'from' => Yii::$app->formatter->asPercent($this->discount / 100),
            'to' => Yii::$app->formatter->asPercent($this->discount_to / 100)
        ]) : Yii::$app->formatter->asPercent($this->discount / 100);
		return $this->promocode.' - '.$discount;
	}

    /**
     * Get title
     * @return string
     */
    public function getDiscount_all() {
        return ($this->discount_to) ? Yii::t('company-discount', 'from_to', [
            'from' => Yii::$app->formatter->asPercent($this->discount / 100),
            'to' => Yii::$app->formatter->asPercent($this->discount_to / 100)
        ]) : Yii::$app->formatter->asPercent($this->discount / 100);
    }
	
	/**
	 * Get date start formatted
	 * @param string $format
	 * @return string
	 * @throws \yii\base\InvalidConfigException
	 */
	public function getDate_start($format = 'dd-MM-yyyy') {
		if (!$this->date_start_at)
			$this->date_start_at = time();
		return Yii::$app->formatter->asDate($this->date_start_at, $format);
	}
	
	/**
	 * Set date start
	 * @param $val
	 */
	public function setDate_start($val) {
		$val .= ' 00:00:00';
		$this->date_start_at = strtotime($val);
	}
	
	/**
	 * Get date end formatted
	 * @param string $format
	 * @return string
	 * @throws \yii\base\InvalidConfigException
	 */
	public function getDate_end($format = 'dd-MM-yyyy') {
		if (!$this->date_end_at)
			$this->date_end_at = time();
		return Yii::$app->formatter->asDate($this->date_end_at, $format);
	}
	
	/**
	 * Set date end
	 * @param $val
	 */
	public function setDate_end($val) {
		$val .= ' 23:59:59';
		$this->date_end_at = strtotime($val);
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
	 * @inheritdoc
	 */
	public function afterFind() {
		parent::afterFind();
		
		// Set tags
		if ($this->isRelationPopulated('tags'))
			$this->_tags_ids_old = $this->getTags_ids();
	}
	
	/**
	 * @inheritdoc
	 */
	public function afterSave($insert, $changedAttributes) {
		parent::afterSave($insert, $changedAttributes);
		
		// Update tags links
		TagModule::updateLinks($this->_tags_ids_old, $this->_tags_ids, self::moduleType(), $this->id, Type::NONE);
	}
}
