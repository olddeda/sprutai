<?php
namespace common\modules\base\extensions\base;

use Yii;
use yii\base\Exception;

trait ModuleTrait
{
	/** @var array */
	private $_module;
	
	/**
	 * Get module
	 * @var string module name
	 * @return mixed
	 * @throws Exception
	 */
	protected function getModuleInstance($name = null) {
		if (!$name)
			$name = Yii::$app->controller->module->id;
		
		if ($this->_module == null)
			$this->_module = \Yii::$app->getModule($name);
		
		if (!$this->_module)
			throw new Exception($name." module not found, may be you didn't add it to your config?");
		
		return $this->_module;
	}
}