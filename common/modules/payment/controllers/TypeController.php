<?php
namespace common\modules\payment\controllers;

use Yii;
use yii\helpers\ArrayHelper;

use common\modules\base\helpers\enum\Status;
use common\modules\base\extensions\editable\EditableAction;
use common\modules\base\components\Debug;

use common\modules\base\components\Controller;

use common\modules\payment\models\PaymentType;

use common\modules\payment\models\search\PaymentTypeSearch;

/**
 * TypeController implements the CRUD actions for PaymentType model.
 */
class TypeController extends Controller
{
	/**
	 * @inheritdoc
	 */
	public function actions() {
		return ArrayHelper::merge(parent::actions(), [
			'editable' => [
				'class' => EditableAction::class,
				'modelClass' => PaymentType::class,
			],
		]);
	}
	
	/**
	 * Lists all PaymentType models.
	 * @return mixed
	 */
	public function actionIndex() {
		$searchModel = new PaymentTypeSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		
		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}
	
	/**
	 * Displays a single PaymentType model.
	 * @param integer $id
	 *
	 * @return mixed
	 */
	public function actionView($id) {
		
		// Render view
		return $this->render('view', [
			'model' => $this->findModel($id),
		]);
	}
	
	/**
	 * Creates a new PaymentType model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 * @return mixed
	 */
	public function actionCreate() {
		
		// Create model
		$model = new PaymentType();
		$model->status = Status::ENABLED;
		
		// Validate and save
		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			
			// Set message
			Yii::$app->getSession()->setFlash('success', Yii::t('payment-type', 'message_create_success'));
				
			// Redirect to view
			return $this->redirect(['index']);
		}
		else {
			
			if ($model->errors) {
				Debug::dump($model->errors);
				die;
			}
			
			// Render view
			return $this->render('create', [
				'model' => $model,
			]);
		}
	}
	
	/**
	 * Updates an existing PaymentType model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 *
	 * @param integer $id
	 *
	 * @return string|\yii\web\Response
	 */
	public function actionUpdate($id) {
		
		// Load model
		$model = $this->findModel($id, true);
		
		// Validate and save
		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			
			// Set message
			Yii::$app->getSession()->setFlash('success', Yii::t('payment-type', 'message_update_success'));
			
			// Redirect to view
			return $this->redirect(['index']);
		}
		else {
			
			// Render view
			return $this->render('update', [
				'model' => $model,
			]);
		}
	}
	
	/**
	 * Deletes an existing PaymentType model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 *
	 * @param integer $id
	 *
	 * @return mixed
	 */
	public function actionDelete($id) {
		
		// Find model and delete
		$this->findModel($id, true)->delete();
		
		// Set message
		Yii::$app->getSession()->setFlash('success', Yii::t('payment-type', 'message_delete_success'));
		
		// Redirect to index
		return $this->redirect(['index']);
	}
	
	/**
	 * Finds the PaymentType model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 *
	 * @param integer $id
	 * @param bool|false $own
	 *
	 * @return PaymentType the loaded model
	 */
	protected function findModel($id, $own = false) {
		return PaymentType::findBy($id, true, 'payment-type', [], false, $own);
	}
}