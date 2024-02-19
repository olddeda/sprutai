<?php
namespace common\modules\project;

use Yii;
use yii\base\Module as BaseModule;

/**
 * Class Module
 * @package comment\modules\project
 */
class Module extends BaseModule
{
	/**
	 * @var string the namespace that controller classes are in.
	 */
	public $controllerNamespace = 'common\modules\project\controllers';

	/**
	 * Initializes the module.
	 *
	 * This method is called after the module is created and initialized with property values
	 * given in configuration. The default implementation will initialize [[controllerNamespace]]
	 * if it is not set.
	 *
	 * If you override this method, please make sure you call the parent implementation.
	 */
	public function init() {
		parent::init();
	}

	/**
	 * @return null|static
	 */
	public static function module() {
		return static::getInstance();
	}
}
