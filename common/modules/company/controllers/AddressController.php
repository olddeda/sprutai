<?php
namespace common\modules\company\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

use common\modules\base\components\Controller;

use common\modules\company\models\Company;
use common\modules\company\models\CompanyAddress;
use common\modules\company\models\search\CompanyAddressSearch;

class AddressController extends Controller
{
	/**
	 * @var integer
	 */
	public $companyId;
	
	/**
	 * @var \common\modules\company\models\Company
	 */
	public $companyModel;
	
	/**
	 * @inheritdoc
	 */
	public function beforeAction($action) {
		$this->companyId = Yii::$app->request->get('company_id', 0);
		$this->companyModel = Company::findOwn($this->companyId, true, 'company');
		
		return parent::beforeAction($action);
	}
	
	/**
	 * Lists all CompanyAddress models.
	 * @return mixed
	 */
	public function actionIndex() {
		
		/** @var CompanyAddressSearch $searchModel */
		$searchModel = new CompanyAddressSearch();
		
		/** @var ActiveDataProvider $dataProvider */
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		$dataProvider->query->andWhere(['company_id' => $this->companyId]);
		
		return $this->render('index', [
			'dataProvider' => $dataProvider,
			'company' => $this->companyModel,
		]);
	}
	
	/**
	 * Creates a new CompanyAddress model.
	 *
	 * @return string|\yii\web\Response
	 * @throws \yii\base\ExitException
	 */
	public function actionCreate() {
		
		$model = new CompanyAddress();
		$model->company_id = $this->companyId;
		$model->is_primary = !$this->companyModel->getAddresses()->count();
		
		// Enable AJAX validation
		$this->performAjaxValidation($model);
		
		// Validate and save
		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			Yii::$app->getSession()->setFlash('success', Yii::t('company-address', 'message_create_success'));
			
			// Refresh
			return $this->redirect(['index', 'company_id' => $this->companyId]);
		}
	
		return $this->render('create', [
			'model' => $model,
			'company' => $this->companyModel,
		]);
	}
	
	/**
	 * Updates an existing CompanyAddress model.
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
			Yii::$app->session->setFlash('success', Yii::t('company-address', 'message_update_success'));
			
			// Redirect to index
			return $this->redirect(['index', 'company_id' => $this->companyId]);
		}
		
		return $this->render('update', [
			'model' => $model,
			'company' => $this->companyModel,
		]);
	}
	
	/**
	 * Set primary CompanyAddress model.
	 *
	 * @param integer $id
	 *
	 * @return \yii\web\Response
	 */
	public function actionPrimary($id) {
		
		// Find model and primary
		$this->findModel($id, true)->primary();
		
		// Set message
		Yii::$app->getSession()->setFlash('success', Yii::t('company-address', 'message_primary_success'));
		
		// Redirect to index
		return $this->redirect(['index', 'company_id' => $this->companyId]);
	
	}
	
	/**
	 * Deletes an existing CompanyAddress model.
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
		Yii::$app->getSession()->setFlash('success', Yii::t('company-address', 'message_delete_success'));
		
		// Redirect to index
		return $this->redirect(['index']);
	}
	
	/**
	 * Finds the CompanyAddress model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 *
	 * @param integer $id
	 * @param bool $own
	 *
	 * @return array|null|\yii\db\ActiveRecord
	 */
	protected function findModel($id, $own = false) {
		$conditions = ['company_id' => $this->companyId];
		return CompanyAddress::findBy($id, true, 'company-address', [], false, $own, $conditions, ['user_id', 'created_by']);
	}
}