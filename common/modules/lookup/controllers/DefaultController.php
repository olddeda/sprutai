<?php
namespace common\modules\lookup\controllers;

use Yii;
use yii\web\NotFoundHttpException;

use common\modules\base\components\Controller;
use common\modules\base\helpers\enum\Status;
use common\modules\base\components\Debug;

use common\modules\lookup\models\Lookup;
use common\modules\lookup\models\search\LookupSearch;

/**
 * LookupController implements the CRUD actions for Lookup model.
 */
class DefaultController extends Controller
{
	/** @var integer */
    public $parentId;
    
    /** @var Lookup */
    public $parentModel;
    
    /** @var array */
    public $parentParams = [];
	
	/**
     * @inheritdoc
     */
    public function beforeAction($action) {
        $this->parentId = Yii::$app->request->get('parent_id', 0);
        if ($this->parentId) {
			$this->parentModel = Lookup::findById($this->parentId);
			$this->parentParams['parent_id'] = $this->parentId;
		}

        return parent::beforeAction($action);
    }

    /**
     * Lists all Lookup models.
     * @return mixed
     */
    public function actionIndex() {
        $params = Yii::$app->request->queryParams;
        $params['parent_id'] = $this->parentId;

        $searchModel = new LookupSearch();
        $dataProvider = $searchModel->search($params);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'params' => $params,
        ]);
    }

    /**
     * Displays a single Lookup model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id) {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Lookup model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
		
		// Create model
		$model = Lookup::find()->where([
			'status' => Status::TEMP,
			'created_by' => Yii::$app->user->id,
		])->one();
		if (!$model) {
			$model = new Lookup();
			$model->status = Status::TEMP;
			$model->created_by = Yii::$app->user->id;
			$model->save(false);
		}
		$model->status = Status::ENABLED;
		$model->sequence = '';
	
		// Enable AJAX validation
		//$this->performAjaxValidation($model);
	
		// Validate and save
		if ($model->load(Yii::$app->request->post()) && $model->save()) {
		
			// Set message
			Yii::$app->getSession()->setFlash('success', Yii::t('lookup', 'message_create_success'));
		
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
     * Updates an existing Lookup model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id) {
    	
    	// Find model
        $model = $this->findModel($id);
	
		// Validate and save
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
	
			// Set message
			Yii::$app->getSession()->setFlash('success', Yii::t('lookup', 'message_update_success'));
	
			// Redirect to view
			return $this->redirect(['index']);
        }
        else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Lookup model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id) {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    public function actionLog($id) {
        $model = $this->findModel($id);
        return $this->render('log', [
            'model' => $model
        ]);
    }

    /**
     * Finds the Lookup model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Lookup the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = Lookup::findOne($id)) !== null) {
            return $model;
        }
        else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}