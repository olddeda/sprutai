<?php
namespace common\modules\base\validators;

use Yii;
use yii\validators\Validator;

/**
 * Class EitherValidator
 * @package common\modules\base\validators
 */
class EitherValidator extends Validator
{
	/**
	 * @inheritdoc
	 */
	public function init() {
		if (!$this->message) {
			$this->message = Yii::t('base', 'validation_either');
		}
		parent::init();
	}
	
	/**
	 * @inheritdoc
	 */
	public function validateAttributes($model, $attributes = null) {
		$labels = [];
		$values = [];
		$attributes = $this->attributes;
		foreach($attributes as $attribute) {
			$labels[] = $model->getAttributeLabel($attribute);
			if (!empty($model->$attribute)) {
				$values[] = $model->$attribute;
			}
		}
		
		if (empty($values)) {
			foreach($attributes as $attribute) {
				$this->addError($model, $attribute, $this->message);
			}
			return false;
		}
		return true;
	}
}
