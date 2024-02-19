<?php
namespace common\modules\plugin\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use yii\helpers\ArrayHelper;

use common\modules\base\extensions\editable\EditableAction;

use common\modules\base\components\Controller;
use common\modules\base\components\Debug;

use common\modules\rbac\helpers\enum\Role;

use common\modules\content\helpers\enum\Status;

use common\modules\payment\models\Payment;
use common\modules\payment\models\PaymentType;

use common\modules\plugin\models\Plugin;
use common\modules\plugin\models\search\PluginSearch;

/**
 * DefaultController implements the CRUD actions for Plugin model.
 */
class DefaultController extends Controller
{
	/**
	 * @inheritdoc
	 */
	public function actions() {
		return ArrayHelper::merge(parent::actions(), [
			'editable' => [
				'class' => EditableAction::class,
				'modelClass' => Plugin::class,
			],
		]);
	}
	
	/**
	 * Lists all Plugin models.
	 * @return mixed
	 */
	public function actionIndex() {
		$searchModel = new PluginSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		
		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}
	
	/**
	 * Displays a single Plugin model.
	 * @param integer $id
	 *
	 * @return mixed
     */
	public function actionView($id) {
		
		// Find model
		$model = $this->findModel($id);
		
		// Render view
		return $this->render('view', [
			'model' => $model,
		]);
	}
	
	/**
	 * Creates a new Plugin model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 *
	 * @return string|\yii\web\Response
	 * @throws NotFoundHttpException
	 * @throws \Throwable
	 */
	public function actionCreate() {
		
		// Create model
		$model = Plugin::find()->where([
			'type' => Plugin::type(),
			'status' => Status::TEMP,
			'created_by' => Yii::$app->user->id,
		])->one();
		if (!$model) {
			$model = new Plugin();
			$model->type = Plugin::type();
			$model->status = Status::TEMP;
			$model->created_by = Yii::$app->user->id;
			$model->save(false);
		}
		
		if (!$model->paymentTypes) {
			$typeModel = PaymentType::findByIdentifier('plugin', true);
			
			$params = [
				'module_type' => Plugin::moduleType(),
				'price' => $typeModel->price,
			];
			
			$model->link('paymentTypes', $typeModel, $params);
		}
		
		$model->status = Status::DRAFT;
		if (!Yii::$app->user->hasRole([Role::SUPERADMIN, Role::ADMIN, Role::EDITOR]))
			$model->status = Status::MODERATED;
		
		// Validate and save
		if ($model->load(Yii::$app->request->post())) {
			if (isset($_POST['draft']))
				$model->status = Status::DRAFT;
			
			if ($model->save()) {
				
				// Set message
				Yii::$app->getSession()->setFlash('success', Yii::t('plugin', 'message_create_success'));
				
				// Redirect to view
				return $this->redirect(['/plugin/version/create', 'plugin_id' => $model->id]);
			}
		}
		
		// Render view
		return $this->render('create', [
			'model' => $model,
		]);
	}
	
	/**
	 * Updates an existing Plugin model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 *
	 * @param integer $id
	 *
	 * @return string|\yii\web\Response
	 * @throws NotFoundHttpException
	 * @throws \Throwable
	 */
	public function actionUpdate($id) {
		
		// Load model
		$model = $this->findModel($id, true);
		
		$isUser = !Yii::$app->user->hasRole([Role::SUPERADMIN, Role::ADMIN, Role::EDITOR]);
		if ($isUser && in_array($model->status, [Status::MODERATED, Status::ENABLED])) {
			throw new NotFoundHttpException(Yii::t('plugin', 'error_moderated'));
		}
		
		if (!$model->paymentTypes) {
			$typeModel = PaymentType::findByIdentifier('plugin', true);
			
			$params = [
				'module_type' => Plugin::moduleType(),
				'price' => $typeModel->price,
			];
			
			$model->link('paymentTypes', $typeModel, $params);
		}
		
		// Validate and save
		if ($model->load(Yii::$app->request->post())) {
			if (isset($_POST['draft']))
				$model->status = Status::DRAFT;
			
			if ($isUser) {
				if (!in_array($model->status, [Status::DRAFT, Status::MODERATED])) {
					$model->status = Status::MODERATED;
				}
			}
			
			if ($model->save()) {
				
				// Set message
				Yii::$app->getSession()->setFlash('success', Yii::t('plugin', 'message_update_success'));
				
				// Redirect to view
				return $this->redirect(['view', 'id' => $id]);
			}
		}
		
		// Render view
		return $this->render('update', [
			'model' => $model,
		]);
	}
	
	/**
	 * Deletes an existing Plugin model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 *
	 * @param integer $id
     *
     * @return \yii\web\Response
     */
	public function actionDelete($id) {
		
		// Find model and delete
		$this->findModel($id, true)->delete();
		
		// Set message
		Yii::$app->getSession()->setFlash('success', Yii::t('plugin', 'message_delete_success'));
		
		// Redirect to index
		return $this->redirect(['index']);
	}
	
	/**
	 * Finds the Plugin model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 *
	 * @param integer $id
	 * @param bool|false $own
	 *
	 * @return Plugin the loaded model
	 */
	protected function findModel($id, $own = false) {
		return Plugin::findBy($id, true, 'plugin', ['tags', 'paymentTypes'], false, $own);
	}
}