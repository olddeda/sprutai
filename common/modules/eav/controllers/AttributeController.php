<?php
namespace common\modules\eav\controllers;

use Yii;
use yii\web\NotFoundHttpException;

use common\modules\base\components\Controller;

use common\modules\eav\models\EavAttribute;
use common\modules\eav\models\EavAttributeSearch;

/**
 * Class AttributeController
 * @package common\modules\eav\controllers
 *
 * AttributeController implements the CRUD actions for EavAttribute model.
 */
class AttributeController extends Controller
{
    /**
     * Lists all EavAttribute models.
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new EavAttributeSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single EavAttribute model.
	 * @param integer $id
	 *
	 * @return string
	 * @throws NotFoundHttpException
	 */
    public function actionView($id) {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new EavAttribute model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new EavAttribute();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }
		else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing EavAttribute model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
	 *
	 * @return string|\yii\web\Response
	 * @throws NotFoundHttpException
	 */
    public function actionUpdate($id) {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }
        else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing EavAttribute model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
	 *
	 * @return \yii\web\Response
	 * @throws NotFoundHttpException
	 * @throws \Throwable
	 * @throws \yii\db\StaleObjectException
	 */
    public function actionDelete($id) {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the EavAttribute model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return EavAttribute the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = EavAttribute::findOne($id)) !== null) {
            return $model;
        }
        else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
