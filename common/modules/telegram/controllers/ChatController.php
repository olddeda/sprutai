<?php
namespace common\modules\telegram\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use yii\helpers\ArrayHelper;

use common\modules\base\extensions\editable\EditableAction;
use common\modules\base\helpers\enum\Status;
use common\modules\base\components\Debug;

use common\modules\base\components\Controller;

use common\modules\telegram\models\TelegramChat;
use common\modules\telegram\models\search\TelegramChatSearch;

/**
 * ChatController implements the CRUD actions for TelegramChat model.
 */
class ChatController extends Controller
{
	/**
	 * @inheritdoc
	 */
	public function actions() {
		return ArrayHelper::merge(parent::actions(), [
			'editable' => [
				'class' => EditableAction::class,
				'modelClass' => TelegramChat::class,
			],
		]);
	}
	
	/**
	 * Lists all TelegramChat models.
	 * @return mixed
	 */
	public function actionIndex() {
		$searchModel = new TelegramChatSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		
		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}
	
	/**
	 * Creates a new TelegramChat model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 * @return mixed
	 */
	public function actionCreate() {
		
		// Create model
		$model = TelegramChat::find()->where([
			'status' => Status::TEMP,
			'created_by' => Yii::$app->user->id,
		])->one();
		if (!$model) {
			$model = new TelegramChat();
			$model->status = Status::TEMP;
			$model->created_by = Yii::$app->user->id;
			$model->save(false);
		}
		$model->status = Status::ENABLED;

		// Validate and save
		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			
			// Set message
            Yii::$app->getSession()->setFlash('success', Yii::t('telegram-chat', 'message_create_success'));
				
			// Redirect to view
			return $this->redirect(['index']);
		}
		
		// Render view
		return $this->render('create', [
			'model' => $model,
		]);
	}
	
	/**
	 * Updates an existing TelegramChat model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 *
	 * @param integer $id
	 *
	 * @return string|\yii\web\Response
	 */
	public function actionUpdate($id) {
		
		// Load model
		$model = $this->findModel($id, true);
		
		//Debug::dump($model->tag);die;
		
		// Validate and save
		if ($model->load(Yii::$app->request->post()) && $model->save()) {
		 
			// Set message
            Yii::$app->getSession()->setFlash('success', Yii::t('telegram-chat', 'message_update_success'));
            
            // Redirect to view
            return $this->redirect(['index']);
		}
		
		// Render view
		return $this->render('update', [
			'model' => $model,
		]);
	}
	
	/**
	 * Deletes an existing TelegramChat model.
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
		Yii::$app->getSession()->setFlash('success', Yii::t('telegram-chat', 'message_delete_success'));
		
		// Redirect to index
		return $this->redirect(['index']);
	}
	
	/**
	 * Finds the TelegramChat model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 *
	 * @param integer $id
	 * @param bool|false $own
	 *
	 * @return TelegramChat the loaded model
	 */
	protected function findModel($id, $own = false) {
		return TelegramChat::findBy($id, true, 'telegram-chat', ['tags'], false, $own);
	}
}