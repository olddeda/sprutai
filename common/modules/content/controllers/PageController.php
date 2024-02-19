<?php
namespace common\modules\content\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use yii\helpers\ArrayHelper;

use common\modules\base\extensions\editable\EditableAction;

use common\modules\base\components\Controller;
use common\modules\base\components\Debug;

use common\modules\base\helpers\enum\Status;

use common\modules\content\models\Page;
use common\modules\content\models\search\PageSearch;

/**
 * PageController implements the CRUD actions for Page model.
 */
class PageController extends Controller
{
	/**
	 * @var integer
	 */
	public $parentId;
	
	/**
	 * @var Page
	 */
	public $parentModel;
	
	/**
	 * @inheritdoc
	 */
	public function actions() {
		return ArrayHelper::merge(parent::actions(), [
			'editable' => [
				'class' => EditableAction::class,
				'modelClass' => Page::class,
				'forceCreate' => false
			],
		]);
	}
	
	/**
	 * @inheritdoc
	 */
	public function beforeAction($action) {
		$this->parentId = Yii::$app->request->get('parent_id', 0);
		if ($this->parentId)
			$this->parentModel = Lookup::loadModel($this->parentId);
		
		return parent::beforeAction($action);
	}
	
	/**
	 * Lists all Content models.
	 * @return mixed
	 */
	public function actionIndex() {
		$searchModel = new PageSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		return $this->render('index', [
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
			'model' => $this->findModel($id, true),
		]);
	}

	/**
	 * Creates a new Content model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 * @return mixed
	 */
	public function actionCreate() {
		
		// Create model
		$model = Page::find()->where([
			'type' => Page::type(),
			'status' => Status::TEMP,
			'created_by' => Yii::$app->user->id,
		])->one();
		if (!$model) {
			$model = new Page();
			$model->type = Page::type();
			$model->status = Status::TEMP;
			$model->created_by = Yii::$app->user->id;
			$model->save(false);
		}
		$model->status = Status::ENABLED;
		
		//Debug::dump(Yii::$app->request->post());
		//die;

		// Validate and save
		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			
			// Set message
			Yii::$app->getSession()->setFlash('success', Yii::t('content-page', 'message_create_success'));

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
	 * Updates an existing Content model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 *
	 * @param integer $id
	 *
	 * @return mixed
	 */
	public function actionUpdate($id) {

		// Load model
		$model = $this->findModel($id, true);

		// Validate and save
		if ($model->load(Yii::$app->request->post()) && $model->save()) {

			// Set message
			Yii::$app->getSession()->setFlash('success', Yii::t('content-page', 'message_update_success'));

			// Redirect to view
			return $this->redirect(['view', 'id' => $model->id]);
		}
		else {

			// Render view
			return $this->render('update', [
				'model' => $model,
			]);
		}
	}

	/**
	 * Deletes an existing Content model.
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
		Yii::$app->getSession()->setFlash('success', Yii::t('content-page', 'message_delete_success'));

		// Redirect to index
		return $this->redirect(['index']);
	}

	/**
	 * Finds the Content model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 *
	 * @param integer $id
	 * @param bool|false $own
	 *
	 * @return Content the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel($id, $own = false) {
		return Page::findBy($id, true, 'content-page', [], false, $own);
	}
}
