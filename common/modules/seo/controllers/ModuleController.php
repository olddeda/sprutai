<?php
namespace common\modules\seo\controllers;

use Yii;
use yii\web\NotFoundHttpException;

use common\modules\base\components\Controller;


use common\modules\seo\models\SeoModule;
use common\modules\seo\models\search\SeoModuleSearch;

/**
 * ModuleController implements the CRUD actions for SeoModule model.
 */
class ModuleController extends Controller
{
	/**
	 * Lists all SeoModule models.
	 * @return mixed
	 */
	public function actionIndex() {
		$searchModel = new SeoModuleSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		
		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}
	
	/**
	 * Updates an existing SeoModule model.
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
			Yii::$app->getSession()->setFlash('success', Yii::t('seo-module', 'message_update_success'));
			
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
	 * Finds the SeoModule model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 *
	 * @param integer $id
	 * @param bool|false $own
	 *
	 * @return SeoModule the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel($id, $own = false) {
		return SeoModule::findBy($id, true, 'seo-module', [], false, $own);
	}
}
