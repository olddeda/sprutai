<?php
namespace common\modules\notification\forms;

use common\modules\base\components\Debug;
use Yii;
use yii\base\Model;

use common\modules\notification\Module;

/**
 * Class NotifyForm
 * @package common\modules\notify\forms
 */
class NotifyForm extends Model
{
	/**
	 * @var string
	 */
	public $body;
	
	/**
	 * @return array the validation rules.
	 */
	public function rules() {
		return [
			[['body'], 'required'],
			[['body'], 'string', 'max' => 10000],
		];
	}
	
	/**
	 * @return array customized attribute labels
	 */
	public function attributeLabels() {
		return [
			'body' => Yii::t('notification-form', 'field_body'),
		];
	}
	
	/**
	 * @return bool whether the model passes validation
	 */
	public function send() {
		if ($this->validate()) {
			
			/** @var Module $module */
			$module = Module::getInstance();
			
			Yii::$app->notification->queueTelegramIds($module->channelIds, $this->body);
			
			return true;
		}
		return false;
	}
}