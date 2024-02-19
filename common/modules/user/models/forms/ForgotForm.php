<?php

namespace common\modules\user\models\forms;

use Yii;
use yii\base\Model;

use common\modules\user\Finder;
use common\modules\user\Mailer;

use common\modules\user\models\UserToken;

/**
 * Model for collecting data on password recovery.
 *
 * @property \common\modules\user\Module $module
 */
class ForgotForm extends Model
{
	// Scenarios
	const SCENARIO_REQUEST = 'request';
	const SCENARIO_RESET = 'reset';

    /**
	 * @var string
	 */
    public $email;

    /**
	 * @var string
	 */
    public $password;

    /**
	 * @var \common\modules\user\models\User
	 */
    protected $user;

    /**
	 * @var \common\modules\user\Module
	 */
    protected $module;

    /**
	 * @var \common\modules\user\Mailer
	 */
    protected $mailer;

    /**
	 * @var \common\modules\user\Finder
	 */
    protected $finder;

    /**
     * @param \common\modules\user\Mailer $mailer
     * @param \common\modules\user\Finder $finder
     * @param array $config
     */
    public function __construct(Mailer $mailer, Finder $finder, $config = []) {
        $this->module = Yii::$app->getModule('user');
        $this->mailer = $mailer;
        $this->finder = $finder;
        parent::__construct($config);
    }

	/**
	 * @inheritdoc
	 */
	public function formName() {
		return 'forgot-form';
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
	 * @inheritdoc
	 */
    public function scenarios() {
        return [
            self::SCENARIO_REQUEST => ['email'],
            self::SCENARIO_RESET => ['password'],
        ];
    }

    /**
	 * @inheritdoc
	 */
    public function rules() {
        return [
            'emailTrim' => ['email', 'filter', 'filter' => 'trim'],
            'emailRequired' => ['email', 'required'],
            'emailPattern' => ['email', 'email'],
            'emailExist' => ['email', 'exist', 'targetClass' => $this->module->modelMap['User'], 'message' => Yii::t('user', 'message_there_is_no_user_with_this_email_address'),],
            'emailUnconfirmed' => ['email', function ($attribute) {
                    $this->user = $this->finder->findUserByEmail($this->email);
                    if ($this->user !== null && $this->module->enableConfirmation && !$this->user->getIsConfirmed()) {
                        $this->addError($attribute, Yii::t('user', 'message_you_need_to_confirm_your_email_address'));
                    }
                }
            ],
            'passwordRequired' => ['password', 'required'],
            'passwordLength' => ['password', 'string', 'min' => 6],
        ];
    }

    /**
     * Send forgot message.
     *
     * @return bool
     */
    public function sendForgotMessage() {
        if ($this->validate()) {

            /** @var UserToken $token */
            $token = Yii::createObject([
                'class' => UserToken::className(),
                'user_id' => $this->user->id,
                'type' => UserToken::TYPE_RECOVERY,
            ]);
            $token->save(false);
            $this->mailer->sendForgotMessage($this->user, $token);
            Yii::$app->session->setFlash('inline-info', Yii::t('user', 'message_an_email_has_been_sent_with_instructions_for_resetting_your_password'));

            return true;
        }

        return false;
    }

    /**
     * Reset user's password.
     *
     * @param UserToken $token
     *
     * @return bool
     */
    public function resetPassword(UserToken $token) {
        if (!$this->validate() || $token->user === null)
            return false;

        if ($token->user->resetPassword($this->password)) {
            Yii::$app->session->setFlash('inline-success', Yii::t('user', 'message_your_password_has_been_changed_successfully'));
            $token->delete();
        }
		else {
            Yii::$app->session->setFlash('inline-danger', Yii::t('user', 'message_an_error_occurred_and_your_password_has_not_been_changed_please_try_again_later'));
        }

        return true;
    }
}
