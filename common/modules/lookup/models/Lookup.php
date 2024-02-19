<?php
namespace common\modules\lookup\models;

use Yii;
use yii\caching\DbDependency;
use yii\helpers\ArrayHelper;

use common\modules\base\components\ActiveRecord;
use common\modules\base\helpers\enum\Status;

use common\modules\lookup\models\query\LookupQuery;

/**
 * This is the model class for table "{{%lookup}}".
 *
 * @property integer $id
 * @property integer $type
 * @property integer $parent_id
 * @property integer $code
 * @property integer $sequence
 * @property string $title
 * @property string $text
 * @property integer $flag
 * @property integer $status
 * @property integer $created_by
 * @property integer $updated_by
 * @property integer $created_at
 * @property integer $updated_at
 */
class Lookup extends ActiveRecord
{
	const TYPE_NONE						= 0;
	const TYPE_COUNTRY					= 1;
	const TYPE_CITY						= 2;
	
	const TYPE_RAPID_ASSESSMENT_REASON	= 10;
	
	const SEQUENCE_STEP					= 10;
	
	static private $_types = [
		self::TYPE_COUNTRY	=> [
			'name' => 'country',
			'use' => [
				'avatar' => false,
				'parent_id' => false,
				'code' => false,
				'text' => false,
				'childs' => [
					self::TYPE_CITY => [
						'name' => 'city'
					]
				]
			],
		],
		self::TYPE_CITY	=> [
			'name' => 'city',
			'use' => [
				'avatar' => false,
				'parent_id' => true,
				'code' => false,
				'text' => false,
			],
		],
		self::TYPE_RAPID_ASSESSMENT_REASON	=> [
			'name' => 'rapid_assessment_reason',
			'use' => [
				'avatar' => false,
				'parent_id' => false,
				'code' => false,
				'text' => false,
			],
		],
	];
	
	private static $_items			= [];
	
	/**
	 * @return int
	 */
	public static function type() {
		return self::TYPE_NONE;
	}
	
	/**
	 * @param array $row
	 *
	 * @return Lookup|InvalidConfigException
	 */
	public static function instantiate($row) {
		switch ($row['type']) {
			case self::TYPE_COUNTRY:
				return new Country();
			case self::TYPE_CITY:
				return new City();
			default:
				return new self();
		}
	}
	
	/**
	 * @inheritdoc
	 */
	public function init() {
		
		// Set type
		$this->type = static::type();
		
		parent::init();
	}

	/**
	 * @inheritdoc
	 */
	public static function tableName() {
		return '{{%lookup}}';
	}
	
	/**
	 * Returns a list of behaviors that this component should behave as.
	 *
	 * @return array the behavior configurations.
	 */
	public function behaviors() {
		return ArrayHelper::merge(parent::behaviors(), []);
	}

	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[['title', 'type', 'status'], 'required'],
			[['type', 'parent_id', 'code','sequence', 'flag', 'status', 'created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
			[['title'], 'string', 'max' => 255],
			[['text'], 'string', 'max' => 10000],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels() {
		return [
			'id' => Yii::t('lookup', 'field_id'),
			'type' => Yii::t('lookup', 'field_type'),
			'parent_id' => Yii::t('lookup', 'field_parent_id'),
			'code' => Yii::t('lookup', 'field_code'),
			'sequence' => Yii::t('lookup', 'field_sequence'),
			'title' => Yii::t('lookup', 'field_title'),
			'text' => Yii::t('lookup', 'field_text'),
			'flag' => Yii::t('lookup', 'field_flash'),
			'status' => Yii::t('lookup', 'field_status'),
			'created_by' => Yii::t('lookup', 'field_created_by'),
			'updated_by' => Yii::t('lookup', 'field_updated_by'),
			'created_at' => Yii::t('lookup', 'field_created_at'),
			'updated_at' => Yii::t('lookup', 'field_updated_at'),
		];
	}
	
	/**
	 * @inheritdoc
	 * @return \common\modules\store\models\query\StoreQuery the active query used by this AR class.
	 */
	public static function find() {
		return new LookupQuery(get_called_class(), ['type' => static::type()]);
	}
	
	/**
	 * Get types
	 * @return array
	 */
	public static function types($type = null, $localized = true) {
		$tmp = [];
		foreach (self::$_types as $typeId => $typeArr)
			$tmp[$typeId] = ($localized) ? Yii::t('lookup', 'type_'.$typeArr['name']) : $typeArr['name'];
		if ($type !== null)
			return (isset($tmp[$type])) ? $tmp[$type] : Yii::t('lookup', 'type_none');
		return $tmp;
	}
	
	/**
	 * @return array
	 */
	public static function typesConfig() {
		return self::$_types;
	}
	
	/**
	 * Returns the items for the specified type.
	 * @param integer $type
	 * @param string $sort
	 * @param array $includeItems
	 * @param array $excludeItems
	 * @return array items
	 */
	public static function items($type, $sort = 'sequence', $includeItems = [], $excludeItems = []) {
		if (!isset(self::$_items[$type]))
			self::_loadItems($type, $sort);
		
		$items = self::$_items[$type][$sort];
		
		$result = [];
		if ($items) {
			foreach ($items as $id => $val) {
				$itemOrig = self::item($type, $id);
				if (!in_array($itemOrig, $excludeItems))
					$result[$id] = $items[$id];
				if (in_array($itemOrig, $includeItems))
					$result[$id] = $items[$id];
			}
		}
		return $result;
	}
	
	/**
	 * Returns the item name for the specified type and id.
	 * @param integer $type
	 * @param integer $id
	 * @param string $sort
	 * @return string the item name for the specified the id. Null is returned if the item type or code does not exist.
	 */
	public static function item($type, $id, $sort = 'sequence') {
		if (!isset(self::$_items[$type]))
			self::_loadItems($type, $sort);
		return isset(self::$_items[$type][$sort][$id]) ? self::$_items[$type][$sort][$id] : null;
	}
	
	/**
	 * Return last sequence
	 * @param $type
	 *
	 * @return int
	 */
	public static function lastSequence_($type) {
		$row = self::find()->select('MAX(sequence) as max')->andWhere('type = :type AND status != :status_temp', [
			'type' => $type,
			'status_temp' => Status::TEMP,
		])->asArray()->one();
		return ($row && $row['max']) ? $row['max'] + self::SEQUENCE_STEP : 1;
	}
	
	/**
	 * List all last sequences
	 * @return mixed
	 */
	public static function lastSequences() {
		$dependency = new DbDependency();
		$dependency->sql = 'SELECT MAX(updated_at) FROM '.self::tableName();
		
		return self::getDb()->cache(function ($db) {
			$result = [];
			
			// Create query
			$query = self::find();
			$query->select('type, MAX(sequence) AS max');
			$query->groupBy('type');
			
			// Get rows
			$rows = $query->asArray()->all();
			$tmp = [];
			if ($rows) {
				foreach ($rows as $row) {
					if ($row['type'])
						$tmp[$row['type']] = ($row['max']) ? $row['max'] + self::SEQUENCE_STEP : 1;
				}
			}
			
			foreach (self::$_types as $typeId => $typeArr)
				$result[$typeId] = (isset($tmp[$typeId])) ? $tmp[$typeId] : 1;
			
			return $result;
			
		}, Yii::$app->params['cache.duration'], $dependency);
	}
	
	/**
	 * Return last code
	 * @param $type
	 *
	 * @return int
	 */
	public static function lastCode($type) {
		$row = self::find()->select('MAX(code) as max')->andWhere('type = :type AND status != :status_temp', [
			'type' => $type,
			'status_temp' => Status::TEMP,
		])->asArray()->one();
		return ($row && $row['max']) ? $row['max'] + 1 : 1;
	}
	
	/**
	 * Check use field
	 * @param $type
	 * @param $field
	 *
	 * @return bool
	 */
	public static function isUseField($type, $field) {
		if (isset(self::$_types[$type]) && isset(self::$_types[$type]['use']) && isset(self::$_types[$type]['use'][$field]))
			return self::$_types[$type]['use'][$field];
		return false;
	}
	
	/**
	 * Check has child
	 * @return boolean
	 */
	public function getHasChild() {
		if (isset(self::$_types[$this->type]) && isset(self::$_types[$this->type]['childs']))
			return true;
		return false;
	}

	/**
	 * Loads the lookup items for the specified type from the database.
	 * @param string the item type
	 */
	private static function _loadItems($type, $sort) {
		self::$_items[$type][$sort] = [];
		
		// Create depedency
		$dependency = new DbDependency;
		$dependency->sql = 'SELECT MAX(updated_at) FROM '.self::tableName();
		
		// Find models
		$models = self::getDb()->cache(function ($db) use($type, $sort) {
			return self::find()->where([
				'type' => $type,
				'status' => Status::ENABLED,
			])->orderBy([
				$sort => SORT_ASC,
			])->all();
		}, Yii::$app->params['cache.duration'], $dependency);
		
		if ($models) {
			foreach ($models as $model) {
				self::$_items[$type][$sort][$model->id] = $model->title;
			}
		}
	}
	
	/**
	 * @inheritdoc
	 */
	public function beforeSave($insert) {
		
		if ($this->status != Status::TEMP) {
			
			// Set parent_id
			if (!$this->parent_id)
				$this->parent_id = 0;
			
			// Set code
			if (!$this->code)
				$this->code = self::lastCode($this->type);
			
			// Set sequence
			if (!$this->sequence) {
				$this->sequence = self::lastSequence(['type' => $this->type, 'parent_id' => $this->parent_id]);
			}
		}
		
		return parent::beforeSave($insert);
	}
	
}
