<?php
namespace common\modules\paste\controllers;

use Yii;
use yii\helpers\ArrayHelper;

use common\modules\base\extensions\editable\EditableAction;

use common\modules\base\components\Controller;
use common\modules\base\components\Debug;

use common\modules\base\helpers\enum\Status;

use common\modules\paste\models\Paste;
use common\modules\paste\models\search\PasteSearch;
use yii\web\NotFoundHttpException;

/**
 * DefaultController implements the CRUD actions for Paste model.
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
				'modelClass' => Paste::class,
				'forceCreate' => false
			],
		]);
	}
	
	/**
	 * Lists all Paste models.
	 * @return mixed
	 */
	public function actionIndex() {
		$searchModel = new PasteSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		$dataProvider->query->andWhere(['created_by' => Yii::$app->user->id]);

		// Render view
		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	/**
	 * Displays a single Paste model.
	 *
	 * @return string
	 */
	/**
	 * @param string $slug
	 *
	 * @return string
	 * @throws NotFoundHttpException
	 */
	public function actionView($slug) {
		
		/** @var Paste $model */
		$model = Paste::find()->where('slug = :slug', [
			':slug' => $slug,
		])->one();
		if (!$model)
			throw new NotFoundHttpException(Yii::t('paste', 'errors_not_found'));
		if ($model->is_private && !$model->getIsOwn()) {
			throw new NotFoundHttpException(Yii::t('paste', 'errors_private'));
		}

		// Render view
		return $this->render('view', [
			'model' => $model,
		]);
	}

	/**
	 * Creates a new Paste model.
	 * If creation is successful, the browser will be redirected to the 'index' page.
	 *
	 * @return mixed
	 */
	public function actionCreate() {
		
		// Create model
		$model = new Paste();
		$model->mode = 'javascript';
		$model->status = Status::ENABLED;
		
		// Validate and save
		if ($model->load(Yii::$app->request->post()) && $model->save()) {

			// Set message
			Yii::$app->getSession()->setFlash('success', Yii::t('paste', 'message_create_success'));

			// Redirect to view
			return $this->redirect(['index']);
		}
		
		// Render view
		return $this->render('create', [
			'model' => $model,
		]);
	}

	/**
	 * Updates an existing Paste model.
	 * If update is successful, the browser will be redirected to the 'index' page.
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
			Yii::$app->getSession()->setFlash('success', Yii::t('paste', 'message_update_success'));

			// Redirect to view
			return $this->redirect(['index']);
		}
		
		// Render view
		return $this->render('update', [
			'model' => $model,
		]);
	}

	/**
	 * Deletes an existing Paste model.
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
		Yii::$app->getSession()->setFlash('success', Yii::t('paste', 'message_delete_success'));

		// Redirect to index
		return $this->redirect(['index']);
	}

	/**
	 * Finds the Paste model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 *
	 * @param integer $id
	 * @param bool $own
	 *
	 * @return Paste the loaded model
	 *
	 * @return array|null|\yii\db\ActiveRecord
	 */
	protected function findModel($id, $own = false) {
		return Paste::findBy($id, true, 'paste', [], false, $own);
	}
}
