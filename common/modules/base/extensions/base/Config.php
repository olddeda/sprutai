<?php
namespace common\modules\base\extensions\base;

use Yii;
use yii\base\InvalidConfigException;

/**
 * Global configuration helper class for widgets
 *
 * @since 1.0
 */
class Config
{
	const VENDOR_NAME = "appmake/";
	const NAMESPACE_PREFIX = "\\appmake\\widget\\";
	const DEFAULT_REASON = "for your selected functionality";

	protected static $_validHtmlInputs = [
		'hiddenInput',
		'textInput',
		'passwordInput',
		'textArea',
		'checkbox',
		'radio',
		'listBox',
		'dropDownList',
		'checkboxList',
		'radioList',
		'input',
		'fileInput'
	];

	protected static $_validDropdownInputs = [
		'listBox',
		'dropDownList',
		'checkboxList',
		'radioList'
	];

	protected static $_validInputWidgets = [
		'\appmake\widget\typeahead\Typeahead' => [
			'yii2-widgets',
			'yii2-widget-typeahead'
		],
		'\appmake\widget\select2\Select2' => [
			'yii2-widgets',
			'yii2-widget-select2'
		],
		'\appmake\widget\depdrop\DepDrop' => [
			'yii2-widgets',
			'yii2-widget-depdrop'
		],
		'\appmake\widget\touchspin\TouchSpin' => [
			'yii2-widgets',
			'yii2-widget-touchspin'
		],
		'\appmake\widget\switchinput\SwitchInput' => [
			'yii2-widgets',
			'yii2-widget-switchinput'
		],
		'\appmake\widget\rating\StarRating' => [
			'yii2-widgets',
			'yii2-widget-rating'
		],
		'\appmake\widget\file\FileInput' => [
			'yii2-widgets',
			'yii2-widget-fileinput'
		],
		'\appmake\widget\range\RangeInput' => [
			'yii2-widgets',
			'yii2-widget-rangeinput'
		],
		'\appmake\widget\color\ColorInput' => [
			'yii2-widgets',
			'yii2-widget-colorinput'
		],
		'\appmake\widget\date\DatePicker' => [
			'yii2-widgets',
			'yii2-widget-datepicker'
		],
		'\appmake\widget\time\TimePicker' => [
			'yii2-widgets',
			'yii2-widget-timepicker'
		],
		'\appmake\widget\datetime\DateTimePicker' => [
			'yii2-widgets',
			'yii2-widget-datetimepicker'
		],
		'\appmake\widget\daterange\DateRangePicker' => 'yii2-daterange',
		'\appmake\widget\sortinput\SortableInput' => 'yii2-sortinput',
		'\appmake\widget\money\MaskMoney' => 'yii2-money',
		'\appmake\widget\checkbox\CheckboxX' => 'yii2-checkbox',
		'\appmake\widget\slider\Slider' => 'yii2-slider',
	];

	/**
	 * Validate multiple extension dependencies
	 *
	 * @param array $extensions the configuration of extensions with each array
	 * item setup as required in `checkDependency` method. The following keys
	 * can be setup:
	 * - name: string, the extension class name (without vendor namespace prefix)
	 * - repo: string, the extension package repository name (without vendor name prefix)
	 * - reason: string, a user friendly message for dependency validation failure
	 *
	 * @throws InvalidConfigException if extension fails dependency validation
	 */
	public static function checkDependencies($extensions = []) {
		foreach ($extensions as $extension) {
			$name = empty($extension[0]) ? '' : $extension[0];
			$repo = empty($extension[1]) ? '' : $extension[1];
			$reason = empty($extension[2]) ? '' : self::DEFAULT_REASON;
			static::checkDependency($name, $repo, $reason);
		}
	}

	/**
	 * Validate a single extension dependency
	 *
	 * @param string $name the extension class name (without vendor namespace prefix)
	 * @param mixed $repo the extension package repository names (without vendor name prefix)
	 * @param string $reason a user friendly message for dependency validation failure
	 *
	 * @throws InvalidConfigException if extension fails dependency validation
	 */
	public static function checkDependency($name = '', $repo = '', $reason = self::DEFAULT_REASON) {
		if (empty($name)) {
			return;
		}
		$command = "php composer.phar require ".self::VENDOR_NAME;
		$version = ": \"@dev\"";
		$class = (substr($name, 0, 8) == self::NAMESPACE_PREFIX) ? $name : self::NAMESPACE_PREFIX.$name;

		if (is_array($repo)) {
			$repos = "one of '".implode("' OR '", $repo)."' extensions. ";
			$installs = $command.implode("{$version}\n\n--- OR ---\n\n{$command}", $repo).$version;
		}
		else {
			$repos = "the '".$repo."' extension. ";
			$installs = $command.$repo.$version;
		}

		if (!class_exists($class)) {
			throw new InvalidConfigException("The class '{$class}' was not found and is required {$reason}.\n\n"."Please ensure you have installed {$repos}"."To install, you can run this console command from your application root:\n\n{$installs}");
		}
	}

	/**
	 * Gets list of namespaced Krajee input widget classes as an associative
	 * array, where the array keys are the namespaced classes, and the array
	 * values are the names of the github repository to which these classes
	 * belong to.
	 *
	 * @returns array
	 */
	public static function getInputWidgets() {
		return static::$_validInputWidgets;
	}

	/**
	 * Check if a type of input is any possible valid input (html or widget)
	 *
	 * @param string $type the type of input
	 *
	 * @returns bool
	 */
	public static function isValidInput($type) {
		return static::isHtmlInput($type) || static::isInputWidget($type) || $type === 'widget';
	}

	/**
	 * Check if a input type is a valid Html Input
	 *
	 * @param string $type the type of input
	 *
	 * @returns bool
	 */
	public static function isHtmlInput($type) {
		return in_array($type, static::$_validHtmlInputs);
	}

	/**
	 * Check if a type of input is a valid input widget
	 *
	 * @param string $type the type of input
	 *
	 * @returns bool
	 */
	public static function isInputWidget($type) {
		return isset(static::$_validInputWidgets[$type]);
	}

	/**
	 * Check if a input type is a valid dropdown input
	 *
	 * @param string $type the type of input
	 *
	 * @returns bool
	 */
	public static function isDropdownInput($type) {
		return in_array($type, static::$_validDropdownInputs);
	}

	/**
	 * Check if a namespaced widget is valid or installed.
	 *
	 * @throws InvalidConfigException
	 */
	public static function validateInputWidget($type, $reason = self::DEFAULT_REASON) {
		if (static::isInputWidget($type)) {
			static::checkDependency($type, static::$_validInputWidgets[$type], $reason);
		}
	}

	/**
	 * Convert a language string in yii\i18n format to
	 * a ISO-639 format (2 or 3 letter code).
	 *
	 * @param string $language the input language string
	 *
	 * @return string
	 */
	public static function getLang($language) {
		$pos = strpos($language, "-");
		return $pos > 0 ? substr($language, 0, $pos) : $language;
	}

	/**
	 * Get the current directory of the extended class object
	 *
	 * @param mixed $object the called object instance
	 *
	 * @return string
	 */
	public static function getCurrentDir($object) {
		if (empty($object)) {
			return '';
		}
		$child = new \ReflectionClass($object);
		return dirname($child->getFileName());
	}

	/**
	 * Check if a file exists
	 *
	 * @param string $file the file with path in URL format
	 *
	 * @return bool
	 */
	public static function fileExists($file) {
		$file = str_replace('/', DIRECTORY_SEPARATOR, $file);
		return file_exists($file);
	}

	/**
	 * Gets the module
	 *
	 * @param string $m the module name
	 *
	 * @return Module
	 */
	public static function getModule($name) {
		$module = Yii::$app->controller->module;
		return $module && $module->getModule($name) ? $module->getModule($name) : Yii::$app->getModule($name);
	}

	/**
	 * Initializes and validates the module
	 *
	 * @param string $class the Module class name
	 *
	 * @return \yii\base\Module
	 *
	 * @throws InvalidConfigException
	 */
	public static function initModule($class) {
		$name = $class::MODULE;
		$module = $name ? static::getModule($name) : null;
		if ($module === null || !$module instanceof $class) {
			throw new InvalidConfigException("The '{$name}' module MUST be setup in your Yii configuration file and must be an instance of '{$class}'.");
		}
		return $module;
	}
}
