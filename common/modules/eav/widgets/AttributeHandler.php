<?php
namespace common\modules\eav\widgets;

use Yii;
use yii\base\InvalidParamException;
use yii\base\Widget;
use yii\db\ActiveRecord;

use common\modules\eav\EavModel;
use common\modules\eav\handlers\ValueHandler;

/**
 * Class AttributeHandler
 * @package common\modules\eav
 */
class AttributeHandler extends Widget
{
	/**
	 * value class
	 */
	const VALUE_HANDLER_CLASS = '\common\modules\eav\handlers\RawValueHandler';
	
	/**
	 * @var EavModel
	 */
	public $owner;
	
	/**
	 * @var ValueHandler
	 */
	public $valueHandler;
	
	/**
	 * @var ActiveRecord
	 */
	public $attributeModel;
	
	/**
	 * @var string
	 */
	public $nameField = 'name';
	
	/**
	 * @var string
	 */
	public $labelField = 'label';
	
	/**
	 * @var array
	 */
	public $options = [];
	
	/**
	 * @param EavModel $owner
	 * @param ActiveRecord $attributeModel
	 *
	 * @return AttributeHandler
	 * @throws \yii\base\InvalidConfigException
	 */
	public static function load($owner, $attributeModel) {
		if (!class_exists($class = $attributeModel->eavType->handlerClass)) {
			throw new InvalidParamException('Unknown handler class: '.$class);
		}
		
		$handler = Yii::createObject([
			'class' => $class,
			'owner' => $owner,
			'attributeModel' => $attributeModel
		]);
		$handler->init();
		
		return $handler;
	}
	
	/**
	 * @inheritdoc
	 */
	public function init() {
		$this->valueHandler = Yii::createObject([
			'class' => static::VALUE_HANDLER_CLASS,
			'attributeHandler' => $this,
		]);
	}
	
	/**
	 * @return string
	 */
	public function getAttributeName() {
		return (string)($this->attributeModel->{$this->nameField});
	}
	
	public function getAttributeLabel() {
		return (string)($this->attributeModel->{$this->labelField});
	}
	
	public function getOptions() {
		$result = [];
		foreach ($this->attributeModel->eavOptions as $option) {
			$result[] = $option->getPrimaryKey();
		}
		return $result;
	}
}
