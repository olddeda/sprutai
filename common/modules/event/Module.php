<?php
namespace common\modules\event;

use Yii;
use yii\base\Module as BaseModule;

/**
 * Class Module
 * @package comment\modules\lookup
 */
class Module extends BaseModule
{
	/**
	 * @var string the namespace that controller classes are in.
	 * This namespace will be used to load controller classes by prepending it to the controller
	 * class name.
	 */
    public $controllerNamespace = 'common\modules\event\controllers';
}
