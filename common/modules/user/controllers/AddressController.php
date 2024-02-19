<?php
namespace common\modules\user\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\filters\Cors;

use common\modules\base\components\Controller;

use common\modules\user\traits\AjaxValidationTrait;
use common\modules\user\models\User;
use common\modules\user\models\UserAddress;
use common\modules\user\models\search\UserAddressSearch;

class AddressController extends Controller
{
	/**
	 * @inheritdoc
	 */
	public function behaviors() {
		return ArrayHelper::merge(parent::behaviors(), [
			'verbs' => [
				'class' => VerbFilter::class,
				'actions' => [
					'delete' => ['post', 'primary'],
				],
			],
			'access' => [
				'class' => AccessControl::class,
				'rules' => [
					[
						'allow' => true,
						'actions' => ['index', 'create', 'update', 'delete', 'primary'],
						'roles' => ['@'],
					],
				],
			],
		]);
	}
	
	/**
	 * Lists all UserAddress models.
	 * @return mixed
	 */
	public function actionIndex() {
		
		/** @var UserAddressSearch $searchModel */
		$searchModel = new UserAddressSearch();
		
		/** @var ActiveDataProvider $dataProvider */
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		$dataProvider->query->andWhere(['user_id' => Yii::$app->user->id]);
		
		return $this->render('index', [
			'dataProvider' => $dataProvider,
		]);
	}
	
	/**
	 * Creates a new UserAddress model.
	 *
	 * @return string|\yii\web\Response
	 * @throws \yii\base\ExitException
	 */
	public function actionCreate() {
		
		/** @var User $user */
		$user = Yii::$app->user->identity;
		
		$model = new UserAddress();
		$model->user_id = Yii::$app->user->id;
		$model->is_primary = !$user->getAddresses()->count();
		
		// Enable AJAX validation
		$this->performAjaxValidation($model);
		
		// Validate and save
		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			Yii::$app->getSession()->setFlash('success', Yii::t('user-address', 'message_create_success'));
			
			// Refresh
			return $this->redirect('index');
		}
	
		return $this->render('create', [
			'model' => $model,
		]);
	}
	
	/**
	 * Updates an existing UserAddress model.
	 *
	 * @param integer $id
	 *
	 * @return string|\yii\web\Response
	 * @throws NotFoundHttpException
	 */
	public function actionUpdate($id) {
		
		// Find model
		$model = $this->findModel($id);
		
		// Validate and save
		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			
			// Set flash message
			Yii::$app->session->setFlash('success', Yii::t('user-address', 'message_update_success'));
			
			// Redirect to index
			return $this->redirect(['index']);
		}
		
		return $this->render('update', [
			'model' => $model,
		]);
	}
	
	/**
	 * Set primary UserAddress model.
	 *
	 * @param integer $id
	 *
	 * @return \yii\web\Response
	 */
	public function actionPrimary($id) {
		
		// Find model and primary
		$this->findModel($id, true)->primary();
		
		// Set message
		Yii::$app->getSession()->setFlash('success', Yii::t('user-address', 'message_primary_success'));
		
		// Redirect to index
		return $this->redirect(['index']);
	
	}
	
	/**
	 * Deletes an existing UserAddress model.
	 *
	 * @param integer $id
	 *
	 * @return \yii\web\Response
	 * @throws \Throwable
	 * @throws \yii\db\StaleObjectException
	 */
	public function actionDelete($id) {
		
		// Find model and delete
		$this->findModel($id, true)->delete();
		
		// Set message
		Yii::$app->getSession()->setFlash('success', Yii::t('user-address', 'message_delete_success'));
		
		// Redirect to index
		return $this->redirect(['index']);
	}
	
	/**
	 * Finds the UserAddress model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 *
	 * @param integer $id
	 * @param bool $own
	 *
	 * @return array|null|\yii\db\ActiveRecord
	 */
	protected function findModel($id, $own = false) {
		return UserAddress::findBy($id, true, 'user-address', [], false, $own);
	}
}