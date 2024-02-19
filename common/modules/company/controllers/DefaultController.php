<?php
namespace common\modules\company\controllers;

use Yii;
use yii\helpers\ArrayHelper;

use common\modules\base\components\Controller;
use common\modules\base\helpers\enum\Status;
use common\modules\base\extensions\editable\EditableAction;
use common\modules\base\components\Debug;

use common\modules\content\models\Portfolio;
use common\modules\content\models\search\PortfolioSearch;

use common\modules\company\models\Company;
use common\modules\company\models\search\CompanySearch;

/**
 * DefaultController implements the CRUD actions for Company model.
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
				'modelClass' => Company::class,
			],
		]);
	}
	
    /**
     * Lists all Company models.
     * @return mixed
     */
    public function actionIndex() {
		$params = Yii::$app->request->queryParams;

        $searchModel = new CompanySearch();
        $dataProvider = $searchModel->search($params);

        // Render view
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
	
	/**
	 * List portfolio
	 * @return string
	 */
    public function actionPortfolio() {
		$searchModel = new PortfolioSearch();
		$dataProvider = $searchModel->search(ArrayHelper::merge(Yii::$app->request->queryParams, ['skip_author_id' => true]));
		if (!Yii::$app->user->getIsAdmin() && !Yii::$app->user->getIsEditor()) {
			$companies = Company::findByUserId(Yii::$app->user->id);
			$dataProvider->query->andWhere(['in', Portfolio::tableName().'.company_id', ArrayHelper::getColumn($companies, 'id')]);
		}
	
		// Render view
		return $this->render('portfolio', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

    /**
     * Displays a single Company model.
	 *
     * @param integer $id
	 *
     * @return mixed
     */
    public function actionView($id) {
        return $this->render('view', [
            'model' => $this->findModel($id, true),
        ]);
    }

    /**
     * Creates a new Company model.
	 *
     * @return mixed
     */
    public function actionCreate() {
		
		// Create model
		$model = Company::find()->where([
			'status' => Status::TEMP,
			'created_by' => Yii::$app->user->id,
		])->one();
		if (!$model) {
			$model = new Company();
			$model->status = Status::TEMP;
			$model->created_by = Yii::$app->user->id;
			$model->save(false);
		}
		$model->status = Status::ENABLED;
		
		// Validate and save
		if ($model->load(Yii::$app->request->post()) && $model->save()) {
		
			// Set message
			Yii::$app->getSession()->setFlash('success', Yii::t('company', 'message_create_success'));
		
			// Redirect to view
			return $this->redirect(['index']);
		}
	
		// Render view
		return $this->render('create', [
			'model' => $model,
		]);
    }

    /**
     * Updates an existing Company model.
     * If update is successful, the browser will be redirected to the 'view' page.
	 *
     * @param integer $id
	 *
     * @return mixed
     */
    public function actionUpdate($id) {
    	
    	// Find model
        $model = $this->findModel($id, true);
	
		// Validate and save
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
	
			// Set message
			Yii::$app->getSession()->setFlash('success', Yii::t('company', 'message_update_success'));
	
			// Redirect to index
			return $this->redirect(['index']);
        }
	
        // Render view
		return $this->render('update', [
			'model' => $model,
		]);
    }

    /**
     * Deletes an existing Lookup model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
	 *
     * @param integer $id
	 *
	 * @return \yii\web\Response
	 * @throws \Throwable
	 * @throws \yii\db\StaleObjectException
	 */
    public function actionDelete($id) {
        $this->findModel($id, true)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Company model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
	 *
     * @param integer $id
	 *
	 * @return Company|null|\yii\db\ActiveRecord
	 */
    protected function findModel($id, $own = false) {
		return Company::findBy($id, true, 'company', ['users'], false, $own);
    }

}