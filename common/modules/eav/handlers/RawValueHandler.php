<?php

namespace common\modules\eav\handlers;

/**
 * Class RawValueHandler
 * @package common\modules\eav
 */
class RawValueHandler extends ValueHandler
{
	/**
	 * @inheritdoc
	 */
	public function load() {
		$valueModel = $this->getValueModel();
		return $valueModel->value;
	}
	
	/**
	 * @inheritdoc
	 */
	public function defaultValue() {
		return false;
	}
	
	/**
	 * @inheritdoc
	 */
	public function save() {
		$EavModel = $this->attributeHandler->owner;
		$valueModel = $this->getValueModel();
		$attribute = $this->attributeHandler->getAttributeName();
		
		if (isset($EavModel->attributes[$attribute])) {
			
			$valueModel->value = $EavModel->attributes[$attribute];
			if (!$valueModel->save()) {
				throw new \Exception("Can't save value model");
			}
			
		}
	}
	
	public function getTextValue() {
		return $this->getValueModel()->value;
	}
}