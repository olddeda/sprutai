<?php

namespace common\modules\user\controllers;

use common\modules\user\models\UserAccount;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

use common\modules\base\components\Controller;

use common\modules\user\Module;
use common\modules\user\Finder;
use common\modules\user\traits\AjaxValidationTrait;
use common\modules\user\models\forms\SettingsForm;

/**
 * SettingsController manages updating user settings (e.g. profile, email and password).
 *
 * @property \common\modules\user\Module $module
 */
class SettingsController extends Controller
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
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'disconnect' => ['post'],
                ],
            ],
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['profile', 'account', 'address', 'confirm', 'networks', 'subscribe', 'disconnect', 'telegram-connect', 'telegram-disconnect'],
                        'roles' => ['@'],
                    ],
                ],
            ],
        ]);
    }

    /**
     * Shows profile settings form.
     *
	 * @return string|\yii\web\Response
	 * @throws \yii\base\ExitException
	 */
    public function actionProfile() {

		// Find model
        $model = $this->finder->findProfileById(Yii::$app->user->identity->getId());

		// Enable AJAX validation
        $this->performAjaxValidation($model);

		// Validate and save
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->getSession()->setFlash('success', Yii::t('user', 'message_your_profile_has_been_updated'));

			// Refresh
            return $this->refresh();
        }

		// Render view
        return $this->render('profile', [
            'model' => $model,
        ]);
    }

    /**
     * Displays page where user can update account settings (username, email or password).
     *
	 * @return string|\yii\web\Response
	 * @throws \yii\base\ExitException
	 * @throws \yii\base\InvalidConfigException
	 */
    public function actionAccount() {

        // Find model
        $model = Yii::createObject(SettingsForm::class);

		// Enable AJAX validate
        $this->performAjaxValidation($model);

		// Validate and save
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', Yii::t('user', 'message_your_account_have_been_updated'));

			// Refresh
            return $this->refresh();
        }

		// Render view
        return $this->render('account', [
            'model' => $model,
        ]);
    }
	
	/**
	 * Shows profile address
	 *
	 * @return string|\yii\web\Response
	 * @throws \yii\base\ExitException
	 */
	public function actionAddress() {
		
		// Find model
		$model = $this->finder->findProfileById(Yii::$app->user->identity->getId());
		
		// Enable AJAX validation
		//$this->performAjaxValidation($model);
		
		// Validate and save
		//if ($model->load(Yii::$app->request->post()) && $model->save()) {
		//	Yii::$app->getSession()->setFlash('success', Yii::t('user', 'message_your_profile_has_been_updated'));
		//
		//	// Refresh
		//	return $this->refresh();
		//}
		
		// Render view
		return $this->render('address', [
			'model' => $model,
		]);
	}

    /**
     * Attempt changing user's password.
     *
     * @param int $id
     * @param string $code
     *
     * @return string
     * @throws \yii\web\HttpException
     */
    public function actionConfirm($id, $code) {

		// Find model
        $user = $this->finder->findUserById($id);

		// Check user and email change strategy
        if ($user === null || $this->module->emailChangeStrategy == Module::STRATEGY_INSECURE)
            throw new NotFoundHttpException();

        $user->attemptEmailChange($code);

        return $this->redirect(['account']);
    }

    /**
     * Display list of connected network accounts.
     *
     * @return string
     */
    public function actionNetworks() {
        return $this->render('networks', [
            'user' => Yii::$app->user->identity,
        ]);
    }
    
    public function actionSubscribe() {
	
		/**
		 * @var \common\modules\user\models\User $user
		 */
    	$user = Yii::$app->user->identity;
	
		/**
		 * @var \common\modules\user\models\UserSubscribe $model
		 */
		$model = $user->subscribe;
	
		// Enable AJAX validation
		$this->performAjaxValidation($model);
	
		// Validate and save
		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			Yii::$app->getSession()->setFlash('success', Yii::t('user-subscribe', 'message_update_success'));
		
			// Refresh
			return $this->refresh();
		}
		
		return $this->render('subscribe', [
			'model' => $model,
		]);
	}

    /**
     * Disconnect a network account from user.
     *
     * @param int $id
     *
     * @return \yii\web\Response
     * @throws \yii\web\NotFoundHttpException
     * @throws \yii\web\ForbiddenHttpException
     */
    public function actionDisconnect($id) {
        $account = $this->finder->findAccount()->byId($id)->one();
        if ($account === null)
            throw new NotFoundHttpException();

        if ($account->user_id != Yii::$app->user->id)
            throw new ForbiddenHttpException();

        $account->delete();

        return $this->redirect(['networks']);
    }
    
	public function actionTelegramConnect() {
    	$clients = Yii::$app->authClientCollection->clients;
    	if (!isset($clients['telegram'])) {
    		return $this->redirect(['settings/networks']);
		}
		
		/**
		 * @var \common\modules\user\clients\Telegram $client
		 */
    	$client = $clients['telegram'];
  
		/**
		 * @var \common\modules\user\models\UserAccount $account
		 */
    	$account = UserAccount::find()->where([
    		'user_id' => Yii::$app->user->id,
			'provider' => $client->getId(),
		])->one();
    	if (is_null($account)) {
    		$code = UserAccount::generateCode($client);
    		
			$account = Yii::createObject(UserAccount::class);
			$account->setAttributes([
				'user_id' => Yii::$app->user->id,
				'provider' => $client->getId(),
				'code' => $code,
			], false);
			$account->save();
		}
    	
    	return $this->redirect(['settings/networks']);
	}
	
	public function actionTelegramDisconnect() {
	
	}
}
