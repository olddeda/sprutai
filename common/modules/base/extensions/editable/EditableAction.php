<?php

namespace common\modules\base\extensions\editable;

use common\modules\base\components\ActiveRecord;
use common\modules\base\components\ArrayHelper;
use Yii;
use yii\base\Action;
use yii\helpers\Inflector;
use yii\base\InvalidConfigException;
use yii\web\BadRequestHttpException;

/**
 * Class EditableAction
 *
 * @package common\modules\base\extensions\editable
 */
class EditableAction extends Action
{
	/**
	 * @var string the class name to handle
	 */
	public $modelClass;
	
	/**
	 * @var string the scenario to be used (optional)
	 */
	public $scenario;
	
	/**
	 * @var \Closure a function to be called previous saving model. The anonymous function is preferable to have the
	 * model passed by reference. This is useful when we need to set model with extra data previous update
	 */
	public $preProcess;
	
	/**
	 * @var bool whether to create a model if a primary key parameter was not found
	 */
	public $forceCreate = true;
	
	/**
	 * @var string default pk column name
	 */
	public $pkColumn = 'id';
	
	/**
	 * @var array $allowedAttributes
	 */
	public $allowedAttributes;
	
	/**
	 * @inheritdoc
	 *
	 * @throws \yii\base\InvalidConfigException
	 */
	public function init() {
		if ($this->modelClass === null) {
			throw new InvalidConfigException('ModelClass cannot be empty.');
		}
	}
	
	/**
	 * Runs the action
	 *
	 * @throws BadRequestHttpException
	 * @throws \ReflectionException
	 * @throws \Throwable
	 * @throws \yii\web\NotFoundHttpException
	 */
	public function run() {
		$isJSON = false;
		
		/** @var ActiveRecord $class */
		$class = $this->modelClass;
		
		$className = Inflector::camel2id((new \ReflectionClass($class))->getShortName());
		$pk = Yii::$app->request->post('pk');
		$attribute = Yii::$app->request->post('name');
		
		// For attributes with format - relationName.attributeName
		if (strpos($attribute, '.')) {
			$attributeParts = explode('.', $attribute);
			$attribute = array_pop($attributeParts);
		}
		$value = Yii::$app->request->post('value');
		
		if (isset($_POST['editableAttribute'])) {
			$pk = Yii::$app->request->post('editableKey');
			$attribute = Yii::$app->request->post('editableAttribute');
			$tmp = Yii::$app->request->post(ucfirst($className));
			if (is_array($tmp)) {
				$tmp = current($tmp);
				if (isset($tmp[$attribute]))
					$value = $tmp[$attribute];
			}
			$isJSON = true;
			
		}
		
		if ($pk === null) {
			throw new BadRequestHttpException('PK cannot be empty.');
		}
		if ($attribute === null) {
			throw new BadRequestHttpException('Attribute cannot be empty.');
		}
		if ($value === null) {
			throw new BadRequestHttpException('Value cannot be empty.');
		}
		
		$pkColumn = $this->pkColumn;
		
		if (is_array($pk)) {
			$models = $class::findAllByColumn($pkColumn, $pk, [], true, true);
			if ($models) {
				foreach ($models as $model) {
					$this->_save($model, $attribute, $value);
				}
			}
		}
		else {
			/** @var \Yii\db\ActiveRecord $model */
			$model = $class::findByColumn($pkColumn, $pk, !$this->forceCreate, $className, [], true, true);
			$this->_save($model, $attribute, $value);
		}
		
		if ($isJSON) {
			Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
			return ['output' => $value, 'message' => ''];
		}
	}
	
	/**
	 * @param object $model
	 * @param string $attribute
	 * @param string $value
	 *
	 * @return mixed
	 * @throws BadRequestHttpException
	 */
	private function _save($model, $attribute, $value) {
		$class = $this->modelClass;
		if (!$model) {
			if ($this->forceCreate) {
				$model = new $class();
			}
			else {
				throw new BadRequestHttpException('Entity not found by primary key '.$pk);
			}
		}
		
		// do we have a preProcess function
		if ($this->preProcess && is_callable($this->preProcess, true)) {
			call_user_func($this->preProcess, $model);
		}
		
		if ($this->scenario !== null) {
			$model->setScenario($this->scenario);
		}
		
		if (is_array($value)) {
			$fields = ['price', 'year'];
			if (in_array($attribute, $fields)) {
				foreach ($value as $key => $val) {
					$attr = $attribute;
					if ($key != $attr)
						$attr.= '_'.$key;
					$model->$attr = $val;
				}
			}
			else if (in_array($attribute, ['address'])) {
				$model->address = $value['address'];
				$model->country = $value['country'];
				$model->locality = $value['locality'];
				$model->latitude = $value['latitude'];
				$model->longitude = $value['longitude'];
			}
			else
				$model->$attribute = $value;
		}
		else
			$model->$attribute = $value;
		
		if ($model->validate([$attribute])) {
			return $model->save(false);
		}
		else {
			throw new BadRequestHttpException($model->getFirstError($attribute));
		}
	}
}
