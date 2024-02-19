<?php
namespace api\forms;

use Yii;
use yii\base\Model;

use common\modules\user\Finder;
use common\modules\user\Mailer;

use common\modules\user\models\UserToken;

use api\components\ErrorException;
use api\helpers\enum\Error;

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
	 * @var \api\models\user\User
	 */
    public $user;

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
			'emailRequired' => ['email', 'required', 'message' => Error::ERROR_USER_FIELD_EMPTY_EMAIL],
            'emailPattern' => ['email', 'email', 'message' => Error::ERROR_USER_FIELD_INVALID_EMAIL],
            'emailExist' => ['email', 'exist', 'targetClass' => $this->module->modelMap['User'], 'message' => Error::ERROR_USER_FIELD_NOT_EXISTS_EMAIL],
			'emailTrim' => ['email', 'filter', 'filter' => 'trim'],
            'emailUnconfirmed' => ['email', function ($attribute) {
				$this->user = $this->finder->findUserByEmail($this->email);
				if ($this->user !== null) {
					if ($this->module->enableConfirmation && !$this->user->getIsConfirmed()) {
						$this->addError($attribute, Error::ERROR_USER_STATUS_UNCONFIRMED);
					}
					if ($this->user->getIsBlocked()) {
						$this->addError($attribute, Error::ERROR_USER_STATUS_BLOCKED);
					}
					if ($this->user->getIsDeleted()) {
						$this->addError($attribute, Error::ERROR_USER_STATUS_DELETED);
					}
				}
			}],

			// Password rules
			'passwordRequired' => ['password', 'required', 'message' => Error::ERROR_USER_FIELD_EMPTY_PASSWORD],
			'passwordLength' => ['password', 'string', 'min' => 6, 'tooShort' => Error::ERROR_USER_FIELD_SHORT_PASSWORD],
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
                'type' => UserToken::TYPE_RECOVERY_MOBILE,
            ]);
            $token->save(false);
            $this->mailer->sendForgotMobileMessage($this->user, $token);

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

        if ($token->user->resetPassword($this->password))
			$token->delete();
		else
			throw new ErrorException($token->user);

        return true;
    }
}
