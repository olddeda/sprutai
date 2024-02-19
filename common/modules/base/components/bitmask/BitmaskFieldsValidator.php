<?php
namespace common\modules\base\components\bitmask;

use Yii;
use yii\db\ActiveRecord;
use yii\validators\Validator;

/**
 * Class BitmaskFieldsValidator
 * @package common\modules\base\components\bitmask
 */
class BitmaskFieldsValidator extends Validator
{
    /**
     * @var string bit mask attribute to check
     */
    public $maskAttribute = 'options';

    /**
     * @var string error message
     */
    public $message;

    /**
     * @inheritdoc
     */
    public function init() {
        $this->message = Yii::t('app', 'Only "{names}" fields can be modified');
    }

    /**
     * @inheritdoc
     */
    public function validateAttributes($model, $attributes = null) {
    	
        /**
         * @var ActiveRecord $model difference between old and new bit mask
         */
        $diffMask = (int)($model->{$this->maskAttribute} ^ $model->getOldAttribute($this->maskAttribute));

        $fields = $model->getBitmaskFields();

        $newMask = array_reduce($this->attributes, function ($res, $nextName) use ($fields) {
            return $res | $fields[$nextName];
        }, 0);

        if ($diffMask & ~$newMask) {
            $names = implode(', ', $this->attributes);
            $this->addError($model, $this->maskAttribute, $this->message, ['names' => $names]);
        }
    }

}