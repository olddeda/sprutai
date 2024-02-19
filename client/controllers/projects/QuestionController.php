<?php
namespace client\controllers\projects;

use client\components\Controller;

use Yii;
use yii\helpers\ArrayHelper;
use yii\filters\AccessControl;
use yii\data\ActiveDataProvider;

use common\modules\base\components\Debug;
use common\modules\base\helpers\enum\ModuleType;

use common\modules\content\models\Question;
use common\modules\content\helpers\enum\Status;

use common\modules\project\models\Project;

class QuestionController extends Controller
{
	/**
	 * @var integer
	 */
	public $projectId;
	
	/**
	 * @var \common\modules\content\models\Project
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
	public function behaviors() {
		return ArrayHelper::merge(parent::behaviors(), [
			'access' => [
				'class' => AccessControl::class,
				'rules' => [
					[
						'allow' => true,
						'actions' => ['index', 'view'],
						'roles' => ['?', '@'],
					],
					[
						'allow' => true,
						'actions' => ['create', 'update', 'delete'],
						'roles' => ['@'],
					],
				],
			],
		]);
	}
	
	/**
	 * List all questions project
	 * @return string
	 */
	public function actionIndex() {
		
		/** @var \common\modules\content\models\query\ContentQuery $query */
		$query = Question::find()->andWhere([
			Question::tableName().'.module_type' => ModuleType::CONTENT_PROJECT,
			Question::tableName().'.module_id' => $this->projectId,
			Question::tableName().'.status' => Status::ENABLED,
		])->votes();
		
		$query->orderBy([
			'date_at' => SORT_DESC,
		]);
		
		// Prepare provider
		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'pagination' => [
				'pageSize' => 10,
			],
		]);
		
		// Render view
		return $this->render('index', [
			'dataProvider' => $dataProvider,
			'project' => $this->projectModel,
		]);
	}
	
	/**
	 * Show single question model
	 * @param $id
	 *
	 * @return string
	 */
	public function actionView($id) {
		
		// Find model
		$model = $this->findModel($id);
		
		// Set visit
		$model->setStat();
		
		// Render view
		return $this->render('view', [
			'model' => $model,
			'project' => $this->projectModel,
		]);
	}
	
	/**
	 * Creates a new Question model.
	 *
	 * @return mixed
	 */
	public function actionCreate() {
		
		// Create model
		$model = Question::find()->where([
			'module_type' => ModuleType::CONTENT_PROJECT,
			'module_id' => $this->projectId,
			'type' => Question::type(),
			'status' => Status::TEMP,
			'created_by' => Yii::$app->user->id,
		])->one();
		if (!$model) {
			$model = new Question();
			$model->type = Question::type();
			$model->module_type = ModuleType::CONTENT_PROJECT;
			$model->module_id = $this->projectId;
			$model->status = Status::TEMP;
			$model->created_by = Yii::$app->user->id;
			$model->save(false);
		}
		$model->status = Status::ENABLED;
		
		// Validate and save
		if ($model->load(Yii::$app->request->post())) {
			if ($model->save()) {
				
				// Set message
				Yii::$app->getSession()->setFlash('success', Yii::t('project-question', 'message_create_success'));
				
				// Redirect to view
				return $this->redirect(['view', 'project_id' => $this->projectId, 'id' => $model->id]);
			}
		}
		
		// Render view
		return $this->render('create', [
			'model' => $model,
			'project' => $this->projectModel,
		]);
	}
	
	/**
	 * Updates an existing Question model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 *
	 * @param integer $id
	 *
	 * @return string|\yii\web\Response
	 * @throws \yii\base\ExitException
	 */
	public function actionUpdate($id) {
		
		// Load model
		$model = $this->findModel($id, true);
		
		// Validate and save
		if ($model->load(Yii::$app->request->post())) {
			if ($model->validate() && $model->save()) {
				
				// Set message
				Yii::$app->getSession()->setFlash('success', Yii::t('project-question', 'message_update_success'));
				
				// Redirect to view
				return $this->redirect(['view', 'project_id' => $this->projectId, 'id' => $model->id]);
			}
			
		}
		
		// Render view
		return $this->render('update', [
			'model' => $model,
			'project' => $this->projectModel,
		]);
	}
	
	/**
	 * Delete question model
	 * @param $id
	 *
	 * @return \yii\web\Response
	 * @throws \Throwable
	 * @throws \yii\db\StaleObjectException
	 */
	public function actionDelete($id) {
		
		// Find and delete model
		$this->findModel($id, true)->delete();
		
		// Redirect to index
		return $this->redirect(['index', 'project_id' => $this->projectId]);
	}
	
	/**
	 * Finds the Question model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 *
	 * @param integer $id
	 * @param bool $own
	 *
	 * @return array|null|\yii\db\ActiveRecord
	 */
	protected function findModel($id, $own = false) {
		return Question::findBy($id, true, 'project-question', [], false, $own);
	}
}