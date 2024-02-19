<?php

namespace frontend\models\forms;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class ContactsForm extends Model
{

	public $name;
	public $email;
	public $body;
	public $reCaptcha;

	/**
	 * @inheritdoc
	 */
	public function rules() {
		$rules = [
			['name', 'required', 'message' => Yii::t('contacts', 'field_error_name_empty')],
			['email', 'required', 'message' => Yii::t('contacts', 'field_error_email_empty')],
			['email', 'email', 'message' => Yii::t('contacts', 'field_error_email_invalid')],
			['body', 'required', 'message' => Yii::t('contacts', 'field_error_body_empty')],
		];
		if (!YII_ENV_TEST) {
			$rules[] = ['reCaptcha', 'required', 'message' => Yii::t('contacts', 'field_error_re_captcha_empty')];
		}
		$rules[] = [['reCaptcha'], \common\validators\ReCaptchaValidator::className(), 'message' => Yii::t('contacts', 'field_error_re_captcha_invalid'), 'skipOnEmpty' => YII_ENV_TEST];
			
		return $rules;
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels() {
		return [
			'name' => Yii::t('contacts', 'field_name'),
			'email' => Yii::t('contacts', 'field_email'),
			'body' => Yii::t('contacts', 'field_body'),
			'reCaptcha' => Yii::t('contacts', 'field_re_captcha'),
		];
	}

	/**
	 * Sends an email to the specified email address using the information collected by this model.
	 *
	 * @param  string  $email the target email address
	 * @return boolean whether the email was sent
	 */
	public function sendEmail($email) {
		return Yii::$app->mailer->compose()
			->setTo($email)
			->setFrom([$this->email => $this->name])
			->setSubject(Yii::t('contacts', 'subject'))
			->setTextBody($this->body)
			->send();
	}

}
