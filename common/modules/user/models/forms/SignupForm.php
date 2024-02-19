<?php

namespace common\modules\user\models\forms;

use Yii;
use yii\base\Model;

use common\modules\user\models\User;

use common\modules\base\components\Debug;

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
	public function formName() {
		return 'signup-form';
	}

	public function behaviors() {
		return [
		];
	}

    /**
     * @inheritdoc
     */
    public function rules() {
        $user = $this->module->modelMap['User'];
        return [

            // username rules
            'usernameLength' => ['username', 'string', 'min' => 3, 'max' => 20],
            'usernameTrim' => ['username', 'filter', 'filter' => 'trim'],
            'usernamePattern' => ['username', 'match', 'pattern' => $user::$usernameRegexp],
            'usernameRequired' => ['username', 'required'],
            'usernameUnique' => ['username', 'unique', 'targetAttribute' => ['username'], 'targetClass' => $user, 'message' => Yii::t('user', 'error_this_username_has_already_been_taken')],

            // email rules
            'emailTrim' => ['email', 'filter', 'filter' => 'trim'],
            'emailRequired' => ['email', 'required'],
            'emailPattern' => ['email', 'email'],
            'emailUnique' => ['email', 'unique', 'targetAttribute' => ['email'], 'targetClass' => $user, 'message' => Yii::t('user', 'error_this_email_address_has_already_been_taken')],

            // password rules
            'passwordRequired' => ['password', 'required', 'skipOnEmpty' => $this->module->enableGeneratingPassword],
            'passwordLength' => ['password', 'string', 'min' => 6],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'email' => Yii::t('user', 'field_email'),
            'username' => Yii::t('user', 'field_username'),
            'password' => Yii::t('user', 'field_password'),
        ];
    }

    /**
     * Registers a new user account. If registration was successful it will set flash message.
     *
     * @return bool
     */
    public function signup() {
        if (!$this->validate()) {
			return false;
		}

        /** @var User $user */
        $user = Yii::createObject(User::className());
        $user->setScenario(User::SCENARIO_SIGNUP);
        $this->loadAttributes($user);

        if (!$user->signup()) {
			return false;
		}

        Yii::$app->session->setFlash('inline-info', Yii::t('user', 'message_your_account_has_been_created_and_a_message_with_further_instructions_has_been_sent_to_your_email'));

        return true;
    }

    /**
     * Loads attributes to the user model. You should override this method if you are going to add new fields to the
     * registration form. You can read more in special guide.
     *
     * By default this method set all attributes of this model to the attributes of User model, so you should properly
     * configure safe attributes of your User model.
     *
     * @param User $user
     */
    protected function loadAttributes(User $user) {
        $user->setAttributes($this->attributes);
    }
}
