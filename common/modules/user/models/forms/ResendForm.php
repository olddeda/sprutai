<?php

namespace common\modules\user\models\forms;

use Yii;
use yii\base\Model;

use common\modules\user\Finder;
use common\modules\user\Mailer;
use common\modules\user\models\UserToken;

/**
 * ResendForm gets user email address and validates if user has already confirmed his account. If so, it shows error
 * message, otherwise it generates and sends new confirmation token to user.
 *
 * @property \common\modules\user\models\User $user
 */
class ResendForm extends Model
{
    /**
	 * @var string
	 */
    public $email;

    /**
	 * @var \common\modules\user\models\User
	 */
    private $_user;

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
     * @param Mailer $mailer
     * @param Finder $finder
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
		return 'resend-form';
	}

    /**
	 * Get user
	 *
     * @return \common\modules\user\models\User
     */
    public function getUser() {
        if ($this->_user === null) {
            $this->_user = $this->finder->findUserByEmail($this->email);
        }
        return $this->_user;
    }

    /**
	 * @inheritdoc
	 */
    public function rules() {
        return [
            'emailRequired' => ['email', 'required'],
            'emailPattern' => ['email', 'email'],
            'emailExist' => ['email', 'exist', 'targetClass' => $this->module->modelMap['User']],
            'emailConfirmed' => ['email', function () {
                    if ($this->user != null && $this->user->getIsConfirmed()) {
                        $this->addError('email', Yii::t('user', 'message_this_account_has_already_been_confirmed'));
                    }
                }
            ],
        ];
    }

    /**
	 * @inheritdoc
	 */
    public function attributeLabels() {
		return [
            'email' => Yii::t('user', 'field_email'),
        ];
    }

    /**
     * Create new confirmation token and send it to the user.
     *
     * @return bool
     */
    public function resend() {
        if (!$this->validate()) {
            return false;
        }

        /** @var UserToken $token */
        $token = Yii::createObject([
            'class' => UserToken::className(),
            'user_id' => $this->user->id,
            'type' => UserToken::TYPE_CONFIRMATION,
        ]);
        $token->save(false);

        $this->mailer->sendConfirmationMessage($this->user, $token);
        Yii::$app->session->setFlash('inline-info', Yii::t('user', 'message_a_message_has_been_sent_to_your_email_address_it_contains_a_confirmation_link_that_you_must_click_to_complete_registration'));

        return true;
    }
}
