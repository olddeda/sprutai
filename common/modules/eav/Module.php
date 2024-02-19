<?php

namespace common\modules\eav;

use Yii;

class Module extends \yii\base\Module
{
	public $controllerNamespace = 'common\modules\eav\controllers';
	
	public $defaultRoute = 'default';
	
	public function init() {
		parent::init();
		
		// Set module
		$this->setModule('admin', 'common\modules\eav\admin\Module');
	}
	
	public function createController($route) {
		if (strpos($route, 'admin/') !== false) {
			return $this->getModule('admin')->createController(str_replace('admin/', '', $route));
		}
		else {
			return parent::createController($route);
		}
		
	}
}
