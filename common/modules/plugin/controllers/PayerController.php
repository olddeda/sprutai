<?php
namespace common\modules\plugin\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use yii\helpers\ArrayHelper;

use common\modules\base\extensions\editable\EditableAction;

use common\modules\base\components\Controller;
use common\modules\base\components\Debug;
use common\modules\base\helpers\enum\Status;

use common\modules\rbac\helpers\enum\Role;

use common\modules\payment\models\Payment;
use common\modules\payment\models\search\PaymentSearch;

use common\modules\plugin\models\Plugin;
use common\modules\plugin\models\search\PluginSearch;

/**
 * PayerController implements the CRUD actions for Plugin model.
 */
class PayerController extends Controller
{
    /**
     * @var integer
     */
    public $pluginId;

    /**
     * @var Plugin
     */
    public $pluginModel;

    /**
     * @inheritdoc
     */
    public function beforeAction($action) {
        $this->pluginId = Yii::$app->request->get('plugin_id', 0);
        $this->pluginModel = Plugin::findById($this->pluginId, true, 'plugin');

        return parent::beforeAction($action);
    }

	/**
	 * @inheritdoc
	 */
	public function actions() {
		return ArrayHelper::merge(parent::actions(), [
			'editable' => [
				'class' => EditableAction::class,
				'modelClass' => Payment::class,
			],
		]);
	}
	
	/**
	 * Lists all plugin Payers models.
	 * @return mixed
	 */
	public function actionIndex() {
		$searchModel = new PaymentSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		$dataProvider->query->andWhere([
		    Payment::tableName().'.module_type' => $this->pluginModel->getModuleType(),
            Payment::tableName().'.module_id' => $this->pluginModel->id,
        ]);
		
		return $this->render('index', [
		    'plugin' => $this->pluginModel,
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}
	
	/**
	 * Displays a single plugin Payer model.
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
	 * Creates a new plugin Payer model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 * @return mixed
	 */
	public function actionCreate() {
		
		// Create model
        $model = new Payment();
        $model->module_type = $this->pluginModel->getModuleType();
        $model->module_id = $this->pluginId;

		// Validate and save
		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			
			// Set message
            Yii::$app->getSession()->setFlash('success', Yii::t('plugin-payer', 'message_create_success'));
				
			// Redirect to view
			return $this->redirect(['index', 'plugin_id' => $this->pluginId]);
		}
		else {

			// Render view
			return $this->render('create', [
				'model' => $model,
                'plugin' => $this->pluginModel,
			]);
		}
	}
	
	/**
	 * Updates an existing Plugin model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 *
	 * @param integer $id
	 *
	 * @return string|\yii\web\Response
	 * @throws NotFoundHttpException
	 */
	public function actionUpdate($id) {
		
		// Load model
		$model = $this->findModel($id, true);
		
		// Validate and save
		if ($model->load(Yii::$app->request->post()) && $model->save()) {
		 
			// Set message
            Yii::$app->getSession()->setFlash('success', Yii::t('plugin-payer', 'message_update_success'));
            
            // Redirect to view
            return $this->redirect(['index', 'plugin_id' => $this->pluginId]);
		}
		else {
			
			// Render view
			return $this->render('update', [
				'model' => $model,
                'plugin' => $this->pluginModel,
			]);
		}
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
		Yii::$app->getSession()->setFlash('success', Yii::t('content-plugin', 'message_delete_success'));
		
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
		return Payment::findBy($id, true, 'payment', [], false, $own);
	}
}