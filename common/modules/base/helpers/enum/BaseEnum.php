<?php

namespace common\modules\base\helpers\enum;

use common\modules\base\components\Debug;
use ReflectionClass;

use Yii;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;

/**
 * Class BaseEnum
 * @package common\modules\base\helpers
 */
abstract class BaseEnum
{
	/**
	 * The cached list of constants by name.
	 *
	 * @var array
	 */
	private static $byName = [];

	/**
	 * The cached list of constants by value.
	 *
	 * @var array
	 */
	private static $byValue = [];

	/**
	 * The value managed by this type instance.
	 *
	 * @var mixed
	 */
	private $value;

	/**
	 * @var array list of properties
	 */
	private static $list;

	/**
	 * @var array list of exclude
	 */
	private static $exclude;

	/**
	 * @var string message category
	 */
	public static $messageCategory = 'base-enum';

    /**
     * Sets the value that will be managed by this type instance.
     *
     * @param mixed $value The value to be managed.
     *
     * @throws BadRequestHttpException If the value is not valid.
     * @throws \ReflectionException
     */
	public function __construct($value) {
		if (!self::isValidValue($value)) {
			throw new BadRequestHttpException;
		}
		$this->value = $value;
	}

    /**
     * Creates a new type instance for a called name.
     *
     * @param string $name The name of the value.
     * @param array $arguments An ignored list of arguments.
     *
     * @return $this The new type instance.
     * @throws BadRequestHttpException
     * @throws \ReflectionException
     */
	public static function __callStatic($name, array $arguments = []) {
		return self::createByName($name);
	}

    /**
     * Creates a new type instance using the name of a value.
     *
     * @param string $name The name of a value.
     *
     * @return $this The new type instance.
     *
     * @throws \ReflectionException
     * @throws \yii\web\BadRequestHttpException
     */
	public static function createByName($name) {
		$constants = self::getConstantsByName();
		if (!array_key_exists($name, $constants)) {
			throw new BadRequestHttpException;
		}

		return new static($constants[$name]);
	}

    /**
     * get constant key by value(label)
     *
     * @param $value
     *
     * @return mixed
     * @throws \ReflectionException
     */
	public static function getValueByName($value) {
		$list = self::listData();

		return array_search($value, $list);
	}

    /**
     * Creates a new type instance using the value.
     *
     * @param mixed $value The value.
     *
     * @return $this The new type instance.
     *
     * @throws \ReflectionException
     * @throws \yii\web\BadRequestHttpException
     */
	public static function createByValue($value) {
		$constants = self::getConstantsByValue();
		if (!array_key_exists($value, $constants)) {
			throw new BadRequestHttpException;
		}
		return new static($value);
	}

    /**
     * List data
     * @static
     *
     * @param array $include
     * @param array $exclude
     *
     * @return mixed
     * @throws \ReflectionException
     */
	public static function listData($include = [], $exclude = []) {
		$class = get_called_class();

		if (!isset(self::$list[$class])) {
			$reflection = new ReflectionClass($class);
			$includeList = ArrayHelper::merge($reflection->getStaticPropertyValue('list'), $include);
			$excludeList = ArrayHelper::merge($reflection->getStaticPropertyValue('exclude'), $exclude);
			self::$list[$class] = array_diff_key($includeList, array_flip($excludeList));
		}

		$result = ArrayHelper::getColumn(self::$list[$class], function ($value) {
			$class = get_called_class();
			return Yii::t($class::$messageCategory, $value);
		});

		return $result;
	}

    /**
     * List data by key/val
     *
     * @param string $key
     * @param string $val
     *
     * @return array
     * @throws \ReflectionException
     */
	public static function listDataKeyVal($key = 'id', $val = 'title') {
		$tmp = [];
		foreach (self::listData() as $dataKey => $dataVal) {
			$tmp[] = [
				$key => $dataKey,
				$val => $dataVal,
			];
		}
		return $tmp;
	}

	/**
	 * Get label by value
	 * @var string value
	 * @return string label
	 */
	public static function getLabel($value) {
		$list = static::$list;
		if (isset($list[$value])) {
			return Yii::t(static::$messageCategory, $list[$value]);
		}
		return null;
	}

	/**
	 * Get item by value
	 * @var string value
	 * @return string label
	 */
	public static function getItem($value) {
		$list = static::$list;
		if (isset($list[$value])) {
			return $list[$value];
		}
		return null;
	}
	
	/**
	 * Get values
	 * @return array
	 */
	public static function getValues() {
		return array_keys(static::$list);
	}

    /**
     * Returns the list of constants (by name) for this type.
     *
     * @return array The list of constants by name.
     * @throws \ReflectionException
     */
	public static function getConstantsByName() {
		$class = get_called_class();
		if (!isset(self::$byName[$class])) {
			$reflection = new ReflectionClass($class);
			self::$byName[$class] = $reflection->getConstants();
			while (false !== ($reflection = $reflection->getParentClass())) {
				if (__CLASS__ === $reflection->getName()) {
					break;
				}
				self::$byName[$class] = array_replace($reflection->getConstants(), self::$byName[$class]);
			}
		}

		return self::$byName[$class];
	}

    /**
     * Returns the list of constants (by value) for this type.
     *
     * @return array The list of constants by value.
     * @throws \ReflectionException
     */
	public static function getConstantsByValue() {
		$class = get_called_class();
		if (!isset(self::$byValue[$class])) {
			self::getConstantsByName();
			self::$byValue[$class] = [];
			foreach (self::$byName[$class] as $name => $value) {
				if (array_key_exists($value, self::$byValue[$class])) {
					if (!is_array(self::$byValue[$class][$value])) {
						self::$byValue[$class][$value] = [
							self::$byValue[$class][$value]
						];
					}
					self::$byValue[$class][$value][] = $name;;
				} else {
					self::$byValue[$class][$value] = $name;
				}
			}
		}

		return self::$byValue[$class];
	}

    /**
     * Returns the name of the value.
     *
     * @return array|string The name, or names, of the value.
     * @throws \ReflectionException
     */
	public function getName() {
		$constants = self::getConstantsByValue();
		return $constants[$this->value];
	}

	/**
	 * Unwraps the type and returns the raw value.
	 *
	 * @return mixed The raw value managed by the type instance.
	 */
	public function getValue() {
		return $this->value;
	}

    /**
     * Checks if a name is valid for this type.
     *
     * @param string $name The name of the value.
     *
     * @return boolean If the name is valid for this type, `true` is returned.
     * Otherwise, the name is not valid and `false` is returned.
     * @throws \ReflectionException
     */
	public static function isValidName($name) {
		$constants = self::getConstantsByName();
		return array_key_exists($name, $constants);
	}

    /**
     * Checks if a value is valid for this type.
     *
     * @param string $value The value.
     *
     * @return boolean If the value is valid for this type, `true` is returned.
     * Otherwise, the value is not valid and `false` is returned.
     * @throws \ReflectionException
     */
	public static function isValidValue($value) {
		$constants = self::getConstantsByValue();
		return array_key_exists($value, $constants);
	}
}