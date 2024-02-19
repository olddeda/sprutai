<?php
namespace common\modules\base\behaviors;

use Yii;
use yii\db\BaseActiveRecord;
use yii\behaviors\AttributeBehavior;
use yii\base\InvalidConfigException;

class ModifiedByBehavior extends AttributeBehavior
{
	/**
	 * @var string the attribute that will receive user id value
	 * Set this property to false if you do not want to record the creation user id.
	 */
	public $createdByAttribute = 'created_by';

	/**
	 * @var string the attribute that will receive user id value.
	 * Set this property to false if you do not want to record the update user id.
	 */
	public $updatedByAttribute = 'updated_by';

	/**
	 * @var callable|Expression The expression that will be used for generating the timestamp.
	 * This can be either an anonymous function that returns the timestamp value,
	 * or an [[Expression]] object representing a DB expression (e.g. `new Expression('NOW()')`).
	 * If not set, it will use the value of `time()` to set the attributes.
	 */
	public $value;

	/**
	 * @var model
	 */
	public $model;

	/**
	 * @inheritdoc
	 */
	public function init() {
		parent::init();

		if (empty($this->attributes)) {
			$attrs = [];
			try {
				$attrs = $this->model->attributes();
			} catch (InvalidConfigException $exception) {}
			
			$this->attributes = [];
			if (in_array($this->createdByAttribute, $attrs))
				$this->attributes[BaseActiveRecord::EVENT_BEFORE_INSERT][] = $this->createdByAttribute;
			if (in_array($this->updatedByAttribute, $attrs)) {
				$this->attributes[BaseActiveRecord::EVENT_BEFORE_INSERT][] = $this->updatedByAttribute;
				$this->attributes[BaseActiveRecord::EVENT_BEFORE_UPDATE][] = $this->updatedByAttribute;
			}
		}
	}

	/**
	 * @inheritdoc
	 */
	protected function getValue($event) {
		$value = null;
		if ($this->value instanceof Expression) {
			$value = $this->value;
		}
		else {
			if ($this->value !== null)
				$value = call_user_func($this->value, $event);
			else if (Yii::$app instanceof \yii\console\Application)
				$value = 1;
			else
				$value = Yii::$app->user->isGuest ? 0 : Yii::$app->user->id;
		}
		return $value;
	}

	/**
	 * Updates a user id attribute to the current user id.
	 *
	 * @param string $attribute the name of the attribute to update.
	 * @throws InvalidCallException if owner is a new record (since version 2.0.6).
	 */
	public function touch($attribute)  {
		/**
		 * @var $owner BaseActiveRecord
		 */
		$owner = $this->owner;
		
		if ($owner->getIsNewRecord()) {
			throw new InvalidCallException('Updating the user id is not possible on a new record.');
		}
		$owner->updateAttributes(array_fill_keys((array) $attribute, $this->getValue(null)));
	}
}
