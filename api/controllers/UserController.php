<?php

namespace api\modules\v1\controllers;

use Yii;
use yii\helpers\ArrayHelper;
use yii\filters\VerbFilter;

use api\components\ErrorException;
use api\helpers\enum\Error;

use api\modules\v1\components\Controller;
use api\modules\v1\models\User;
use api\modules\v1\models\UserToken;
use api\modules\v1\models\forms\SigninForm;
use api\modules\v1\models\forms\SignupForm;
use api\modules\v1\models\forms\ForgotForm;
use api\modules\v1\models\forms\ProfileForm;
use api\modules\v1\models\forms\PasswordForm;

class UserController extends Controller
{
	/**
	 * @var string the model class name. This property must be set.
	 */
	public $modelClass = 'api\modules\v1\models\User';

	/**
	 * @var \common\modules\user\Module
	 */
	public $module;

	/**
	 * @var \common\modules\user\Finder
	 */
	public $finder;

	/**
	 * @inheritdoc
	 */
	public function init() {
		$this->module = Yii::$app->getModule('user');
	}

	/**
	 * @inheritdoc
	 * @return array
	 */
	public function behaviors() {
		return ArrayHelper::merge(parent::behaviors(), [
			'authenticator' => [
				'except' => ['signin', 'signup', 'signup-complete', 'forgot', 'forgot-validate', 'forgot-complete'],
			],
			'access' => [
				'except' => ['signin', 'signup', 'signup-complete', 'forgot', 'forgot-validate', 'forgot-complete'],
			],
			'verbs' => [
				'class' => VerbFilter::className(),
				'actions' => [
					'signin' => ['post'],
					'signup' => ['post'],
					'forgot' => ['post'],
					'forgot-validate' => ['get'],
					'forgot-complete' => ['put'],
					'profile-view' => ['get'],
					'profile-update' => ['post'],
					'profile-password-update' => ['put'],
				],
			],
		]);
    }

	/**
	 * @apiDefine ObjectUser
	 * @apiSuccess (200) {Object} token Токен
	 * @apiSuccess (200) {String} token.code Ключ
	 * @apiSuccess (200) {Integer} token.expire Дата истечения срока действия
	 * @apiSuccess (200) {Object} user Пользователь
	 * @apiSuccess (200) {Integer} user.id ID
	 * @apiSuccess (200) {String} user.username Никнейм
	 * @apiSuccess (200) {String} user.email E-mail
	 * @apiSuccess (200) {Object} user.profile Профиль
	 * @apiSuccess (200) {String} user.profile.last_name Фамилия
	 * @apiSuccess (200) {String} user.profile.first_name Имя
	 * @apiSuccess (200) {String} user.profile.middle_name Отчество
	 * @apiSuccessExample {json} Success-Response:
	 * HTTP/1.1 200 OK
	 * {
	 *     "success": true,
	 *     "data": {
	 *         "token": {
	 *             "code": "",
	 *             "expire": ""
	 *         },
	 *         "user": {
	 *             "id": 2,
	 *             "username": "demo",
	 *             "email": "demo@antikvarus.ru",
	 *             "profile": {
	 *                 "user_id": 2,
	 *                 "phone": "+7 (123) 456-78-90",
	 *                 "first_name": "",
	 *                 "last_name": "",
	 *                 "middle_name": ""
	 *             },
	 *             "avatar": {
	 *                "path": "/static/media/00/00/00/user/",
	 *                "file": "avatar.jpg?1414211869"
	 *             }
	 *         }
	 *     },
	 *     "error": []
	 * }
	 */

	/**
	 * @apiDefine admin Администраторы
	 * Доступ для пользователей состоящих в группе - Администраторы
	 */

	/**
	 * @apiDefine user Пользователи
	 * Доступ для пользователей состоящих в группе - Пользователи
	 */

	/**
	 * @apiDefine guest Гости
	 * Доступ для неавторизованных пользователей
	 */

	/**
	 * @apiDefine ErrorBlock
	 * @apiErrorExample {json} Error-Response:
	 * HTTP/1.1 400 Not Found
	 * {
	 *     "success": false,
	 *     "data": [],
	 *     "error": {
	 *         "name": "Название ошибки",
	 *         "message": "Описание ошибки",
	 *         "code": "Код ошибки",
	 *         "type": "Тип ошибки"
	 *     }
	 * }
	 *
	 */

	/**
	 * @api {post} /users/signup Регистрация
	 * @apiName UserSignup
	 * @apiGroup User
	 * @apiVersion 1.0.0
	 * @apiPermission guest
	 *
	 * @apiParam {String} email E-mail
	 * @apiParam {String} password Пароль
	 * @apiParam {String} first_name Имя
	 * @apiParam {String} [last_name] Фамилия
	 * @apiParam {String} [middle_name] Отчество
	 *
	 * @apiSampleRequest /users/signup
	 *
	 * @apiUse ObjectUser
	 * @apiUse ErrorBlock
	 *
	 * @apiError ERROR_USER_FIELD_EMPTY_EMAIL Не указан обязательный параметр «email»
	 * @apiError ERROR_USER_FIELD_EMPTY_PASSWORD Не указан обязательный параметр «password»
	 * @apiError ERROR_USER_FIELD_INVALID_EMAIL Указан ошибочный параметр «email»
	 * @apiError ERROR_USER_FIELD_EXISTS_EMAIL Пользователь с таким E-mail уже существует
	 * @apiError ERROR_USER_FIELD_SHORT_PASSWORD Значение «password» должно содержать минимум 4 символа
	 * @apiError ERROR_USER_FIELD_EMPTY_FIRST_NAME Не указан обязательный параметр «first_name»
	 * @apiError ERROR_USER_FIELD_SHORT_FIRST_NAME Значение «first_name» должно содержать минимум 4 символа
	 * @apiError ERROR_USER_FIELD_LONG_FIRST_NAME Значение «first_name» должно содержать максимум 50 символов
	 * @apiError ERROR_USER_FIELD_SHORT_FIRST_NAME Значение «last_name» должно содержать минимум 4 символа
	 * @apiError ERROR_USER_FIELD_LONG_FIRST_NAME Значение «last_name» должно содержать максимум 50 символов
	 * @apiError ERROR_USER_FIELD_SHORT_MIDDLE_NAME Значение «middle_name» должно содержать минимум 4 символа
	 * @apiError ERROR_USER_FIELD_LONG_MIDDLE_NAME Значение «middle_name» должно содержать максимум 50 символов
	 */
	public function actionSignup() {
		if (!Yii::$app->user->isGuest)
			Yii::$app->user->logout();

		if (!$this->module->enableRegistration)
			throw new ErrorException(Error::ERROR_REGISTRATION_DISABLED);

		/** @var \api\modules\v1\models\forms\SignupForm $model */
		$model = Yii::createObject(SignupForm::className());

		$params = array();
		if (Yii::$app->request->post())
			$params['SignupForm'] = Yii::$app->request->post();

		// Load params
		if ($model->load($params)) {

			// Generate username
			if (!$model->username)
				$model->generateUsername();

			// Validate and save
			if ($model->signup()) {

				// Create image
				if (isset($_FILES['image'])) {
					$user = $model->user;
					$imagePath = $user->imagePath;

					if (!User::fileExists($imagePath))
						User::makeDirectory($imagePath);
					else
						User::emptyDirectory($imagePath);

					move_uploaded_file($_FILES['image']['tmp_name'], $imagePath.'avatar.jpg');
				}

				$form = Yii::createObject(SigninForm::className());
				$form->login = $model->email;
				$form->password = $model->password;
				if ($form->login(false)) {

					return [
						'token' => $form->user->tokenData,
						'user' => $form->user,
					];
				}
			}
			else
				throw new ErrorException($model);
		}
		else
			throw new ErrorException($model);

		throw new ErrorException(Error::ERROR_EMPTY_PARAMS);
	}

	/**
	 * @api {post} /users/signin Авторизация
	 * @apiName UserSignin
	 * @apiGroup User
	 * @apiVersion 1.0.0
	 * @apiPermission guest
	 *
	 * @apiParam {String} login E-mail или username
	 * @apiParam {String} password Пароль
	 *
	 * @apiSampleRequest /users/signin
	 *
	 * @apiUse ObjectUser
	 * @apiUse ErrorBlock
	 *
	 * @apiError ERROR_USER_FIELD_EMPTY_USERNAME Не указан обязательный параметр «login»
	 * @apiError ERROR_USER_FIELD_EMPTY_PASSWORD Не указан обязательный параметр «password»
	 * @apiError ERROR_USER_FIELD_INVALID_PASSWORD Неправильный логин или пароль
	 * @apiError ERROR_USER_STATUS_UNCONFIRMED Аккаунт не активирован
	 * @apiError ERROR_USER_STATUS_BLOCKED Аккаунт заблокирован
	 * @apiError ERROR_USER_STATUS_DELETED Аккаунт удален
	 */
	public function actionSignin() {
		if (!Yii::$app->user->isGuest)
			Yii::$app->user->logout();

		/** @var \api\modules\v1\models\forms\SigninForm $model */
		$model = Yii::createObject(SigninForm::className());

		$params = array();
		if (Yii::$app->request->post())
			$params['SigninForm'] = Yii::$app->request->post();

		// Validate and auth
		if ($model->load($params)) {
			if ($model->login()) {
			    $user = $model->user;

				return [
					'token' => $model->user->tokenData,
					'user' => $model->user,
				];
			}
			else
				throw new ErrorException($model);
		}

		throw new ErrorException(Error::ERROR_EMPTY_PARAMS);
	}

	/**
	 * @api {post} /users/forgot Востановление пароля
	 * @apiName UserForgot
	 * @apiGroup User
	 * @apiVersion 1.0.0
	 * @apiPermission guest
	 *
	 * @apiParam {String} email E-mail
	 *
	 * @apiSampleRequest /users/forgot
	 *
	 * @apiSuccessExample {json} Success-Response:
	 * HTTP/1.1 200 OK
	 * {
	 *     "success": true,
	 *     "data": {
	 *         "user_id": 2,
	 *         "email": @"demo@antikvarus.ru"
	 *     ],
	 *     "error": []
	 * }
	 *
	 * @apiUse ErrorBlock
	 *
	 * @apiError ERROR_USER_FIELD_EMPTY_EMAIL Не указан обязательный параметр «email»
	 * @apiError ERROR_USER_FIELD_INVALID_EMAIL Указан ошибочный параметр «email»
	 * @apiError ERROR_USER_FIELD_NOT_EXISTS_EMAIL Пользователь с таким E-mail не зарегистрирован
	 */
	public function actionForgot() {
		if (!Yii::$app->user->isGuest)
			Yii::$app->user->logout();

		if (!$this->module->enablePasswordRecovery)
			throw new ErrorException(Error::ERROR_FORGOT_DISABLED);

		/** @var \api\modules\v1\models\forms\ForgotForm $model */
		$model = Yii::createObject([
			'class' => ForgotForm::className(),
			'scenario' => ForgotForm::SCENARIO_REQUEST,
		]);

		$params = array();
		if (Yii::$app->request->post())
			$params['ForgotForm'] = Yii::$app->request->post();

		// Load params
		if ($model->load($params)) {
			if ($model->sendForgotMessage()) {
				return [
					'forgot' => [
						'user_id' => $model->user->id,
						'email' => $model->user->email,
					],
				];
			}
			else
				throw new ErrorException($model);
		}

		throw new ErrorException(Error::ERROR_EMPTY_PARAMS);
	}

	/**
	 * @api {get} /users/forgot/validate/:id/:code Востановление пароля - проверка
	 * @apiName UserForgotValidate
	 * @apiGroup User
	 * @apiVersion 1.0.0
	 * @apiPermission guest
	 *
	 * @apiParam {String} id ID пользователя
	 * @apiParam {String} code Код из E-mail
	 *
	 * @apiSampleRequest /users/forgot/:id/:code
	 *
	 * @apiSuccessExample {json} Success-Response:
	 * HTTP/1.1 200 OK
	 * {
	 *     "success": true,
	 *     "data": [
	 *         "user_id": 2,
	 *         "email": @"demo@antikvarus.ru",
	 *         "code": 0000
	 *     ],
	 *     "error": []
	 * }
	 *
	 * @apiUse ErrorBlock
	 *
	 * @apiError ERROR_USER_FIELD_NOT_EXISTS_TOKEN Токен неправильный или устарел
	 */
	public function actionForgotValidate($id, $code) {
		if (!Yii::$app->user->isGuest)
			Yii::$app->user->logout();

		if (!$this->module->enablePasswordRecovery)
			throw new ErrorException(Error::ERROR_FORGOT_DISABLED);

		/** @var \api\modules\v1\models\UserToken $token */
		$token = UserToken::find()->andWhere([
			'user_id' => $id,
			'code' => $code,
			'type' => UserToken::TYPE_RECOVERY_MOBILE,
		])->one();
		if ($token === null || $token->getIsExpired() || $token->user === null)
			throw new ErrorException(Error::ERROR_USER_FIELD_NOT_EXISTS_TOKEN);

		return [
			'forgot' => [
				'user_id' => $id,
				'email' => $token->user->email,
				'code' => $code,
			],
		];
	}

	/**
	 * @api {put} /users/forgot/complete/:id/:code Востановления пароля - завершение
	 * @apiName UserForgotComplete
	 * @apiGroup User
	 * @apiVersion 1.0.0
	 * @apiPermission guest
	 *
	 * @apiParam {String} id ID пользователя
	 * @apiParam {String} code Код из E-mail
	 * @apiParam {String} password Новый пароль
	 *
	 * @apiSampleRequest /users/forgot/complete
	 *
	 * @apiUse ObjectUser
	 * @apiUse ErrorBlock
	 *
	 * @apiError ERROR_USER_FIELD_NOT_EXISTS_TOKEN Токен неправильный или устарел
	 * @apiError ERROR_USER_FIELD_EMPTY_PASSWORD Не указан обязательный параметр «password»
	 * @apiError ERROR_USER_FIELD_SHORT_PASSWORD Значение «password» должно содержать минимум 4 символа
	 */
	public function actionForgotComplete($id, $code) {
		if (!Yii::$app->user->isGuest)
			Yii::$app->user->logout();

		if (!$this->module->enablePasswordRecovery)
			throw new ErrorException(Error::ERROR_FORGOT_DISABLED);

		/** @var \api\modules\v1\models\UserToken $token */
		$token = UserToken::find()->andWhere([
			'user_id' => $id,
			'code' => $code,
			'type' => UserToken::TYPE_RECOVERY_MOBILE,
		])->one();
		if ($token === null || $token->getIsExpired() || $token->user === null)
			throw new ErrorException(Error::ERROR_USER_FIELD_NOT_EXISTS_TOKEN);

		/** @var \api\modules\v1\models\User $user */
		$user = $token->user;

		/** @var \api\modules\v1\models\forms\ForgotForm $model */
		$model = Yii::createObject([
			'class' => ForgotForm::className(),
			'scenario' => ForgotForm::SCENARIO_RESET,
		]);

		$params = array();
		if (Yii::$app->request->post())
			$params['ForgotForm'] = Yii::$app->request->post();

		// Load params
		if ($model->load($params)) {
			if ($model->resetPassword($token)) {
				$form = Yii::createObject(SigninForm::className());
				$form->login = $user->email;
				$form->password = $model->password;

				if ($form->login(false)) {
					return [
						'token' => $user->tokenData,
						'user' => $user,
					];
				}
			}
			else
				throw new ErrorException($model);
		}

		throw new ErrorException(Error::ERROR_EMPTY_PARAMS);
	}

	/**
	 * @api {get} /users Список пользователей
	 * @apiName UserList
	 * @apiGroup User
	 * @apiVersion 1.0.0
	 * @apiPermission admin
	 *
	 * @apiHeader {String} Authorization Токен авторизации
	 *
	 * @apiSuccess (200) {Object} items Массив пользователей
	 * @apiSuccess (200) {Integer} items.id ID
	 * @apiSuccess (200) {String} items.username Никнейм
	 * @apiSuccess (200) {String} items.email E-mail
	 * @apiSuccess (200) {Object} items.profile Профиль
	 * @apiSuccess (200) {String} items.profile.last_name Фамилия
	 * @apiSuccess (200) {String} items.profile.first_name Имя
	 * @apiSuccess (200) {String} items.profile.middle_name Отчество
	 *
	 * @apiHeaderExample {json} Header-Example:
	 * Authorization: Bearer AosiQLOSjHxYHioWgFpoLtk99ZJ5tvsn
	 *
	 * @apiSampleRequest /users
	 *
	 * @apiSuccessExample {json} Success-Response:
	 * HTTP/1.1 200 OK
	 * {
	 *     "success": true,
	 *     "data": {
	 *         "items": [{
	 *             "id": 2,
	 *             "username": "appmake",
	 *             "email": "info@appmake.ru",
	 *             "profile": {
	 *                 "user_id": 2,
	 *                 "first_name": "Сергей",
	 *                 "last_name": "Сафронов",
	 *                 "middle_name": ""
	 *             }
	 *         }],
	 *     },
	 *     "error": []
	 * }
	 *
	 * @apiUse ErrorBlock
	 */

	/**
	 * @api {get} /users/profile Профиль пользователя
	 * @apiName UserProfile
	 * @apiGroup User
	 * @apiVersion 1.0.0
	 * @apiPermission user
	 *
	 * @apiSampleRequest /users/profile
	 *
	 * @apiHeader {String} Authorization Токен авторизации
	 *
	 * @apiUse ObjectUser
	 * @apiUse ErrorBlock
	 */
	public function actionProfileView() {

		// Get user object
		$user = Yii::$app->user->getIdentity();

		return [
			'token' => $user->tokenData,
			'user' => $user,
		];
	}

	/**
	 * @api {post} /users/profile Редактирование профиля
	 * @apiName UserProfileUpdate
	 * @apiGroup User
	 * @apiVersion 1.0.0
	 * @apiPermission guest
	 *
	 * @apiHeader {String} Authorization Токен авторизации
	 *
	 * @apiParam {String} first_name Имя
	 * @apiParam {String} [last_name] Фамилия
	 * @apiParam {String} [middle_name] Отчество
	 * @apiParam {String} [phone] Отчество
	 * @apiParam {Bool} [avatar_remove] Удаление аватара
	 *
	 * @apiSampleRequest /users/profile
	 *
	 * @apiUse ObjectUser
	 * @apiUse ErrorBlock
	 *
	 * @apiError ERROR_USER_FIELD_EMPTY_FIRST_NAME Не указан обязательный параметр «first_name»
	 * @apiError ERROR_USER_FIELD_SHORT_FIRST_NAME Значение «first_name» должно содержать минимум 4 символа
	 * @apiError ERROR_USER_FIELD_LONG_FIRST_NAME Значение «first_name» должно содержать максимум 50 символов
	 * @apiError ERROR_USER_FIELD_SHORT_FIRST_NAME Значение «last_name» должно содержать минимум 4 символа
	 * @apiError ERROR_USER_FIELD_LONG_FIRST_NAME Значение «last_name» должно содержать максимум 50 символов
	 * @apiError ERROR_USER_FIELD_SHORT_MIDDLE_NAME Значение «middle_name» должно содержать минимум 4 символа
	 * @apiError ERROR_USER_FIELD_LONG_MIDDLE_NAME Значение «middle_name» должно содержать максимум 50 символов
	 * @apiError ERROR_USER_FIELD_INVALID_PHONE Указан ошибочный параметр «phone»
	 */
	public function actionProfileUpdate() {

		/** @var \api\modules\v1\models\forms\ProfileForm $model */
		$model = Yii::createObject(ProfileForm::className());

		$params = array();
		if (Yii::$app->request->post())
			$params['ProfileForm'] = Yii::$app->request->post();

		// Load params
		if ($model->load($params)) {

			// Validate and save
			if ($model->save()) {
				$user = $model->user;
				$imagePath = $user->imagePath;
				$imageFilePath = $imagePath.'avatar.jpg';

				// Create image
				if (isset($_FILES['image'])) {

					if (!User::fileExists($imagePath))
						User::makeDirectory($imagePath);
					else
						User::emptyDirectoryCache($imagePath, 'avatar.jpg');

					move_uploaded_file($_FILES['image']['tmp_name'], $imageFilePath);

					// Clear avatar time
					$user->image_avatar_time = time();
					$user->save();
				}
				else if ($model->avatar_remove) {

					if (User::fileExists($imageFilePath)) {

						// Empty directory
						User::emptyDirectoryCache($imagePath, 'avatar.jpg');

						// Remove file
						unlink($imageFilePath);
					}

					// Clear avatar time
					$user->image_avatar_time = 0;
					$user->save();
				}

				return [
					'token' => $model->user->tokenData,
					'user' => $model->user,
				];
			}
			else
				throw new ErrorException($model);
		}
		else
			throw new ErrorException($model);

		throw new ErrorException(Error::ERROR_EMPTY_PARAMS);
	}

	/**
	 * @api {put} /users/profile/password Смена пароля
	 * @apiName UserProfilePasswordUpdate
	 * @apiGroup User
	 * @apiVersion 1.0.0
	 * @apiPermission guest
	 *
	 * @apiHeader {String} Authorization Токен авторизации
	 *
	 * @apiParam {String} password Новый пароль
	 *
	 * @apiSampleRequest /users/password
	 *
	 * @apiUse ObjectUser
	 * @apiUse ErrorBlock
	 *
	 * @apiError ERROR_USER_FIELD_EMPTY_PASSWORD Не указан обязательный параметр «password»
	 * @apiError ERROR_USER_FIELD_SHORT_PASSWORD Значение «password» должно содержать минимум 4 символа
	 */
	public function actionProfilePasswordUpdate() {

		/** @var \api\modules\v1\models\forms\PasswordForm $model */
		$model = Yii::createObject(PasswordForm::className());

		$params = array();
		if (Yii::$app->request->post())
			$params['PasswordForm'] = Yii::$app->request->post();

		// Load params
		if ($model->load($params)) {

			// Validate and save
			if ($model->save()) {
				return [
					'token' => $model->user->tokenData,
					'user' => $model->user,
				];
			}
			else
				throw new ErrorException($model);
		}
		else
			throw new ErrorException($model);

		throw new ErrorException(Error::ERROR_EMPTY_PARAMS);
	}
}