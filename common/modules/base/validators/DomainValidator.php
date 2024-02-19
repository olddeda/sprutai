<?php
namespace common\modules\base\validators;

use Yii;
use yii\validators\Validator;

/**
 * Class DomainValidator
 * @package common\modules\base\validators
 */
class DomainValidator extends Validator
{
	public function validateAttribute($model, $attribute) {
		if (!(
			preg_match("/^([a-z\d](-*[a-z\d])*)(\.([a-z\d](-*[a-z\d])*))*$/i", $model->$attribute) &&
			preg_match("/^.{1,253}$/", $model->$attribute) &&
			preg_match("/^[^\.]{1,63}(\.[^\.]{1,63})*$/", $model->$attribute)
		))
		{
			$this->addError($model, $attribute, Yii::t('base', 'validation_domain_error', ['attribute' => $model->getAttributeLabel($attribute)]));
		}
	}
}