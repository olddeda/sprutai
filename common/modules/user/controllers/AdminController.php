<?php
namespace common\modules\user\controllers;

use Yii;
use yii\base\ExitException;
use yii\base\Model;
use yii\base\Module as Module2;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;

use client\components\Controller;

use common\modules\base\components\Debug;

use common\modules\user\Module;
use common\modules\user\Finder;
use common\modules\user\models\User;
use common\modules\user\models\UserProfile;
use common\modules\user\models\UserLog;
use common\modules\user\models\search\UserSearch;
use common\modules\user\models\search\UserLogSearch;

/**
 * AdminController allows you to administrate users.
 *
 * @property \common\modules\user\Module $module
 */
class AdminController extends Controller
{
	/** @var Finder */
	protected $finder;

	/**
	 * @param string $id
	 * @param Module2 $module
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
		return ArrayHelper::merge(parent::behaviors(), [
			'verbs' => [
				'class' => VerbFilter::class,
				'actions' => [
					'delete' => ['post'],
					'confirm' => ['post'],
					'block' => ['post'],
				],
			],
		]);
	}

	/**
	 * List all User models.
	 *
	 * @return string
	 * @throws \yii\base\InvalidConfigException
	 */
	public function actionIndex() {
		Url::remember('', 'actions-redirect');
		
		$searchModel = Yii::createObject(UserSearch::class);
		$dataProvider = $searchModel->search(Yii::$app->request->get());
		
		return $this->render('index', [
			'dataProvider' => $dataProvider,
			'searchModel' => $searchModel,
		]);
	}

	/**
	 * Create a new User model.
	 * If creation is successful, the browser will be redirected to the 'index' page.
	 *
	 * @return string|Response
	 * @throws ExitException
	 * @throws \yii\base\InvalidConfigException
	 */
	public function actionCreate() {

		/**
		 * Create model
		 * @var User $user
		 */
		$user = Yii::createObject([
			'class' => User::class,
			'scenario' => User::SCENARIO_CREATE,
		]);

		// Enable AJAX validate
		$this->performAjaxValidation($user);

		// Validate and save
		if ($user->load(Yii::$app->request->post()) && $user->create()) {
			Yii::$app->getSession()->setFlash('success', Yii::t('user', 'message_user_has_been_created'));

			// Redirect to update
			return $this->redirect([
				'update',
				'id' => $user->id
			]);
		}

		// Render view
		return $this->render('create', [
			'user' => $user,
		]);
	}

	/**
	 * Update an existing User model.
	 *
	 * @param int $id
	 *
	 * @return string|Response
	 * @throws ExitException
	 * @throws NotFoundHttpException
	 */
	public function actionUpdate($id) {
		Url::remember('', 'actions-redirect');

		// Find model
		$user = $this->findModel($id);
		$user->scenario = User::SCENARIO_UPDATE;

		// Enable AJAX validation
		$this->performAjaxValidation($user);

		// Validate and save
		if ($user->load(Yii::$app->request->post()) && $user->save()) {
			Yii::$app->getSession()->setFlash('success', Yii::t('user', 'message_user_details_have_been_updated'));

			return $this->refresh();
		}

		// Render view
		return $this->render('_account', [
			'user' => $user,
		]);
	}

	/**
	 * Updates an existing profile.
	 *
	 * @param int $id
	 *
	 * @return string|Response
	 * @throws ExitException
	 * @throws NotFoundHttpException
	 * @throws \yii\base\InvalidConfigException
	 */
	public function actionUpdateProfile($id) {
		Url::remember('', 'actions-redirect');

		// Find user model
		$user = $this->findModel($id);

		// Find profile model
		$profile = $user->profile;

		// Create profile model if need
		if ($profile == null) {
			$profile = Yii::createObject(UserProfile::className());
			$profile->link('user', $user);
		}

		// Enable AJAX validation
		$this->performAjaxValidation($profile);

		// Validate and save
		if ($profile->load(Yii::$app->request->post()) && $profile->save()) {
			Yii::$app->getSession()->setFlash('success', Yii::t('user', 'message_user_profile_details_have_been_updated'));

			// Refresh page
			return $this->refresh();
		}

		// Render view
		return $this->render('_profile', [
			'user' => $user,
			'profile' => $profile,
		]);
	}

	/**
	 * Shows information about user.
	 *
	 * @param int $id
	 *
	 * @return string
	 * @throws NotFoundHttpException
	 */
	public function actionInfo($id) {
		Url::remember('', 'actions-redirect');

		// Find user model
		$user = $this->findModel($id);

		// Render view
		return $this->render('_info', [
			'user' => $user,
		]);
	}

	/**
	 * Shows information about user.
	 *
	 * @param int $id
	 *
	 * @return string
	 * @throws NotFoundHttpException
	 */
	public function actionLog($id) {
		Url::remember('', 'actions-redirect');

		// Find user model
		$user = $this->findModel($id);

		$params = Yii::$app->request->get();

		$searchModel = new UserLogSearch();
		$searchModel->user_id = $user->id;
		$dataProvider = $searchModel->search(Yii::$app->request->get());

		// Render view
		return $this->render('_log', [
			'user' => $user,
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	/**
	 * If "rbac" extension is installed, this page displays form
	 * where user can assign multiple auth items to user.
	 *
	 * @param int $id
	 *
	 * @return string
	 * @throws NotFoundHttpException
	 */
	public function actionAssignments($id) {
		if (!isset(Yii::$app->modules['rbac'])) {
			throw new NotFoundHttpException();
		}

		Url::remember('', 'actions-redirect');

		// Find user model
		$user = $this->findModel($id);

		// Render view
		return $this->render('_assignments', [
			'user' => $user,
		]);
	}

	/**
	 * Confirm the User.
	 *
	 * @param int $id
	 *
	 * @return Response
	 * @throws NotFoundHttpException
	 */
	public function actionConfirm($id) {
		$this->findModel($id)->confirm();

		Yii::$app->getSession()->setFlash('success', Yii::t('user', 'message_user_has_been_confirmed'));

		return $this->redirect(Url::previous('actions-redirect'));
	}

	/**
	 * Delete an existing User model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 *
	 * @param int $id
	 *
	 * @return Response
	 * @throws \Throwable
	 * @throws \yii\db\StaleObjectException
	 */
	public function actionDelete($id) {
		if ($id == Yii::$app->user->getId()) {
			Yii::$app->getSession()->setFlash('danger', Yii::t('user', 'message_you_can_not_remove_your_own_account'));
		}
		else {
			$user = User::findOwn($id, true, 'user');
			$user->delete();
			Yii::$app->getSession()->setFlash('success', Yii::t('user', 'message_user_has_been_deleted'));
		}

		return $this->redirect(['index']);
	}

	/**
	 * Block the user.
	 *
	 * @param int $id
	 *
	 * @return Response
	 * @throws NotFoundHttpException
	 * @throws \yii\base\Exception
	 */
	public function actionBlock($id) {
		if ($id == Yii::$app->user->getId()) {
			Yii::$app->getSession()->setFlash('danger', Yii::t('user', 'message_you_can_not_block_your_own_account'));
		}
		else {
			$user = $this->findModel($id);
			if ($user->getIsBlocked()) {
				$user->unblock();
				Yii::$app->getSession()->setFlash('success', Yii::t('user', 'message_user_has_been_unblocked'));
			}
			else {
				$user->block();
				Yii::$app->getSession()->setFlash('success', Yii::t('user', 'message_user_has_been_blocked'));
			}
		}

		return $this->redirect(Url::previous('actions-redirect'));
	}
	
	/**
	 * Login by user
	 *
	 * @param int $id
	 *
	 * @return Response
	 * @throws NotFoundHttpException
	 * @throws \Throwable
	 */
	public function actionSignin($id) {
		$model = $this->findModel($id);
		
		$adminId = Yii::$app->getSession()->get('admin_id', Yii::$app->getUser()->id);
		
		Yii::$app->getUser()->logout();
		
		Yii::$app->getSession()->set('admin_id', $adminId);
		
		Yii::$app->getUser()->login($model, $this->module->rememberFor);
		
		$url = (Yii::$app->user->getLevel() == 1) ? Url::home() : ['index'];
		return $this->redirect($url);
	}
	
	/**
	 * Logout from user
	 *
	 * @return Response
	 * @throws NotFoundHttpException
	 * @throws \Throwable
	 */
	public function actionLogout() {
		$adminID = Yii::$app->getSession()->get('admin_id');
		if ($adminID) {
			Yii::$app->getSession()->remove('admin_id');
			
			$model = $this->findModel($adminID, false);
			Yii::$app->getUser()->login($model, $this->module->rememberFor);
			return $this->redirect(['index']);
		}
		else {
			Yii::$app->user->logout();
			return $this->redirect(Url::home());
		}
	}

	/**
	 * Find the User model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 *
	 * @param int $id
	 * @param boolean $break
	 *
	 * @return User the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel($id, $break = true) {
		$user = $this->finder->findUserById($id);
		if ($user === null || ($break && !Yii::$app->user->canAccess($user, true))) {
			throw new NotFoundHttpException(Yii::t('user', 'error_access'));
		}
		return $user;
	}
}
