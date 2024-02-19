<?php
namespace common\modules\menu\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use yii\helpers\ArrayHelper;
use yii\web\Response;

use common\modules\base\extensions\editable\EditableAction;

use common\modules\base\components\Controller;
use common\modules\base\components\Debug;

use common\modules\base\helpers\enum\Status;

use common\modules\menu\models\Menu;
use common\modules\menu\models\search\MenuSearch;

/**
 * DefaultController implements the CRUD actions for Menu model.
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
				'modelClass' => Menu::class,
			],
		]);
	}
	
	/**
	 * Lists all Menu models.
	 * @return mixed
	 */
	public function actionIndex() {
		$searchModel = new MenuSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		
		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}
	
	/**
	 * @param $id
	 *
	 * @return string
	 * @throws NotFoundHttpException
	 */
	public function actionView($id) {
		
		// Find model
		$model = $this->findModel($id, true);
		
		//Debug::dump($model->tree);die;
		
		// Render view
		return $this->render('view', [
			'model' => $model,
		]);
	}
	
	/**
	 * Creates a new Menu model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 *
	 * @return string|Response
	 * @throws \yii\base\InvalidConfigException
	 */
	public function actionCreate() {
		
		// Create model
		$model = Menu::find()->where([
			'status' => Status::TEMP,
			'created_by' => Yii::$app->user->id,
		])->one();
		
		if (!$model) {
			
			// Create model
			$model = new Menu();
			$model->status = Status::TEMP;
			$model->created_by = Yii::$app->user->id;
			$model->save(false);
		}
		
		$model->status = Status::ENABLED;
		
		// Validate and save
		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			
			// Set message
			Yii::$app->getSession()->setFlash('success', Yii::t('menu', 'message_create_success'));
			
			// Redirect to view
			return $this->redirect(['view', 'id' => $model->id]);
		}
		else {
			
			// Render view
			return $this->render('create', [
				'model' => $model,
			]);
		}
	}
	
	/**
	 * Updates an existing Menu model.
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
			Yii::$app->getSession()->setFlash('success', Yii::t('menu', 'message_update_success'));
			
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
	 * Deletes an existing Menu model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 *
	 * @param integer $id
	 *
	 * @return \yii\web\Response
	 * @throws NotFoundHttpException
	 */
	public function actionDelete($id) {
		
		// Find model and delete
		$this->findModel($id, true)->delete();
		
		// Set message
		Yii::$app->getSession()->setFlash('success', Yii::t('menu', 'message_delete_success'));
		
		// Redirect to index
		return $this->redirect(['index']);
	}
	
	/**
	 * Finds the Menu model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 *
	 * @param integer $id
	 * @param bool|false $own
	 *
	 * @return Menu the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel($id, $own = false) {
		return Menu::findBy($id, true, 'menu', [], false, $own);
	}
}