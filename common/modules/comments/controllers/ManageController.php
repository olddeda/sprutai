<?php
namespace common\modules\comments\controllers;

use common\modules\comments\helpers\enum\Status;
use common\modules\content\models\Article;
use Yii;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\helpers\ArrayHelper;

use common\modules\base\components\Controller;
use common\modules\base\extensions\editable\EditableAction;

use common\modules\comments\Module;
use common\modules\comments\models\Comment;
use common\modules\comments\models\search\CommentSearch;

/**
 * Manage comments in admin panel
 *
 * Class ManageController
 * @package common\modules\comments\controllers
 */
class ManageController extends Controller
{
	/**
	 * @var string path to index view file, which is used in admin panel
	 */
	public $indexView = '@common/modules/comments/views/manage/index';

	/**
	 * @var string path to update view file, which is used in admin panel
	 */
	public $updateView = '@common/modules/comments/views/manage/update';

	/**
	 * Behaviors
	 * @return array
	 */
	public function behaviors() {
		return ArrayHelper::merge(parent::behaviors(), [
			'verbs' => [
				'class' => VerbFilter::className(),
				'actions' => [
					'index' => ['get'],
					'delete' => ['post'],
				],
			],
		]);
	}

	/**
	 * Declares external actions for the controller.
	 * This method is meant to be overwritten to declare external actions for the controller.
	 * @return array
	 */
	public function actions() {
		return [
			'edit-comment' => [
				'class' => EditableAction::className(),
				'modelClass' => Comment::className(),
				'forceCreate' => false
			]
		];
	}

	/**
	 * Lists all comments.
	 * @return mixed
	 */
	public function actionIndex() {
		$searchModel = new CommentSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		$commentModel = Yii::$app->getModule(Module::$name)->commentModelClass;

		return $this->render($this->indexView, [
			'dataProvider' => $dataProvider,
			'searchModel' => $searchModel,
			'commentModel' => $commentModel
		]);
	}

	/**
	 * Statistics comments
	 * @return string
	 */
	public function actionStatistics() {
		return $this->render('statistics', []);
	}

	/**
	 * Updates an existing Comment model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 *
	 * @param integer $id
	 *
	 * @return string|\yii\web\Response
	 * @throws NotFoundHttpException
	 */
	public function actionUpdate($id) {

		// Find model
		$model = $this->findModel($id);

		// Validate and save
		if ($model->load(Yii::$app->request->post()) && $model->save()) {

			// Set flash message
			Yii::$app->session->setFlash('success', Yii::t('comments', 'message_update_success'));

			// Redirect to index
			return $this->redirect(['index']);
		}

		return $this->render($this->updateView, [
			'model' => $model,
		]);

	}

	/**
	 * Deletes an existing Comment model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 *
	 * @param integer $id
	 *
	 * @return \yii\web\Response
	 * @throws NotFoundHttpException
	 */
	public function actionDelete($id) {

		// Find model and delete
		$this->findModel($id)->delete();

		// Set flash message
		Yii::$app->session->setFlash('success', Yii::t('comments', 'message_delete_success'));

		// Redirect to index
		return $this->redirect(['index']);
	}

	/**
	 * Finds the Comment model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 *
	 * @param integer $id
	 *
	 * @return Comment the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel($id) {
		if (($model = Comment::findOne($id)) !== null) {
			return $model;
		}
		else {
			throw new NotFoundHttpException(Yii::t('comments', 'The requested page does not exist.'));
		}
	}
}
