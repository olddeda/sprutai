<?php
namespace common\modules\base\components\bitmask;

use Yii;
use yii\db\ActiveRecord;
use yii\validators\Validator;

/**
 * Class BitmaskValidator
 * @package common\modules\base\components\bitmask
 */
class BitmaskValidator extends Validator
{
    /**
     * @var integer allowed bits for bit mask
     */
    public $mask = 0;

    /**
     * @var string error message
     */
    public $message;

    /**
     * @inheritdoc
     */
    public function init() {
        $this->message = Yii::t('app', 'Only "{mask}" bit mask in {attribute} field can be modified');
    }

    /**
     * @inheritdoc
     */
    public function validateAttribute($model, $attribute) {
    	
        /**
         * @var ActiveRecord $model
         * @var integer$diffMask difference between old and new bit mask
         */
        $diffMask = (int)($model->$attribute ^ $model->getOldAttribute($attribute));

        if ($diffMask & ~$this->mask) {
            $this->addError($model, $attribute, $this->message, ['mask' => $this->mask]);
        }
    }
}