<?php
namespace common\modules\plugin;

use Yii;
use yii\base\Module as BaseModule;

/**
 * Class Module
 * @package comment\modules\plugin
 */
class Module extends BaseModule
{
	/** @var array The rules to be used in URL management. */
	public $urlRules = [
		'instruction/<action:\w+>/<plugin_id:\d+>/<id:\d+>' 	=> 'instruction/<action>',
		'instruction/<action:\w+>/<plugin_id:\d+>' 				=> 'instruction/<action>',
		'version/<action:\w+>/<plugin_id:\d+>/<id:\d+>' 		=> 'version/<action>',
		'version/<action:\w+>/<plugin_id:\d+>' 					=> 'version/<action>',
		'payer/<action:\w+>/<plugin_id:\d+>/<id:\d+>' 			=> 'payer/<action>',
		'payer/<action:\w+>/<plugin_id:\d+>' 					=> 'payer/<action>',
		'version/<plugin_id:\d+>/select/provider'				=> 'version/select-provider',
		'version/<plugin_id:\d+>/select/provider/<id:\d+>'		=> 'version/select-provider',
		'version/<plugin_id:\d+>/select/repository'				=> 'version/select-repository',
		'version/<plugin_id:\d+>/select/repository/<id:\d+>'	=> 'version/select-repository',
		'version/<plugin_id:\d+>/select/release'				=> 'version/select-release',
		'version/<plugin_id:\d+>/select/release/<id:\d+>'		=> 'version/select-release',
	];
	
	/**
	 * @var string the namespace that controller classes are in.
	 */
	public $controllerNamespace = 'common\modules\plugin\controllers';

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
