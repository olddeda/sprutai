<?php

namespace common\modules\base;

use Yii;
use yii\base\Module as BaseModule;

class Module extends BaseModule
{
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
