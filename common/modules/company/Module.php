<?php
namespace common\modules\company;

use Yii;
use yii\base\Module as BaseModule;

/**
 * Class Module
 * @package comment\modules\company
 */
class Module extends BaseModule
{
	/**
	 * @var string the namespace that controller classes are in.
	 * This namespace will be used to load controller classes by prepending it to the controller
	 * class name.
	 */
    public $controllerNamespace = 'common\modules\company\controllers';
	
	/**
	 * @var array The rules to be used in URL management.
	 */
	public $urlRules = [
		'default/<id:\d+>' 											=> 'default/view',
		'default/<action:\w+>/<id:\d+>' 							=> 'default/<action>',
		'default/<action:\w+>' 										=> 'default/<action>',
		'<controller:\w+>/<company_id:\d+>/<id:\d+>' 				=> '<controller>/view',
		'<controller:\w+>/<company_id:\d+>/<action:\w+>/<id:\d+>' 	=> '<controller>/<action>',
		'<controller:\w+>/<company_id:\d+>/<action:\w+>' 			=> '<controller>/<action>',
	];
}
