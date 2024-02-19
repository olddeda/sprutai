<?php
namespace common\modules\project\controllers;

use Yii;
use yii\helpers\ArrayHelper;

use common\modules\base\extensions\editable\EditableAction;
use common\modules\base\components\Controller;
use common\modules\base\components\Debug;

use common\modules\content\models\Event;
use common\modules\content\models\search\EventSearch;
use common\modules\content\helpers\enum\Status;

use common\modules\project\models\Project;

/**
 * EventController implements the CRUD actions for Project model.
 */
class EventController extends Controller
{
    /**
     * @var integer
     */
    public $projectId;

    /**
     * @var Project
     */
    public $projectModel;

    /**
     * @inheritdoc
     */
    public function beforeAction($action) {
        $this->projectId = Yii::$app->request->get('project_id', 0);
        $this->projectModel = Project::findById($this->projectId, true, 'project');

        return parent::beforeAction($action);
    }

	/**
	 * @inheritdoc
	 */
	public function actions() {
		return ArrayHelper::merge(parent::actions(), [
			'editable' => [
				'class' => EditableAction::class,
				'modelClass' => Event::class,
			],
		]);
	}
	
	/**
	 * Lists all project Events models.
	 * @return mixed
	 */
	public function actionIndex() {
		$searchModel = new EventSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		$dataProvider->query->andWhere([
		    Event::tableName().'.module_type' => $this->projectModel->getModuleType(),
            Event::tableName().'.module_id' => $this->projectModel->id,
        ]);
		
		return $this->render('index', [
		    'project' => $this->projectModel,
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}
	
	/**
	 * Displays a single Content model.
	 * @param integer $id
	 *
	 * @return string
	 * @throws NotFoundHttpException
	 */
	public function actionView($id) {
		
		// Render view
		return $this->render('view', [
			'project' => $this->projectModel,
			'model' => $this->findModel($id),
		]);
	}
	
	/**
	 * Creates a new project Event model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 * @return mixed
	 */
	public function actionCreate() {
		
		// Create model
		$model = Event::find()->where([
			'module_type' => $this->projectModel->getModuleType(),
			'module_id' => $this->projectId,
			'author_id' => Yii::$app->user->id,
			'status' => Status::TEMP,
		])->one();
		if (!$model) {
			$model = new Event();
			$model->module_type = $this->projectModel->getModuleType();
			$model->module_id = $this->projectId;
			$model->author_id = Yii::$app->user->id;
			$model->status = Status::TEMP;
			$model->save(false);
		}
		$model->status = Status::DRAFT;

		// Validate and save
		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			
			// Set message
            Yii::$app->getSession()->setFlash('success', Yii::t('project-event', 'message_create_success'));
				
			// Redirect to view
			return $this->redirect(['index', 'project_id' => $this->projectId]);
		}
		
		// Render view
		return $this->render('create', [
			'model' => $model,
			'project' => $this->projectModel,
		]);
	}
	
	/**
	 * Updates an existing Event model.
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
            Yii::$app->getSession()->setFlash('success', Yii::t('project-event', 'message_update_success'));
            
            // Redirect to view
            return $this->redirect(['index', 'project_id' => $this->projectId]);
		}
		
		// Render view
		return $this->render('update', [
			'model' => $model,
			'project' => $this->projectModel,
		]);
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
		Yii::$app->getSession()->setFlash('success', Yii::t('project-event', 'message_delete_success'));
		
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
	 * @return Event the loaded model
	 */
	protected function findModel($id, $own = false) {
		return Event::findBy($id, true, 'project-event', [], false, $own);
	}
}