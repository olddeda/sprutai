<?php
namespace api\modules\v1\controllers\user;

use Yii;
use yii\base\Exception;
use yii\base\InvalidConfigException;
use yii\base\NotSupportedException;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\filters\VerbFilter;
use yii\authclient\ClientInterface;
use yii\authclient\OAuth1;
use yii\authclient\OAuth2;
use yii\authclient\OpenId;

use common\modules\user\Finder;
use common\modules\user\Module;

use api\components\ErrorException;
use api\helpers\enum\Error;

use api\modules\v1\components\Controller;
use api\models\user\User;
use api\models\user\UserToken;
use api\models\user\UserAccount;

use api\forms\SigninForm;
use api\forms\SignupForm;
use api\forms\ForgotForm;

class AuthController extends Controller
{
	/**
	 * @var string the model class name. This property must be set.
	 */
	public $modelClass = 'api\models\user\User';

	/**
	 * @var Module
	 */
	public $module;

	/**
	 * @var Finder
	 */
	public $finder;

    /**
     * @param string $id
     * @param Module $module
     * @param Finder $finder
     * @param array $config
     */
    public function __construct($id, $module, Finder $finder, $config = []) {
        $this->module = $module;
        $this->finder = $finder;
        parent::__construct($id, $module, $config);
    }

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
				'except' => ['signin', 'signup', 'signup-complete', 'forgot', 'forgot-validate', 'forgot-complete', 'social'],
			],
			'access' => [
				'except' => ['signin', 'signup', 'signup-complete', 'forgot', 'forgot-validate', 'forgot-complete', 'me', 'social', 'social-me'],
			],
			'verbs' => [
				'class' => VerbFilter::class,
				'actions' => [
					'signin' => ['post'],
					'signup' => ['post'],
					'forgot' => ['post'],
					'forgot-validate' => ['post'],
					'forgot-complete' => ['post'],
					'me' => ['get'],
                    'logout' => ['post'],
				],
			],
		]);
    }
    
    /**
     * @OA\Post (path="/user/auth/signup",
     *     tags={"auth"},
     *     summary="Регистрация пользователя",
     *     operationId="auth_signup",
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         description="Е-mail пользователя",
     *         required=true,
     *         @OA\Schema(
     *            type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="password",
     *         in="query",
     *         description="Пароль пользователя",
     *         required=true,
     *         @OA\Schema(
     *            type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Пользователь успешно создан",
     *         @OA\Schema(
     *              @OA\Property(property="token", type="object",
     *                  @OA\Property(property="code", type="string"),
     *                  @OA\Property(property="expire", type="integer")
     *              ),
     *              @OA\Property(property="user", type="object", ref="#/definitions/User")
     *         ),
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description=">-
     *                      100 - Не указаны параметры
                            1000 - Не указан обязательный параметр «email»
                            1001 - Не указан обязательный параметр «password»
                            1011 - Значение «password» должно содержать минимум 4 символа
                            1030 - Указан ошибочный параметр «email»
                            1040 - Пользователь с таким E-mail уже существует"
     *     ),
     * )
     */
	public function actionSignup() {
		if (!Yii::$app->user->isGuest)
			Yii::$app->user->logout();

		if (!$this->module->enableRegistration)
			throw new ErrorException(Error::ERROR_REGISTRATION_DISABLED);

		/** @var \api\forms\SignupForm $model */
		$model = Yii::createObject(SignupForm::class);

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

				$form = Yii::createObject(SigninForm::class);
				$form->email = $model->email;
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
     * @OA\Post (path="/user/auth/signin",
     *     tags={"auth"},
     *     summary="Авторизация пользователя",
     *     operationId="auth_signin",
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         description="Е-mail пользователя",
     *         required=true,
     *         @OA\Schema(
     *            type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="password",
     *         in="query",
     *         description="Пароль пользователя",
     *         required=true,
     *         @OA\Schema(
     *            type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Авторизация прошла успешно",
     *         @OA\Schema(
     *              @OA\Property(property="token", type="object",
     *                  @OA\Property(property="code", type="string"),
     *                  @OA\Property(property="expire", type="integer")
     *              ),
     *              @OA\Property(property="user", type="object", ref="#/definitions/User")
     *         ),
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description=">-
     *                      100 - Не указаны параметры
                            1000 - Не указан обязательный параметр «email»
                            1001 - Не указан обязательный параметр «password»
                            1032 - Неправильный email или пароль
                            1060 - Аккаунт не активирован
                            1061 - Аккаунт заблокирован
                            1062 - Аккаунт удален"
     *     ),
     * )
     */
	public function actionSignin() {
		if (!Yii::$app->user->isGuest)
			Yii::$app->user->logout();

		/** @var \api\forms\SigninForm $model */
		$model = Yii::createObject(SigninForm::class);

		$params = [];
		if (Yii::$app->request->post())
			$params['SigninForm'] = Yii::$app->request->post();

		// Validate and auth
		if ($model->load($params)) {
			if ($model->login()) {
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
     * @OA\Post (path="/user/auth/forgot",
     *     tags={"auth"},
     *     summary="Восстановление пароля пользователя",
     *     operationId="auth_forgot",
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         description="Е-mail пользователя",
     *         required=true,
     *         @OA\Schema(
     *            type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Код востановления был отправлен пользователю",
     *         @OA\Schema(
     *              @OA\Property(property="forgot", type="object",
     *                  @OA\Property(property="user_id", type="integer"),
     *                  @OA\Property(property="email", type="string")
     *              ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description=">-
     *                      100 - Не указаны параметры
                            103 - Восстановление пароля отключено
                            1000 - Не указан обязательный параметр «email»
                            1032 - Указан ошибочный параметр «email»
                            1050 - Пользователь с таким E-mail не зарегистрирован
                            1060 - Аккаунт не активирован
                            1061 - Аккаунт заблокирован
                            1062 - Аккаунт удален"
     *     ),
     * )
     */
	public function actionForgot() {
		if (!Yii::$app->user->isGuest)
			Yii::$app->user->logout();

		if (!$this->module->enablePasswordRecovery)
			throw new ErrorException(Error::ERROR_FORGOT_DISABLED);

		/** @var \api\forms\ForgotForm $model */
		$model = Yii::createObject([
			'class' => ForgotForm::class,
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
     * @OA\Post (path="/user/auth/forgot/{id}/{code}",
     *     tags={"auth"},
     *     summary="Восстановление пароля пользователя - проверка кода",
     *     operationId="auth_forgot_validate",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID пользователя",
     *         required=true,
     *         @OA\Schema(
     *            type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="code",
     *         in="path",
     *         description="Код восстановления",
     *         required=true,
     *         @OA\Schema(
     *            type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Код востановления был успешно проверен",
     *         @OA\Schema(
     *              @OA\Property(property="forgot", type="object",
     *                  @OA\Property(property="user_id", type="integer"),
     *                  @OA\Property(property="code", type="integer"),
     *                  @OA\Property(property="email", type="string")
     *              ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description=">-
     *                      100 - Не указаны параметры
                            103 - Восстановление пароля отключено
                            1052 - Код не найден или срок жизни истек"
     *     ),
     * )
     */
	public function actionForgotValidate($id, $code) {
		if (!Yii::$app->user->isGuest)
			Yii::$app->user->logout();

		if (!$this->module->enablePasswordRecovery)
			throw new ErrorException(Error::ERROR_FORGOT_DISABLED);

		/** @var \api\models\user\UserToken $token */
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
     * @OA\Post (path="/user/auth/forgot/complete/{id}/{code}",
     *     tags={"auth"},
     *     summary="Восстановление пароля пользователя - сохранение нового пароля",
     *     operationId="auth_forgot_complete",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID пользователя",
     *         required=true,
     *         @OA\Schema(
     *            type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="code",
     *         in="path",
     *         description="Код восстановления",
     *         required=true,
     *         @OA\Schema(
     *            type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="password",
     *         in="query",
     *         description="Пароль пользователя",
     *         required=true,
     *         @OA\Schema(
     *            type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Пароль пользователя был успешно изменен",
     *         @OA\Schema(
     *              @OA\Property(property="token", type="object",
     *                  @OA\Property(property="code", type="string"),
     *                  @OA\Property(property="expire", type="integer")
     *              ),
     *              @OA\Property(property="user", type="object", ref="#/definitions/User")
     *         ),
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description=">-
     *                      100 - Не указаны параметры
                            103 - Восстановление пароля отключено
                            1001 - Не указан обязательный параметр «password»
                            1011 - Значение «password» должно содержать минимум 4 символа
                            1052 - Код не найден или срок жизни истек"
     *     ),
     * )
     */
	public function actionForgotComplete($id, $code) {
		if (!Yii::$app->user->isGuest)
			Yii::$app->user->logout();

		if (!$this->module->enablePasswordRecovery)
			throw new ErrorException(Error::ERROR_FORGOT_DISABLED);

		/** @var \api\models\user\UserToken $token */
		$token = UserToken::find()->andWhere([
			'user_id' => $id,
			'code' => $code,
			'type' => UserToken::TYPE_RECOVERY_MOBILE,
		])->one();
		if ($token === null || $token->getIsExpired() || $token->user === null)
			throw new ErrorException(Error::ERROR_USER_FIELD_NOT_EXISTS_TOKEN);

		/** @var \api\models\user\User $user */
		$user = $token->user;

		/** @var \api\forms\ForgotForm $model */
		$model = Yii::createObject([
			'class' => ForgotForm::class,
			'scenario' => ForgotForm::SCENARIO_RESET,
		]);

		$params = array();
		if (Yii::$app->request->post())
			$params['ForgotForm'] = Yii::$app->request->post();

		// Load params
		if ($model->load($params)) {
			if ($model->resetPassword($token)) {
				$form = Yii::createObject(SigninForm::class);
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
     * @OA\Post (path="/user/auth/logout",
     *     tags={"auth"},
     *     summary="Выход пользователя",
     *     operationId="auth_logout",
     *     security={{"Bearer":{}}},
     *     @OA\Parameter(
     *          name="Authorization",
     *          in="header",
     *          required=false,
     *          description="Authorization",
     *          @OA\Schema(
     *            type="string",
     *            default="Bearer "
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Успешно",
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Пользователь не авторизован"
     *     ),
     * )
     */
    public function actionLogout() {
        
        // Get user object
        Yii::$app->user->logout();
        
        return [];
    }
	
    /**
     * @OA\Get (path="/user/auth/me",
     *     tags={"auth"},
     *     summary="Получение данных пользователя",
     *     operationId="auth_me",
     *     security={{"Bearer":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Данные пользователя",
     *         @OA\Schema(
     *              @OA\Property(property="token", type="object",
     *                  @OA\Property(property="code", type="string"),
     *                  @OA\Property(property="expire", type="integer")
     *              ),
     *              @OA\Property(property="user", type="object", ref="#/definitions/User")
     *         ),
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Пользователь не авторизован"
     *     ),
     * )
     */
	public function actionMe() {
		$user = User::find()->where(['id' => Yii::$app->user->id])->one();
		return [
			'token' => $user->tokenData,
			'user' => $user,
		];
	}

    /**
     * @param $provider
     *
     * @return Response
     * @throws Exception
     * @throws NotFoundHttpException
     * @throws NotSupportedException
     * @throws InvalidConfigException
     */
	public function actionSocial($provider) {
        $collection = Yii::$app->get('authClientCollection');
        if (!$collection->hasClient($provider)) {
            throw new NotFoundHttpException("Unknown auth client '{$provider}'");
        }

        $client = $collection->getClient($provider);
        if ($client instanceof OAuth2) {
            return $this->authOAuth2($client);
        }

        throw new NotSupportedException('Provider "' . get_class($client) . '" is not supported.');
    }

    /**
     * @return array
     * @throws \Throwable
     */
    public function actionSocialMe() {

        // Get user object
        return [
            'root' => Yii::$app->user->getIdentity(),
        ];
    }

    /**
     * Performs OAuth2 auth flow.
     * @param OAuth2 $client auth client instance.
     * @return Response action response.
     * @throws \yii\base\Exception on failure.
     */
    protected function authOAuth2($client)
    {
        $request = Yii::$app->getRequest();

        if (($error = $request->post('error')) !== null) {
            if (
                $error === 'access_denied' ||
                $error === 'user_cancelled_login' ||
                $error === 'user_cancelled_authorize'
            ) {
                // user denied error
                return $this->authCancel($client);
            }
            // request error
            $errorMessage = $request->get('error_description', $request->post('error_message'));
            if ($errorMessage === null) {
                $errorMessage = http_build_query($request->post());
            }
            throw new Exception('Auth error: ' . $errorMessage);
        }

        // Get the access_token and save them to the session.
        if (($code = $request->post('code')) !== null) {
            $token = $client->fetchAccessToken($code);
            if (!empty($token)) {
                return $this->authenticate($client);
            }
        }

        throw new NotFoundHttpException("Unknown error");
    }

    /**
     * @param ClientInterface $client
     *
     * @return array
     * @throws Exception
     * @throws InvalidConfigException
     */
    public function authenticate(ClientInterface $client) {
        $account = UserAccount::find()->byClient($client)->one();

        if (!is_null($account) && is_null($account->user)) {
            $account->delete();
            $account = null;
        }

        if (is_null($account)) {
            $account = UserAccount::create($client);
        }

        if ($account->user instanceof User) {
            if ($account->user->isBlocked) {
                throw new Exception('Your account has been blocked');
            }

            if (Yii::$app->user->login($account->user, 0)) {
                $token = $account->user->token;
                if (!$token) {
                    $token = new UserToken();
                    $token->type = UserToken::TYPE_API;
                    $token->user_id = $account->user->id;
                }
                $token->save();

                return [
                    'token' => $account->user->tokenData,
                    'access_token' => $account->user->tokenData['code'],
                    'user' => $account->user,
                ];
            }
        }

        throw new Exception('Cannot auth by social');
    }

}