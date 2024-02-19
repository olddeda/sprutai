<?php
namespace common\modules\project\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use yii\helpers\ArrayHelper;

use common\modules\base\extensions\editable\EditableAction;

use common\modules\base\components\Controller;
use common\modules\base\components\Debug;

use common\modules\rbac\helpers\enum\Role;

use common\modules\content\helpers\enum\Status;

use common\modules\payment\models\Payment;

use common\modules\project\models\Project;
use common\modules\project\models\search\ProjectSearch;

/**
 * DefaultController implements the CRUD actions for Project model.
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
				'modelClass' => Project::class,
			],
		]);
	}
	
	/**
	 * Lists all Project models.
	 * @return mixed
	 */
	public function actionIndex() {
		$searchModel = new ProjectSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		
		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}
	
	/**
	 * Displays a single Project model.
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
	 * Creates a new Project model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 * @return mixed
	 */
	public function actionCreate() {
		
		// Create model
		$model = Project::find()->where([
			'type' => Project::type(),
			'status' => Status::TEMP,
			'created_by' => Yii::$app->user->id,
		])->one();
		if (!$model) {
			$model = new Project();
			$model->type = Project::type();
			$model->status = Status::TEMP;
			$model->created_by = Yii::$app->user->id;
			$model->save(false);
		}
		
		$model->status = Status::DRAFT;
		
		// Validate and save
		if ($model->load(Yii::$app->request->post())) {
			
			if ($model->save()) {
				
				// Set message
				Yii::$app->getSession()->setFlash('success', Yii::t('project', 'message_create_success'));
				
				// Redirect to view
				if (isset($_POST['types-apply']))
					return $this->redirect(['update', 'id' => $model->id]);
				
				return $this->redirect(['index']);
			}
			else {
				
				// Render view
				return $this->render('create', [
					'model' => $model,
				]);
			}
		}
		else {
			
			// Render view
			return $this->render('create', [
				'model' => $model,
			]);
		}
	}
	
	/**
	 * Updates an existing Project model.
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
		if ($model->load(Yii::$app->request->post())) {
			
			if (isset($_POST['draft']))
				$model->status = Status::DRAFT;
			
			if ($model->save()) {
				
				// Set message
				Yii::$app->getSession()->setFlash('success', Yii::t('project', 'message_update_success'));
				
				// Redirect to view
				$action = 'view';
				if (isset($_POST['types-apply']) && $_POST['types-apply'])
					$action = 'update';
				
				return $this->redirect([$action, 'id' => $id]);
			}
			else {
				
				// Render view
				return $this->render('update', [
					'model' => $model,
				]);
			}
		}
		else {
			
			// Render view
			return $this->render('update', [
				'model' => $model,
			]);
		}
	}
	
	/**
	 * Deletes an existing Project model.
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
		Yii::$app->getSession()->setFlash('success', Yii::t('project', 'message_delete_success'));
		
		// Redirect to index
		return $this->redirect(['index']);
	}
	
	/**
	 * Finds the Project model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 *
	 * @param integer $id
	 * @param bool|false $own
	 *
	 * @return Project the loaded model
	 */
	protected function findModel($id, $own = false) {
		return Project::findBy($id, true, 'project', ['tags', 'paymentTypes'], false, $own);
	}
}