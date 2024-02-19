<?php
namespace api\modules\v1;

use yii\base\Module as BaseModule;
use yii\base\BootstrapInterface;

class Module extends BaseModule
{
	/**
	 * @inheritDoc
	 */
	public $controllerNamespace = 'api\modules\v1\controllers';
	
	/**
	 * @inheritDoc
	 */
	public function init() {
		parent::init();
	}
}
