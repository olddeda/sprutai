<?php
namespace common\modules\banner\controllers;

use Yii;
use yii\helpers\ArrayHelper;

use common\modules\rbac\components\AccessControl;

use common\modules\base\extensions\editable\EditableAction;

use common\modules\base\components\Controller;
use common\modules\base\components\Debug;

use common\modules\base\helpers\enum\Status;

use common\modules\banner\models\Banner;
use common\modules\banner\models\search\BannerSearch;

/**
 * DefaultController implements the CRUD actions for Banner model.
 */
class DefaultController extends Controller
{
	/**
	 * @inheritdoc
	 */
	public function behaviors() {
		return array_merge(parent::behaviors(), [
			'access' => [
				'class' => AccessControl::class,
				'rules' => [
					[
						'allow' => true,
						'actions' => ['visit'],
						'roles' => ['?', '@'],
					],
				],
			],
		]);
	}
	
	/**
	 * @inheritdoc
	 */
	public function actions() {
		return ArrayHelper::merge(parent::actions(), [
			'editable' => [
				'class' => EditableAction::class,
				'modelClass' => Banner::class,
				'forceCreate' => false
			],
		]);
	}
	
	/**
	 * Lists all Banner models.
	 * @return mixed
	 */
	public function actionIndex() {
		$searchModel = new BannerSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		// Render view
		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	/**
	 * Displays a single Banner model.
	 *
	 * @param integer $id
	 *
	 * @return string
	 */
	public function actionView($id) {

		// Render view
		return $this->render('view', [
			'model' => $this->findModel($id),
		]);
	}

	/**
	 * Creates a new Banner model.
	 * If creation is successful, the browser will be redirected to the 'index' page.
	 *
	 * @return mixed
	 */
	public function actionCreate() {
		
		// Create model
		$model = Banner::find()->where([
			'status' => Status::TEMP,
			'created_by' => Yii::$app->user->id,
		])->one();
		if (!$model) {
			$model = new Banner();
			$model->status = Status::TEMP;
			$model->created_by = Yii::$app->user->id;
			$model->save(false);
		}
		$model->status = Status::ENABLED;
		
		// Validate and save
		if ($model->load(Yii::$app->request->post()) && $model->save()) {

			// Set message
			Yii::$app->getSession()->setFlash('success', Yii::t('banner', 'message_create_success'));

			// Redirect to view
			return $this->redirect(['index']);
		}
		else {

			// Render view
			return $this->render('create', [
				'model' => $model,
			]);
		}
	}

	/**
	 * Updates an existing Banner model.
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
			Yii::$app->getSession()->setFlash('success', Yii::t('banner', 'message_update_success'));

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
	 * Deletes an existing Banner model.
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
		Yii::$app->getSession()->setFlash('success', Yii::t('banner', 'message_delete_success'));

		// Redirect to index
		return $this->redirect(['index']);
	}
	
	/**
	 * @param $id
	 *
	 * @return \yii\web\Response
	 */
	public function actionVisit($id) {
		
		// Load model
		$model = $this->findModel($id, false);
		
		$model->updateVisits();
		
		// Redirect to url
		return $this->redirect($model->url);
	}

	/**
	 * Finds the Banner model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 *
	 * @param integer $id
	 * @param bool $own
	 *
	 * @return Banner the loaded model
	 *
	 * @return array|null|\yii\db\ActiveRecord
	 */
	protected function findModel($id, $own = false) {
		return Banner::findBy($id, true, 'banner', [], false, $own);
	}
}
