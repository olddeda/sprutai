<?php
namespace client\forms;

use Yii;
use yii\base\Model;

/**
 * Class ContactsForm
 * @package client\forms
 */
class ContactsForm extends Model
{
	/**
	 * @var string
	 */
	public $name;
	
	/**
	 * @var string
	 */
	public $email;
	
	/**
	 * @var string
	 */
	public $phone;
	
	/**
	 * @var string
	 */
	public $body;
	
	/**
	 * @return array the validation rules.
	 */
	public function rules() {
		return [
			[['name', 'email', 'phone', 'body'], 'required'],
			['email', 'email'],
			[['captcha'], \common\modules\base\extensions\recaptcha\ReCaptchaValidator::class, 'uncheckedMessage' => Yii::t('page_contacts', 'error_empty_captcha'), 'when' => function($data) {
				return Yii::$app->user->isGuest;
			}]
		];
	}
	
	/**
	 * @return array customized attribute labels
	 */
	public function attributeLabels() {
		return [
			'name' => Yii::t('page_contacts', 'field_name'),
			'email' => Yii::t('page_contacts', 'field_email'),
			'phone' => Yii::t('page_contacts', 'field_phone'),
			'body' => Yii::t('page_contacts', 'field_body'),
			'captcha' => Yii::t('page_contacts', 'field_captcha'),
		];
	}
	
	/**
	 * @return bool whether the model passes validation
	 */
	public function send() {
		if ($this->validate()) {
			
			$subject = Yii::t('page_cooperation', 'mail_subject');
			$message = Yii::t('page_cooperation', 'mail_body', [
				'name' => $this->name,
				'email' => $this->email,
				'phone' => $this->phone,
				'body' => nl2br($this->body),
			]);
			
			Yii::$app->notification->queue([3, 6], $subject, $message, 'system');
			
			return true;
		}
		return false;
	}
}