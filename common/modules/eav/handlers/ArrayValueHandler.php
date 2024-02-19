<?php

namespace common\modules\eav\handlers;

/**
 * Class RawValueHandler
 * @package common\modules\eav
 */
class ArrayValueHandler extends RawValueHandler
{
	/**
	 * @inheritdoc
	 */
	public function load() {
		$value = parent::load();
		return json_decode($value, true);
	}
	
	/**
	 * @inheritdoc
	 */
	public function defaultValue() {
		$type = $this->attributeHandler->attributeModel->eavType;
		
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
			
			$value = $EavModel->attributes[$attribute];
			$valueModel->value = json_encode($value);
			
			if (!$valueModel->save()) {
				throw new \Exception("Can't save value model");
			}
			
		}
	}
	
	public function getTextValue() {
		return json_encode(parent::getTextValue());
	}
	
	public function getArrayValue() {
		return json_decode(parent::getTextValue());
	}
}