<?php

namespace api\modules\v1\models\forms;

use common\modules\user\Finder;
use Yii;
use yii\base\Model;

use common\modules\base\components\Debug;
use common\modules\base\extensions\phoneInput\PhoneInputValidator;
use common\modules\base\extensions\phoneInput\PhoneInputBehavior;

use api\components\ErrorException;
use api\helpers\enum\Error;
use api\modules\v1\models\User;

/**
 * Registration form collects user input on registration process, validates it and creates new User model.
 */
class ProfileForm extends Model
{

	/**
	 * @var string email address
	 */
	public $email;

	/**
	 * @var string
	 */
	public $username;

	/**
	 * @var string first_name
	 */
	public $first_name;

	/**
	 * @var string last_name
	 */
	public $last_name;

	/**
	 * @var string middle_name
	 */
	public $middle_name;

	/**
	 * @var string phone
	 */
	public $phone;

	/**
	 * @var bool avatar_remove
	 */
	public $avatar_remove;

	/**
	 * @var \api\modules\v1\models\User
	 */
	private $_user;

	/**
	 * @var \common\modules\user\Module
	 */
	protected $module;

	/**
	 * @var \common\modules\user\Finder
	 */
	protected $finder;

	/**
	 * @inheritdoc
	 */
	public function init() {
		$this->module = Yii::$app->getModule('user');
		$this->finder = Yii::$container->get(Finder::className());

		$this->setAttributes([
			'username' => $this->user->username,
			'email' => $this->user->email,
		], false);
	}

	/**
	 * @inheritdoc
	 */
	public function behaviors() {
		return array_merge(parent::behaviors(), [
			[
				'class' => PhoneInputBehavior::className(),
			],
		]);
	}

	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [

			// Email rules
			'emailRequired' => ['email', 'required', 'message' => Error::ERROR_USER_FIELD_EMPTY_EMAIL],
			'emailPattern' => ['email', 'email', 'message' => Error::ERROR_USER_FIELD_INVALID_EMAIL],
			'emailUsernameUnique' => [['email', 'username'], 'unique', 'when' => function ($model, $attribute) {
				return $this->user->$attribute != $model->$attribute;
			}, 'targetClass' => $this->module->modelMap['User']],
			'emailTrim' => ['email', 'filter', 'filter' => 'trim'],

			// Firstname rules
			'first_nameRequired' => ['first_name', 'required', 'message' => Error::ERROR_USER_FIELD_EMPTY_FIRST_NAME],
			'first_nameLength' => ['first_name', 'string', 'min' => 3, 'max' => 50, 'tooShort' => Error::ERROR_USER_FIELD_SHORT_FIRST_NAME, 'tooLong' => Error::ERROR_USER_FIELD_LONG_FIRST_NAME],
			'first_nameTrim' => ['first_name', 'filter', 'filter' => 'trim'],

			// last_name rules
			'last_nameLength' => ['last_name', 'string', 'min' => 3, 'max' => 50, 'tooShort' => Error::ERROR_USER_FIELD_SHORT_LAST_NAME, 'tooLong' => Error::ERROR_USER_FIELD_LONG_LAST_NAME],
			'last_nameTrim' => ['last_name', 'filter', 'filter' => 'trim'],

			// Middlename rules
			'middle_nameLength' => ['middle_name', 'string', 'min' => 3, 'max' => 50, 'tooShort' => Error::ERROR_USER_FIELD_SHORT_MIDDLE_NAME, 'tooLong' => Error::ERROR_USER_FIELD_LONG_MIDDLE_NAME],
			'middle_nameTrim' => ['middle_name', 'filter', 'filter' => 'trim'],

			// Phone rules
			'phoneTrim' => ['phone', 'filter', 'filter' => 'trim'],
			'phoneValidator' => [['phone'], PhoneInputValidator::className(), 'message' => Error::ERROR_USER_FIELD_INVALID_PHONE],

			[['avatar_remove'], 'integer'],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels() {
		return [
			'email' => Yii::t('user', 'field_email'),
			'first_name' => Yii::t('user', 'field_first_name'),
			'last_name' => Yii::t('user', 'field_last_name'),
			'middle_name' => Yii::t('user', 'field_middle_name'),
			'phone' => Yii::t('user', 'field_phone'),
		];
	}

	/**
	 * Save  user account.
	 *
	 * @return bool
	 */
	public function save() {
		if (!$this->validate())
			return false;

		/** @var User $user */
		$user = $this->user;
		$user->scenario = User::SCENARIO_UPDATE;
		$user->username = $this->username;
		$user->email = $this->email;
		if (!$user->save())
			throw new ErrorException($user);

		// Save profile params
		$profile = $user->profile;
		$profile->first_name = $this->first_name;
		$profile->last_name = $this->last_name;
		$profile->middle_name = $this->middle_name;
		$profile->phone = $this->phone;
		if (!$profile->save())
			throw new ErrorException($profile);

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
