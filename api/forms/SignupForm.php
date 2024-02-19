<?php
namespace api\forms;

use Yii;
use yii\base\Model;
use yii\db\Query;

use api\components\ErrorException;
use api\helpers\enum\Error;

use api\models\user\User;

/**
 * Registration form collects user input on registration process, validates it and creates new User model.
 */
class SignupForm extends Model
{

	/**
	 * @var string email address
	 */
	public $email;

	/**
	 * @var string username
	 */
	public $username;

	/**
	 * @var string password
	 */
	public $password;

	/**
     * @var \api\models\user\User
     */
	public $user;

	/**
	 * @var \common\modules\user\Module
	 */
	protected $module;

	/**
	 * @inheritdoc
	 */
	public function init() {
		$this->module = Yii::$app->getModule('user');
	}

	/**
	 * @inheritdoc
	 */
	public function rules() {
		$user = $this->module->modelMap['User'];
		return [

			// Email rules
			'emailRequired' => ['email', 'required', 'message' => Error::ERROR_USER_FIELD_EMPTY_EMAIL],
			'emailPattern' => ['email', 'email', 'message' => Error::ERROR_USER_FIELD_INVALID_EMAIL],
			'emailUnique' => ['email', 'unique', 'targetAttribute' => ['email'], 'targetClass' => $user, 'message' => Error::ERROR_USER_FIELD_EXISTS_EMAIL],
			'emailTrim' => ['email', 'filter', 'filter' => 'trim'],

			// Password rules
			'passwordRequired' => ['password', 'required', 'message' => Error::ERROR_USER_FIELD_EMPTY_PASSWORD],
			'passwordLength' => ['password', 'string', 'min' => 6, 'tooShort' => Error::ERROR_USER_FIELD_SHORT_PASSWORD],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels() {
		return [
			'email' => Yii::t('user', 'field_email'),
			'password' => Yii::t('user', 'field_password'),
		];
	}

	/**
	 * Registers a new user account. If registration was successful it will set flash message.
	 *
     * @return bool
     * @throws ErrorException
     * @throws \yii\base\InvalidConfigException
     */
	public function signup() {
		if (!$this->validate())
			return false;

		/** @var User $user */
		$this->user = Yii::createObject(User::class);
		$this->user->setScenario(User::SCENARIO_SIGNUP);
		$this->user->setAttributes($this->attributes);

		if (!$this->user->signup())
			throw new ErrorException($this->user);

		// Save profile params
		$profile = $this->user->profile;
		$profile->save(false);

		return true;
	}

	/**
	 * Generate new username based on email address, or create new username
	 *
	 * @return string
	 */
	public function generateUsername() {

		// Try to use name part of email
		$emailPart = explode('@', $this->email)[0];
		$this->username = $emailPart;

		if ($this->validate(['username']))
			return $this->username;

		// Generate username like "user1", "user2", etc...
		while (!$this->validate(['username'])) {
			$row = (new Query())->from(User::tableName())->select('MAX(id) as id')->one();
			$this->username = $emailPart.++$row['id'];
		}

		return $this->username;
	}
}
