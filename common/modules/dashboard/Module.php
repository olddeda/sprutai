<?php
namespace common\modules\dashboard;

use Yii;
use yii\base\Module as BaseModule;

/**
 * Class Module
 * @package comment\modules\dashboard
 */
class Module extends BaseModule
{
	/**
	 * @inheritdoc
	 */
	public $controllerNamespace = 'common\modules\dashboard\controllers';
	
	/**
	 * @var array
	 */
	public $widgets = [];

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
