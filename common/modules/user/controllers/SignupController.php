<?php

namespace common\modules\user\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;

use common\modules\base\components\Controller;

use common\modules\user\Finder;
use common\modules\user\models\User;
use common\modules\user\models\forms\SignupForm;
use common\modules\user\models\forms\ResendForm;

/**
 * RegistrationController is responsible for all registration process, which includes registration of a new account,
 * resending confirmation tokens, email confirmation and registration via social networks.
 *
 * @property \common\modules\user\Module $module
 */
class SignupController extends Controller
{
    /**
	 * @var \common\modules\user\Finder
	 */
    protected $finder;

    /**
     * @param string $id
     * @param \yii\base\Module $module
     * @param \common\modules\user\Finder $finder
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
				'class' => AccessControl::className(),
				'rules' => [
					[
						'allow' => true,
						'actions' => ['index', 'connect'],
						'roles' => ['?']],
					[
						'allow' => true,
						'actions' => ['confirm', 'resend'],
						'roles' => ['?', '@']
					],
				],
			],
		]);
    }

    /**
     * Display the registration page.
     * After successful registration if enableConfirmation is enabled shows info message otherwise redirects to home page.
     *
     * @return string
     * @throws \yii\web\HttpException
     */
    public function actionIndex() {
        if (!$this->module->enableRegistration)
            throw new NotFoundHttpException();

        /** @var SignupForm $model */
        $model = Yii::createObject(SignupForm::className());

		// Enable ajax validate
        $this->performAjaxValidation($model);

		// Validate and save
        if ($model->load(Yii::$app->request->post()) && $model->signup()) {

			// Render view
            return $this->render('index_complete', [
                'title'=> Yii::t('user', 'message_your_account_has_been_created'),
                'module' => $this->module,
            ]);
        }

		// Render view
        return $this->render('index', [
            'model' => $model,
            'module' => $this->module,
        ]);
    }

    /**
     * Display page where user can create new account that will be connected to social account.
     *
     * @param string $code
     *
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionConnect($code) {

		// Find account by code
        $account = $this->finder->findAccount()->byCode($code)->one();

        if ($account === null || $account->getIsConnected())
            throw new NotFoundHttpException();

        /** @var User $user */
        $user = Yii::createObject([
            'class' => User::className(),
            'scenario' => User::SCENARIO_CONNECT,
            'username' => $account->username,
            'email' => $account->email,
        ]);

		// Validate and create
        if ($user->load(Yii::$app->request->post()) && $user->create()) {

			// Connect to user
            $account->connect($user);

			// Auth user
            Yii::$app->user->login($user, $this->module->rememberFor);

			// Return
            return $this->goBack();
        }

		// Render view
        return $this->render('connect', [
            'model' => $user,
            'account' => $account,
        ]);
    }

    /**
     * Confirm user's account. If confirmation was successful logs the user and shows success message. Otherwise
     * shows error message.
     *
     * @param int $id
     * @param string $code
     *
     * @return string
     * @throws \yii\web\HttpException
     */
    public function actionConfirm($id, $code) {
        $user = $this->finder->findUserById($id);

        if ($user === null || $this->module->enableConfirmation == false)
            throw new NotFoundHttpException();

        $user->attemptConfirmation($code);

        return $this->render('confirm_complete', [
            'title'  => Yii::t('user', 'message_account_confirmation'),
            'module' => $this->module,
        ]);
    }

    /**
     * Displays page where user can request new confirmation token. If resending was successful, displays message.
     *
     * @return string
     * @throws \yii\web\HttpException
     */
    public function actionResend() {
        if ($this->module->enableConfirmation == false)
            throw new NotFoundHttpException();

        /** @var ResendForm $model */
        $model = Yii::createObject(ResendForm::className());

        $this->performAjaxValidation($model);

        if ($model->load(Yii::$app->request->post()) && $model->resend()) {
            return $this->render('resend_complete', [
                'title'  => Yii::t('user', 'message_a_new_confirmation_link_has_been_sent'),
                'module' => $this->module,
            ]);
        }

        return $this->render('resend', [
            'model' => $model,
        ]);
    }
}
