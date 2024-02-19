<?php

namespace common\modules\user\models\forms;

use Yii;
use yii\base\Model;

use common\modules\user\Module;
use common\modules\user\Mailer;
use common\modules\user\helpers\Password;
use common\modules\user\models\UserToken;

/**
 * SettingsForm gets user's username, email and password and changes them.
 *
 * @property \common\modules\user\models\User $user
 */
class SettingsForm extends Model
{
	/**
	 * @var \common\modules\user\Module
	 */
	protected $module;

	/**
	 * @var \common\modules\user\Mailer
	 */
	protected $mailer;

	/**
	 * @var \common\modules\user\models\User
	 */
	private $_user;

    /**
	 * @var string
	 */
    public $email;

    /**
	 * @var string
	 */
    public $username;

    /**
	 * @var string
	 */
    public $new_password;

    /**
	 * @var string
	 */
    public $current_password;

	/**
	 * @inheritdoc
	 */
	public function __construct(Mailer $mailer, $config = []) {
		$this->mailer = $mailer;
		$this->module = Yii::$app->getModule('user');
		$this->setAttributes([
			'username' => $this->user->username,
			'email' => $this->user->unconfirmed_email ?: $this->user->email,
		], false);
		parent::__construct($config);
	}

	/**
	 * @inheritdoc
	 */
	public function formName() {
		return 'settings-form';
	}

	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			'usernameRequired' => ['username', 'required'],
			'usernameTrim' => ['username', 'filter', 'filter' => 'trim'],
			'usernameLength'   => ['username', 'string', 'min' => 3, 'max' => 255],
			'usernamePattern' => ['username', 'match', 'pattern' => '/^[-a-zA-Z0-9_\.@]+$/'],
			'emailRequired' => ['email', 'required'],
			'emailTrim' => ['email', 'filter', 'filter' => 'trim'],
			'emailPattern' => ['email', 'email'],
			'emailUsernameUnique' => [['email', 'username'], 'unique', 'when' => function ($model, $attribute) {
				return $this->user->$attribute != $model->$attribute;
			}, 'targetClass' => $this->module->modelMap['User']],
			'newPasswordLength' => ['new_password', 'string', 'min' => 6],
			'currentPasswordRequired' => ['current_password', 'required'],
			'currentPasswordValidate' => ['current_password', function ($attr) {
				if (!Password::validate($this->$attr, $this->user->password_hash)) {
					$this->addError($attr, Yii::t('user', 'error_current_password_is_not_valid'));
				}
			}],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels() {
		return [
			'email' => Yii::t('user', 'field_email'),
			'username' => Yii::t('user', 'field_username'),
			'new_password' => Yii::t('user', 'field_new_password'),
			'current_password' => Yii::t('user', 'field_current_password'),
		];
	}


	/**
	 * Get user
	 *
	 * @return \common\modules\user\models\User
	 */
    public function getUser() {
        if ($this->_user == null)
            $this->_user = Yii::$app->user->identity;
        return $this->_user;
    }

    /**
     * Saves new account settings.
     *
	 * @return bool
	 * @throws \yii\base\InvalidConfigException
	 */
    public function save() {
        if ($this->validate()) {
            $this->user->scenario = 'settings';
            $this->user->username = $this->username;
            $this->user->password = $this->new_password;

            if ($this->email == $this->user->email && $this->user->unconfirmed_email != null) {
                $this->user->unconfirmed_email = null;
            }
			elseif ($this->email != $this->user->email) {
                switch ($this->module->emailChangeStrategy) {
                    case Module::STRATEGY_INSECURE:
                        $this->insecureEmailChange();
                        break;
                    case Module::STRATEGY_DEFAULT:
                        $this->defaultEmailChange();
                        break;
                    case Module::STRATEGY_SECURE:
                        $this->secureEmailChange();
                        break;
                    default:
                        throw new \OutOfBoundsException('Invalid email changing strategy');
                }
            }

            return $this->user->save();
        }

        return false;
    }

    /**
     * Change user's email address to given without any confirmation.
     */
    protected function insecureEmailChange() {
        $this->user->email = $this->email;
        Yii::$app->session->setFlash('success', Yii::t('user', 'message_your_email_address_has_been_changed'));
    }

    /**
     * Send a confirmation message to user's email address with link to confirm changing of email.
     */
    protected function defaultEmailChange() {
        $this->user->unconfirmed_email = $this->email;

        /** @var UserToken $token */
        $token = Yii::createObject([
            'class' => UserToken::class,
            'user_id' => $this->user->id,
            'type' => UserToken::TYPE_CONFIRM_NEW_EMAIL,
        ]);
        $token->save(false);

        $this->mailer->sendReconfirmationMessage($this->user, $token);
        Yii::$app->session->setFlash('info', Yii::t('user', 'message_a_confirmation_message_has_been_sent_to_your_new_email_address'));
    }

    /**
     * Send a confirmation message to both old and new email addresses with link to confirm changing of email.
     *
     * @throws \yii\base\InvalidConfigException
     */
    protected function secureEmailChange() {
        $this->defaultEmailChange();

        /** @var UserToken $token */
        $token = Yii::createObject([
            'class' => UserToken::class,
            'user_id' => $this->user->id,
            'type' => UserToken::TYPE_CONFIRM_OLD_EMAIL,
        ]);
        $token->save(false);

        $this->mailer->sendReconfirmationMessage($this->user, $token);

        // unset flags if they exist
        $this->user->flags &= ~User::NEW_EMAIL_CONFIRMED;
        $this->user->flags &= ~User::OLD_EMAIL_CONFIRMED;
        $this->user->save(false);

        Yii::$app->session->setFlash('info', Yii::t('user', 'message_me_we_have_sent_confirmation_links_to_both_old_and_new_email_addresses_you_must_click_both_links_to_complete_your_request'));
    }
}
