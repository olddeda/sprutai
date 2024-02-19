<?php

namespace common\modules\user\models\forms;

use Yii;
use yii\base\Model;

use common\modules\user\Finder;
use common\modules\user\helpers\Password;

/**
 * SigninForm get user's login and password, validates them and logs the user in. If user has been blocked, it adds
 * an error to login form.
 */
class SigninForm extends Model
{
    /**
	 * @var string User's email or username
	 */
    public $login;

    /**
 	 * @var string User's plain password
	 */
    public $password;

    /**
 	 * @var string Whether to remember the user
	 */
    public $rememberMe = false;
	
	/**
	 * @var string Whether to remember the user
	 */
	public $anotherComputer = false;

    /**
 	 * @var \common\modules\user\models\User
	 */
    protected $user;

    /**
 	 * @var \common\modules\user\Module
	 */
    protected $module;

    /**
 	 * @var \common\modules\user\Finder
	 */
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
	 * @inheritdoc
	 */
	public function formName() {
		return 'login-form';
	}

    /**
	 * @inheritdoc
	 */
    public function attributeLabels() {
        return [
            'login' => Yii::t('user', 'field_login'),
            'password' => Yii::t('user', 'field_password'),
            'rememberMe' => Yii::t('user', 'field_remember_me_next_time'),
			'anotherComputer' => Yii::t('user', 'field_another_computer'),
        ];
    }

    /**
	 * @inheritdoc
	 */
    public function rules() {
        return [
            'requiredFields' => [['login', 'password'], 'required'],
            'loginTrim' => ['login', 'trim'],
            'passwordValidate' => [
                'password',
                function ($attribute) {
                    if ($this->user === null || !Password::validate($this->password, $this->user->password_hash)) {
						$this->addError($attribute, Yii::t('user', 'error_invalid_login_or_password'));
                    }
                }
            ],
            'confirmationValidate' => [
                'login',
                function ($attribute) {
                    if ($this->user !== null) {
                        $confirmationRequired = $this->module->enableConfirmation && !$this->module->enableUnconfirmedLogin;
                        if ($confirmationRequired && !$this->user->getIsConfirmed()) {
                            $this->addError($attribute, Yii::t('user', 'error_you_need_to_confirm_your_email_address'));
                        }
                        if ($this->user->getIsBlocked()) {
                            $this->addError($attribute, Yii::t('user', 'error_your_account_has_been_blocked'));
                        }
						if ($this->user->getIsDeleted()) {
							$this->addError($attribute, Yii::t('user', 'error_your_account_has_been_deleted'));
						}
                    }
                }
            ],
            'rememberMe' => ['rememberMe', 'boolean'],
			'anotherComputer' => ['anotherComputer', 'boolean'],
        ];
    }

    /**
     * Validates form and logs the user in.
     *
     * @return bool whether the user is logged in successfully
	 * @throws \Throwable
	 */
    public function login() {
        if ($this->validate()) {
        	$remember = ($this->rememberMe || !$this->anotherComputer) ? $this->module->rememberFor : 0;
            $return = Yii::$app->getUser()->login($this->user, $remember);
            if ($return) {
				Yii::$app->getSession()->remove('admin_id');
			}
			return $return;
        }
		else {
            return false;
        }
    }

    /**
	 * @inheritdoc
	 */
    public function beforeValidate() {
        if (parent::beforeValidate()) {
            $this->user = $this->finder->findUserByUsernameOrEmail($this->login);
            return true;
        }
		else {
            return false;
        }
    }
}
