<?php
namespace api\forms;

use Yii;
use yii\base\Model;

use common\modules\user\Finder;
use common\modules\user\helpers\Password;

use api\helpers\enum\Error;

use api\models\user\User;
use api\models\user\UserToken;

/**
 * SigninForm is the model behind the login form.
 */
class SigninForm extends Model
{
	/** @var string email */
	public $email;

	/** @var string password */
	public $password;

	/** @var \common\modules\user\models\User */
	private $_user;

	/** @var \common\modules\user\Module */
	protected $module;

	/** @var \common\modules\user\Finder */
	protected $finder;

	/**
	 * @param \common\modules\user\Finder $finder
	 * @param array $config
	 */
	public function __construct(Finder $finder, $config = []) {
		$this->finder = $finder;
		$this->module = Yii::$app->getModule('user');
		parent::__construct($config);
	}

	/**
	 * @return array the validation rules.
	 */
	public function rules() {
		return [

			// Username rules
			'emailRequired' => ['email', 'required', 'message' => Error::ERROR_USER_FIELD_EMPTY_EMAIL],
			'emailTrim' => ['email', 'trim'],
			'confirmationValidate' => [
				'email',
				function ($attribute) {
					if ($this->user !== null) {
						$confirmationRequired = $this->module->enableConfirmation && !$this->module->enableUnconfirmedLogin;
						if ($confirmationRequired && !$this->user->getIsConfirmed()) {
							$this->addError($attribute, Error::ERROR_USER_STATUS_UNCONFIRMED);
						}
						if ($this->user->getIsBlocked()) {
							$this->addError($attribute, Error::ERROR_USER_STATUS_BLOCKED);
						}
						if ($this->user->getIsDeleted()) {
							$this->addError($attribute, Error::ERROR_USER_STATUS_DELETED);
						}
					}
				}
			],

			// Password rules
			'passwordRequired' => ['password', 'required', 'message' => Error::ERROR_USER_FIELD_EMPTY_PASSWORD],
			'passwordValidate' => [
				'password',
				function ($attribute) {
					if ($this->user === null || !Password::validate($this->password, $this->user->password_hash)) {
						$this->addError($attribute, Error::ERROR_USER_FIELD_INVALID_PASSWORD);
					}
				}
			],
		];
	}

	/**
	 * Logs in a user using the provided username and password.
     *
	 * @return boolean whether the user is logged in successfully
	 */
	public function login($validate = true) {
		if ($validate && !$this->validate())
			return false;

		if (Yii::$app->user->login($this->user, 0)) {
			$token = $this->user->token;
			if (!$token) {
				$token = new UserToken();
				$token->type = UserToken::TYPE_API;
				$token->user_id = $this->user->id;
			}
			$token->save();
			return true;
		}

		return false;
	}

	/**
	 * Get user
     *
     * @return \api\models\User|array|\common\modules\user\models\User|\yii\db\ActiveRecord|null
     * @throws \yii\base\InvalidConfigException
     */
	public function getUser() {
		if (!$this->_user)
			$this->_user = User::findUserByUsernameOrEmail($this->email);
		return $this->_user;
	}

	/**
	 * Set user
	 * @param $user
	 */
	public function setUser($user) {
		$this->_user = $user;
	}

	/**
	 * @inheritdoc
	 */
	public function beforeValidate() {
		if (parent::beforeValidate()) {
			$this->user = User::findUserByUsernameOrEmail($this->email);
			return true;
		}
		else
			return false;
	}
}