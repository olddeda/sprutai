<?php
namespace common\modules\company\controllers;

use Yii;
use yii\data\ActiveDataProvider;

use common\modules\base\components\Controller;
use common\modules\base\components\Debug;
use common\modules\base\extensions\editable\EditableAction;

use common\modules\company\models\Company;
use common\modules\company\models\CompanyDiscount;
use common\modules\company\models\search\CompanyDiscountSearch;
use yii\helpers\ArrayHelper;

class DiscountController extends Controller
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
	public function actions() {
		return ArrayHelper::merge(parent::actions(), [
			'editable' => [
				'class' => EditableAction::class,
				'modelClass' => CompanyDiscount::class,
			],
		]);
	}
	
	/**
	 * @inheritdoc
	 */
	public function beforeAction($action) {
		$this->companyId = Yii::$app->request->get('company_id', 0);
		$this->companyModel = Company::findOwn($this->companyId, true, 'company');
		
		return parent::beforeAction($action);
	}
	
	/**
	 * Lists all CompanyDiscount models.
	 * @return mixed
	 */
	public function actionIndex() {
		
		/** @var CompanyDiscountSearch $searchModel */
		$searchModel = new CompanyDiscountSearch();
		
		/** @var ActiveDataProvider $dataProvider */
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		$dataProvider->query->andWhere(['company_id' => $this->companyId]);
		
		// Render view
		return $this->render('index', [
			'dataProvider' => $dataProvider,
			'searchModel' => $searchModel,
			'company' => $this->companyModel,
		]);
	}
	
	/**
	 * Creates a new CompanyDiscount model.
	 *
	 * @return string|\yii\web\Response
	 * @throws \yii\base\ExitException
	 */
	public function actionCreate() {
		
		$model = new CompanyDiscount();
		$model->company_id = $this->companyId;
		$model->date_start_at = time();
		$model->date_end_at = strtotime("+1 month", time());
		$model->infinitely = true;
		
		// Enable AJAX validation
		$this->performAjaxValidation($model);
		
		// Validate and save
		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			Yii::$app->getSession()->setFlash('success', Yii::t('company-discount', 'message_create_success'));
			
			// Refresh
			return $this->redirect(['index', 'company_id' => $this->companyId]);
		}
		
		// Render view
		return $this->render('create', [
			'model' => $model,
			'company' => $this->companyModel,
		]);
	}
	
	/**
	 * Updates an existing CompanyDiscount model.
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
			Yii::$app->session->setFlash('success', Yii::t('company-discount', 'message_update_success'));
			
			// Redirect to index
			return $this->redirect(['index', 'company_id' => $this->companyId]);
		}
		
		// Render view
		return $this->render('update', [
			'model' => $model,
			'company' => $this->companyModel,
		]);
	}
	
	/**
	 * Deletes an existing CompanyDiscount model.
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
		Yii::$app->getSession()->setFlash('success', Yii::t('company-discount', 'message_delete_success'));
		
		// Redirect to index
		return $this->redirect(['index']);
	}
	
	/**
	 * Finds the CompanyDiscount model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 *
	 * @param integer $id
	 * @param bool $own
	 *
	 * @return array|null|\yii\db\ActiveRecord
	 */
	protected function findModel($id, $own = false) {
		$conditions = ['company_id' => $this->companyId];
		return CompanyDiscount::findBy($id, true, 'company-discount', [], false, $own, $conditions, ['user_id', 'created_by']);
	}
}