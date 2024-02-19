<?php

namespace common\modules\media\controllers;

use common\modules\base\helpers\enum\Boolean;
use Yii;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

use common\modules\base\components\Controller;

use common\modules\media\models\MediaFormat;
use common\modules\media\models\search\MediaFormatSearch;

/**
 * FormatController implements the CRUD actions for MediaFormat model.
 */
class FormatController extends Controller
{
    /**
     * Lists all MediaFormat models.
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new MediaFormatSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		// Render view
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single MediaFormat model.
	 *
     * @param integer $id
	 *
	 * @return string
	 * @throws NotFoundHttpException
	 */
    public function actionView($id) {

		// Render view
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new MediaFormat model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new MediaFormat();
		$model->watermark = Boolean::NO;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
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
     * Updates an existing MediaFormat model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
	/**
	 * @param $id
	 *
	 * @return string|\yii\web\Response
	 * @throws NotFoundHttpException
	 */
    public function actionUpdate($id) {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
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
     * Deletes an existing MediaFormat model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
	 *
     * @param integer $id
	 *
	 * @return \yii\web\Response
	 * @throws NotFoundHttpException
	 */
    public function actionDelete($id) {
        $this->findModel($id)->delete();
        return $this->redirect(['index']);
    }

    /**
     * Finds the MediaFormat model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return MediaFormat the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = MediaFormat::findOne($id)) !== null) {
            return $model;
        }
		else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
