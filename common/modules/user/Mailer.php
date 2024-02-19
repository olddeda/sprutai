<?php

namespace common\modules\user;

use common\modules\base\components\Debug;
use Yii;
use yii\base\Component;

use common\modules\user\models\User;
use common\modules\user\models\UserToken;

use Swift_Plugins_Loggers_ArrayLogger;
use Swift_Plugins_LoggerPlugin;

/**
 * Mailer.
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class Mailer extends Component
{
    /** @var string */
    public $viewPath = '@common/modules/user/views/mail';

    /** @var string|array Default: `Yii::$app->params['adminEmail']` OR `no-reply@example.com` */
    public $sender;

    /** @var string */
    protected $signupSubject;

    /** @var string */
    protected $confirmationSubject;

    /** @var string */
    protected $reconfirmationSubject;

    /** @var string */
    protected $forgotSubject;

    /** @var \common\modules\user\Module */
    protected $module;

	/** @inheritdoc */
	public function init() {
		$this->module = Yii::$app->getModule('user');
		parent::init();
	}

    /**
     * @return string
     */
    public function getSignupSubject() {
        if ($this->signupSubject == null) {
            $this->setSignupSubject(Yii::t('user-mail', 'subject_signup', Yii::$app->name));
        }
        return $this->signupSubject;
    }

    /**
     * @param string $signupSubject
     */
    public function setSignupSubject($signupSubject) {
        $this->signupSubject = $signupSubject;
    }

    /**
     * @return string
     */
    public function getConfirmationSubject() {
        if ($this->confirmationSubject == null)
            $this->setConfirmationSubject(Yii::t('user-mail', 'subject_confirm_account', Yii::$app->name));
        return $this->confirmationSubject;
    }

    /**
     * @param string $confirmationSubject
     */
    public function setConfirmationSubject($confirmationSubject) {
        $this->confirmationSubject = $confirmationSubject;
    }

    /**
     * @return string
     */
    public function getReconfirmationSubject() {
        if ($this->reconfirmationSubject == null)
            $this->setReconfirmationSubject(Yii::t('user-mail', 'subject_confirm_email_change', Yii::$app->name));
        return $this->reconfirmationSubject;
    }

    /**
     * @param string $reconfirmationSubject
     */
    public function setReconfirmationSubject($reconfirmationSubject) {
        $this->reconfirmationSubject = $reconfirmationSubject;
    }

    /**
     * @return string
     */
    public function getForgotSubject() {
        if ($this->forgotSubject == null)
            $this->setForgotSubject(Yii::t('user-mail', 'subject_complete_password_reset', Yii::$app->name));
        return $this->forgotSubject;
    }

    /**
     * @param string $forgotSubject
     */
    public function setForgotSubject($forgotSubject) {
        $this->forgotSubject = $forgotSubject;
    }

    /**
     * Sends an email to a user after registration.
     *
     * @param User $user
     * @param UserToken $token
     * @param bool $showPassword
	 * @param bool $generatePassword
     *
     * @return bool
     */
    public function sendSignupMessage(User $user, UserToken $token = null, $showPassword = false, $generatePassword = false) {
        return $this->sendMessage($user->email, $this->getSignupSubject(), 'signup', [
			'user' => $user,
			'token' => $token,
			'module' => $this->module,
			'showPassword' => $showPassword,
			'generatePassword' => $generatePassword,
		]);
    }

    /**
     * Sends an email to a user with confirmation link.
     *
     * @param User  $user
     * @param UserToken $token
     *
     * @return bool
     */
    public function sendConfirmationMessage(User $user, UserToken $token) {
        return $this->sendMessage($user->email, $this->getConfirmationSubject(), 'confirmation', [
			'user' => $user,
			'token' => $token
		]);
    }

    /**
     * Sends an email to a user with reconfirmation link.
     *
     * @param User  $user
     * @param UserToken $token
     *
     * @return bool
     */
    public function sendReconfirmationMessage(User $user, UserToken $token) {
		$email =  ($token->type == UserToken::TYPE_CONFIRM_NEW_EMAIL) ? $user->unconfirmed_email : $user->email;
        return $this->sendMessage($email, $this->getReconfirmationSubject(), 'reconfirmation', [
			'user' => $user,
			'token' => $token
		]);
    }

    /**
     * Sends an email to a user with recovery link.
     *
     * @param User $user
     * @param UserToken $token
     *
     * @return bool
     */
    public function sendForgotMessage(User $user, UserToken $token) {
        return $this->sendMessage($user->email, $this->getForgotSubject(), 'forgot', [
			'user' => $user,
			'token' => $token
		]);
    }

	/**
	 * Sends an email to a user with recovery link.
	 *
	 * @param User $user
	 * @param UserToken $token
	 *
	 * @return bool
	 */
	public function sendForgotMobileMessage(User $user, UserToken $token) {
		return $this->sendMessage($user->email, $this->getForgotSubject(), 'forgot_mobile', [
			'user' => $user,
			'token' => $token
		]);
	}

    /**
     * @param string $to
     * @param string $subject
     * @param string $view
     * @param array  $params
     *
     * @return bool
     */
    protected function sendMessage($to, $subject, $view, $params = []) {

        /** @var \yii\mail\BaseMailer $mailer */
        $mailer = Yii::$app->mailer;
        $mailer->viewPath = $this->viewPath;
        $mailer->getView()->theme = Yii::$app->view->theme;
		
        if ($this->sender === null) {
            $this->sender = isset(Yii::$app->params['adminEmail']) ? Yii::$app->params['adminEmail'] : 'no-reply@example.com';
        }
        
		$logger = new Swift_Plugins_Loggers_ArrayLogger();
		$mailer->getSwiftMailer()->registerPlugin(new Swift_Plugins_LoggerPlugin($logger));
        
        return $mailer->compose(['html' => $view, 'text' => 'text/'.$view], $params)
            ->setTo($to)
            ->setFrom($this->sender)
            ->setSubject($subject)
            ->send();
    }
}
