<?php
namespace common\modules\contest;

use Yii;
use yii\base\Module as BaseModule;

use common\modules\telegram\models\TelegramCategory;

class Module extends BaseModule
{
	
	/**
	 * @var string module name
	 */
	public static $name = 'contest';
	
	/**
	 * @var string the namespace that controller classes are in.
	 * This namespace will be used to load controller classes by prepending it to the controller
	 * class name.
	 */
	public $controllerNamespace = 'common\modules\contest\controllers';
	
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
}
