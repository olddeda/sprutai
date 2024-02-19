<?php
namespace common\modules\plugin\controllers;

use Yii;

use common\modules\base\components\Controller;

use common\modules\content\helpers\enum\Status;
use common\modules\content\models\Instruction;

use common\modules\plugin\models\Plugin;

/**
 * InstructionController implements the CRUD actions for Instruction model.
 */
class InstructionController extends Controller
{
	/**
	 * @var integer
	 */
	public $pluginId;
	
	/**
	 * @var Project
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
	 * Show instruction model
	 * @return mixed
	 */
	public function actionIndex() {
		$model = $this->pluginModel->instruction;
		if (!$model)
			return $this->redirect(['update', 'plugin_id' => $this->pluginId]);
		
		// Render view
		return $this->render('index', [
			'model' => $model,
		]);
	}
	
	/**
	 * Updates an existing Instruction model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 *
	 * @param integer $id
	 *
	 * @return string|\yii\web\Response
	 */
	public function actionUpdate() {
		
		// Load model
		$model = $this->pluginModel->instruction;
		if (!$model) {
			$model = new Instruction();
			$model->status = Status::TEMP;
			$model->content_id = $this->pluginId;
			$model->save(false);
		}
		
		// Validate and save
		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			
				
			// Set message
			Yii::$app->getSession()->setFlash('success', Yii::t('plugin-instruction', 'message_update_success'));
				
			return $this->redirect(['index', 'plugin_id' => $this->pluginId]);
		}
		
		// Render view
		return $this->render('update', [
			'model' => $model,
		]);
	}
}