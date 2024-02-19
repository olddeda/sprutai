<?php

namespace api\modules\v1\models\forms;

use Yii;
use yii\base\Model;

use api\components\ErrorException;
use api\helpers\enum\Error;

use api\modules\v1\models\User;

class PasswordForm extends Model
{
	/**
	 * @var string
	 */
	public $password;

	/**
	 * @var \api\modules\v1\models\User
	 */
	private $_user;

	/**
	 * @param array $config
	 */
	public function __construct($config = []) {
		parent::__construct($config);
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels() {
		return [
			'password' => Yii::t('user', 'field_password'),
		];
	}

	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [

			// Password rules
			'passwordRequired' => ['password', 'required', 'message' => Error::ERROR_USER_FIELD_EMPTY_PASSWORD],
			'passwordLength' => ['password', 'string', 'min' => 6, 'tooShort' => Error::ERROR_USER_FIELD_SHORT_PASSWORD],
		];
	}

	/**
	 * @return bool
	 */
	public function save() {
		if (!$this->validate())
			return false;

		if (!$this->user->resetPassword($this->password))
			throw new ErrorException($this->user);

		return true;
	}

	/**
	 * Get user
	 *
	 * @return \common\modules\user\models\User
	 */
	public function getUser() {
		if ($this->_user == null)
			$this->_user = User::findOne(['id' => Yii::$app->user->id]);
		return $this->_user;
	}
}
