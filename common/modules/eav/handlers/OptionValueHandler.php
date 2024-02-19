<?php
namespace common\modules\eav\handlers;

/**
 * Class OptionValueHandler
 * @package common\modules\eav
 */
class OptionValueHandler extends ValueHandler
{
	/**
	 * @inheritdoc
	 */
	public function load() {
		$valueModel = $this->getValueModel();
		
		return $valueModel->optionId;
	}
	
	/**
	 * @inheritdoc
	 */
	public function save() {
		$EavModel = $this->attributeHandler->owner;
		$valueModel = $this->getValueModel();
		$attribute = $this->attributeHandler->getAttributeName();
		
		if (isset($EavModel->attributes[$attribute])) {
			
			$valueModel->optionId = $EavModel->attributes[$attribute];
			
			if (!$valueModel->save()) {
				throw new \Exception("Can't save value model");
			}
			
		}
		
	}
	
	/**
	 * @inheritdoc
	 */
	public function defaultValue() {
		$defaultOptions = [];
		
		foreach ($this->attributeHandler->attributeModel->eavOptions as $option) {
			if ($option->defaultOptionId === 1) {
				$defaultOptions[] = $option->id;
			}
		}
		
		return $defaultOptions;
	}
	
	public function getTextValue() {
		return $this->getValueModel()->option->value;
	}
}