<?php
namespace common\modules\paste;

use Yii;
use yii\base\Module as BaseModule;

use common\modules\telegram\models\TelegramCategory;

class Module extends BaseModule
{
	
	/**
	 * @var string module name
	 */
	public static $name = 'paste';
	
	/**
	 * @var string the namespace that controller classes are in.
	 * This namespace will be used to load controller classes by prepending it to the controller
	 * class name.
	 */
	public $controllerNamespace = 'common\modules\paste\controllers';
	
	/**
	 * @var array The rules to be used in URL management.
	 */
	public $urlRules = [
		'<slug:\w+>' => 'default/view',
	];
}
