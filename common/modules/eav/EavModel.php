<?php

namespace common\modules\eav;

use Yii;
use yii\base\DynamicModel as BaseEavModel;
use yii\db\ActiveRecord;
use yii\widgets\ActiveForm;

use common\modules\eav\widgets\AttributeHandler;
use common\modules\eav\handlers\ValueHandler;

/**
 * Class EavModel
 * @package common\modules\eav
 */
class EavModel extends BaseEavModel
{
	/** @var string Class to use for storing data */
	public $valueClass;
	
	/** @var ActiveRecord */
	public $entityModel;
	
	/** @var AttributeHandler[] */
	public $handlers;
	
	/** @var string */
	public $attribute = '';
	
	/** @var ActiveForm */
	public $activeForm;
	
	/** @var string[] */
	private $attributeLabels = [];
	
	/**
	 * Constructor for creating form model from entity object
	 *
	 * @param array $params
	 *
	 * @return static
	 */
	public static function create($params) {
		$params['class'] = static::className();
		
		/** @var static $model */
		$model = Yii::createObject($params);
		
		$params = [];
		
		if (!empty($params['attribute'])) {
			$params['name'] = $params['attribute'];
		}
		
		foreach ($model->entityModel->getEavAttributes()->andWhere($params)->all() as $attribute) {
			$handler = AttributeHandler::load($model, $attribute);
			$attribute_name = $handler->getAttributeName();
			
			//
			// Add rules
			//
			if ($attribute->eavType->storeType == ValueHandler::STORE_TYPE_RAW) {
				$model->addRule($attribute_name, 'default', ['value' => $attribute->defaultValue]);
			}
			
			if ($attribute->eavType->storeType == ValueHandler::STORE_TYPE_OPTION) {
				$model->addRule($attribute_name, 'default', ['value' => $attribute->defaultOptionId]);
			}
			
			if ($attribute->eavType->storeType == ValueHandler::STORE_TYPE_ARRAY) {
				$model->addRule($attribute_name, 'string');
			}
			
			if ($attribute->required) {
				$model->addRule($attribute_name, 'required');
			}
			else {
				$model->addRule($attribute_name, 'safe');
			}
			
			//
			// Load attribute value
			//
			$value = $handler->valueHandler->load();
			if (!$value) {
				
				// Set default attribute
				$value = $handler->valueHandler->defaultValue();
			}
			
			$model->defineAttribute($attribute_name, $value);
			
			//
			// Add widget handler
			//
			$model->handlers[$attribute_name] = $handler;
			
		}
		
		//
		// Set POST data
		//
		if (Yii::$app->request->isPost && Yii::$app->request->getIsConsoleRequest() == false) {
			$modelName = self::getModelShortName($model->entityModel);
			$post = Yii::$app->request->post($modelName);
			$model->load($post, 'EavModel');
		}
		
		return $model;
	}
	
	/**
	 * @inheritdoc
	 */
	public function getAttributeLabels() {
		return $this->attributeLabels;
	}
	
	public function setLabel($name, $label) {
		$this->attributeLabels[$name] = $label;
	}
	
	public function save($runValidation = true, $attributes = null) {
		if (!$this->handlers) {
			Yii::info(Yii::t('eav', 'Dynamic model data were no attributes.'), __METHOD__);
			
			return false;
		}
		
		if ($runValidation && !$this->validate($attributes)) {
			Yii::info(Yii::t('eav', 'Dynamic model data were not save due to validation error.'), __METHOD__);
			
			return false;
		}
		
		$db = $this->entityModel->getDb();
		
		$transaction = $db->beginTransaction();
		try {
			foreach ($this->handlers as $handler) {
				$handler->valueHandler->save();
			}
			$transaction->commit();
		} catch (\Exception $e) {
			$transaction->rollBack();
			throw $e;
		}
	}
	
	public function __set($name, $value) {
		$this->defineAttribute($name, $value);
	}
	
	public function getValue() {
		if (isset($this->attributes[$this->attribute])) {
			return $this->attributes[$this->attribute];
		}
		else {
			return '';
		}
		
	}
	
	public function __toString() {
		if (isset($this->attributes[$this->attribute])) {
			if (is_string($this->attributes[$this->attribute])) {
				return (string)$this->attributes[$this->attribute];
			}
			else {
				return (string)json_encode($this->attributes[$this->attribute]);
			}
		}
		else {
			return '';
		}
	}
	
	public function formName() {
		return self::getModelShortName($this->entityModel).'[EavModel]';
	}
	
	protected static function getModelShortName($model) {
		$reflector = new \ReflectionClass($model::className());
		
		return $reflector->getShortName();
	}
}