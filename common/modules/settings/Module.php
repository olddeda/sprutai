<?php

namespace common\modules\settings;


use Yii;
use yii\base\BootstrapInterface;
use yii\base\Module as BaseModule;

/**
 * Class Module
 * @package common\modules\settings
 */
class Module extends BaseModule
{
    /**
     * @var string the default route of this module. Defaults to 'default'.
     * The route may consist of child module ID, controller ID, and/or action ID.
     * For example, `help`, `post/create`, `admin/post/create`.
     * If action ID is not given, it will take the default value as specified in
     * [[Controller::defaultAction]].
     */
    public $defaultRoute = 'default';

    /**
     * @var string the namespace that controller classes are in.
     * This namespace will be used to load controller classes by prepending it to the controller
     * class name.
     */
    public $controllerNamespace = 'common\modules\settings\controllers';

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
