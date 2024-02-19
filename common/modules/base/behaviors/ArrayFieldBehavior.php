<?php
namespace common\modules\base\behaviors;

use common\modules\base\components\Debug;
use yii\base\Behavior;
use yii\db\BaseActiveRecord;
use yii\helpers\Json;

/**
 * Class ArrayFieldBehavior
 *
 * ~~~
 * use app\modules\base\behaviors;
 *
 * public function behaviors()
 * {
 *     return [
 *         [
 *             'class' => ArrayFieldBehavior::className(),
 *             'attributes' => ['attribute1', 'attribute2'],
 *             'defaultEncodedValue' => 'some value',
 *             'defaultDecodedValue' => 'some value',
 *         ],
 *     ];
 * }
 * ~~~
 *
 * @property BaseActiveRecord $owner
 */
class ArrayFieldBehavior extends Behavior {
	/**
	 * @var array
	 */
	public $attribute = null;
	
	/**
	 * @var mixed
	 */
	public $defaultEncodedValue = null;
	
	/**
	 * @var mixed
	 */
	public $defaultDecodedValue = [];
	
	/**
	 * @var array
	 */
	private $_cache = [];
	
	/**
	 * @var array
	 */
	private $_oldAttribute = null;
	
	/**
	 * @inheritdoc
	 */
	public function events() {
		return [
			BaseActiveRecord::EVENT_AFTER_FIND => 'decode',
			BaseActiveRecord::EVENT_AFTER_INSERT => 'decode',
			BaseActiveRecord::EVENT_AFTER_UPDATE => 'decode',
			BaseActiveRecord::EVENT_BEFORE_UPDATE => 'encode',
			BaseActiveRecord::EVENT_BEFORE_INSERT => 'encode',
		];
	}
	
	/**
	 * Encode attributes
	 */
	public function encode() {
		if ($this->_oldAttribute) {
			$this->owner->setOldAttribute($this->attribute, $this->_oldAttribute);
		}
		
		$value = $this->owner->getAttribute($this->attribute);
		$this->_cache[$this->attribute] = $value;
		
		$value = !empty($value) ? Json::encode($value) : $this->defaultEncodedValue;
		$this->owner->setAttribute($this->attribute, $value);
	}
	
	/**
	 * Decode attributes
	 */
	public function decode() {
		if (isset($this->_cache[$this->attribute])) {
			$value = $this->_cache[$this->attribute];
		}
		else {
			$value = Json::decode($this->owner->getAttribute($this->attribute));
		}
		
		$value = !empty($value) ? $value : $this->defaultDecodedValue;
		$this->owner->setAttribute($this->attribute, $value);
		
		if (!$this->owner->getIsNewRecord()) {
			$this->_oldAttribute = $this->owner->getOldAttribute($this->attribute);
			$this->owner->setOldAttribute($this->attribute, $value);
		}
		
		$this->_cache = [];
	}
	
	/**
	 * Set value
	 * @param $key
	 * @param $val
	 * @param bool $save
	 */
	public function setData($key, $val, $save = true) {
		$tmp = $this->owner->getAttribute($this->attribute);
		$tmp[$key] = $val;
		$this->owner->setAttribute($this->attribute, $tmp);
		if ($save) {
			$this->owner->save();
		}
	}
	
	/**
	 * Get value
	 * @param $val
	 * @param null $defaultValue
	 *
	 * @return null
	 */
	public function getData($val, $defaultValue = null) {
		$tmp = $this->owner->getAttribute($this->attribute);
		return (isset($tmp[$val])) ? $tmp[$val] : $defaultValue;
	}
}