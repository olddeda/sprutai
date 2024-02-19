<?php
namespace common\modules\user\controllers;

use Yii;
use yii\authclient\AuthAction;
use yii\authclient\ClientInterface;
use yii\base\ExitException;
use yii\base\InvalidConfigException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\web\Response;

use common\modules\base\components\Controller;

use common\modules\base\components\Debug;

use common\modules\user\Module;
use common\modules\user\Finder;
use common\modules\user\models\User;
use common\modules\user\models\UserAccount;
use common\modules\user\models\forms\SigninForm;
use common\modules\user\traits\AjaxValidationTrait;

/**
 * Controller that manages user authentication process.
 *
 * @property Module $module
 */
class SecurityController extends Controller
{
    /** @var Finder */
    protected $finder;

    /**
     * @param string $id
     * @param Module $module
     * @param Finder $finder
     * @param array $config
     */
    public function __construct($id, $module, Finder $finder, $config = []) {
        $this->finder = $finder;
        parent::__construct($id, $module, $config);
    }

    /**
	 * @inheritdoc
	 */
    public function behaviors() {
        return array_merge(parent::behaviors(), [
			'access' => [
				'class' => AccessControl::class,
				'rules' => [
					[
						'allow' => true,
						'actions' => ['signin', 'auth', 'blocked'],
						'roles' => ['?']
					],
					[
						'allow' => true,
						'actions' => ['signin', 'auth', 'logout'],
						'roles' => ['@']
					],
				],
			],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ]);
    }

    /**
	 * @inheritdoc
	 */
    public function actions() {
        return [
            'auth' => [
                'class' => AuthAction::class,
                'successCallback' => Yii::$app->user->isGuest ? [$this, 'authenticate'] : [$this, 'connect'],
            ],
        ];
    }

    /**
     * Displays the login page.
     *
	 * @return string|Response
	 * @throws \Throwable
	 * @throws ExitException
	 * @throws InvalidConfigException
	 */
    public function actionSignin() {
        if (!Yii::$app->user->isGuest)
            $this->goHome();

        /**
		 * @var SigninForm $model
		 */
        $model = Yii::createObject(SigninForm::class);

		// Enable AJAX validation
        $this->performAjaxValidation($model);

		// Validate and login
        if ($model->load(Yii::$app->getRequest()->post()) && $model->login()) {
            return $this->goBack();
        }

        return $this->render('login', [
            'model'  => $model,
            'module' => $this->module,
        ]);
    }

    /**
     * Log the user out and then redirects to the homepage.
     *
     * @return Response
     */
    public function actionLogout() {
		Yii::$app->getSession()->remove('admin_id');
        Yii::$app->getUser()->logout();

        return $this->goHome();
    }
    
    /**
     * Tries to authenticate user via social network. If user has already used
     * this network's account, he will be logged in. Otherwise, it will try
     * to create new user account.
     *
	 * @param ClientInterface $client
	 *
	 * @throws InvalidConfigException
	 */
    public function authenticate(ClientInterface $client) {
        $account = $this->finder->findAccount()->byClient($client)->one();
        if ($account === null)
            $account = UserAccount::create($client);

        if ($account->user instanceof User) {
            if ($account->user->isBlocked) {
                Yii::$app->session->setFlash('danger', Yii::t('user', 'Your account has been blocked.'));
                $this->action->successUrl = Url::to(['/user/security/signin']);
            }
			else {
                Yii::$app->user->login($account->user, $this->module->rememberFor);
                $this->action->successUrl = Yii::$app->getUser()->getReturnUrl();
            }
        }
		else {
            $this->action->successUrl = $account->getConnectUrl();
        }
    }

    /**
     * Tries to connect social account to user.
     *
	 * @param ClientInterface $client
	 *
	 * @throws InvalidConfigException
	 */
    public function connect(ClientInterface $client) {
    
		/**
		 * @var UserAccount $account
		 */
        $account = Yii::createObject(UserAccount::class);
        $account->connectWithUser($client);

        $this->action->successUrl = Url::to(['/user/settings/networks']);
    }
}
