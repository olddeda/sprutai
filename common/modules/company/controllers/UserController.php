<?php
namespace common\modules\company\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\helpers\ArrayHelper;

use common\modules\base\extensions\editable\EditableAction;
use common\modules\base\components\Debug;
use common\modules\base\components\Controller;

use common\modules\media\helpers\enum\Mode;

use common\modules\user\models\User;
use common\modules\user\models\UserProfile;

use common\modules\company\models\Company;
use common\modules\company\models\CompanyUser;
use common\modules\company\models\search\CompanyUserSearch;

/**
 * UserController implements the CRUD actions for Project model.
 */
class UserController extends Controller
{
    /**
     * @var integer
     */
    public $companyId;

    /**
     * @var \common\modules\company\models\Company
     */
    public $companyModel;

    /**
     * @inheritdoc
     */
    public function beforeAction($action) {
        $this->companyId = Yii::$app->request->get('company_id', 0);
        $this->companyModel = Company::findById($this->companyId, true, 'company');

        return parent::beforeAction($action);
    }

	/**
	 * @inheritdoc
	 */
	public function actions() {
		return ArrayHelper::merge(parent::actions(), [
			'editable' => [
				'class' => EditableAction::class,
				'modelClass' => CompanyUser::class,
			],
		]);
	}
	
	/**
	 * Lists all company user models.
	 * @return mixed
	 */
	public function actionIndex() {
		$searchModel = new CompanyUserSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		$dataProvider->query->andWhere([
			CompanyUserSearch::tableName().'.company_id' => $this->companyModel->id,
        ]);
		
		// Render view
		return $this->render('index', [
		    'company' => $this->companyModel,
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}
	
	/**
	 * Displays a single company CompanyUser model.
	 * @param integer $id
	 *
	 * @return mixed
     */
	public function actionView($id) {
		
		// Render view
		return $this->render('view', [
			'model' => $this->findModel($id),
		]);
	}
	
	/**
	 * Creates a new company CompanyUser model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 * @return mixed
	 */
	public function actionCreate() {
		
		// Create model
        $model = new CompanyUser();
        $model->company_id = $this->companyId;
        
		// Validate and save
		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			
			// Set message
            Yii::$app->getSession()->setFlash('success', Yii::t('company-user', 'message_create_success'));
				
			// Redirect to view
			return $this->redirect(['index', 'company_id' => $this->companyId]);
		}
		
		// Render view
		return $this->render('create', [
			'model' => $model,
			'company' => $this->companyModel,
		]);
	}
	
	/**
	 * Updates an existing Project model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 *
	 * @param integer $id
	 *
	 * @return string|\yii\web\Response
	 * @throws NotFoundHttpException
	 */
	public function actionUpdate($id) {
		
		// Load model
		$model = $this->findModel($id, true);
		
		// Validate and save
		if ($model->load(Yii::$app->request->post()) && $model->save()) {
		 
			// Set message
            Yii::$app->getSession()->setFlash('success', Yii::t('company-user', 'message_update_success'));
            
            // Redirect to view
            return $this->redirect(['index', 'company_id' => $this->companyId]);
		}
		
		// Render view
		return $this->render('update', [
			'model' => $model,
			'company' => $this->companyModel,
		]);
	}
	
	/**
	 * Deletes an existing Project model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 *
	 * @param integer $id
     *
     * @return \yii\web\Response
     */
	public function actionDelete($id) {
		
		// Find model and delete
		$this->findModel($id, true)->delete(false);
		
		// Set message
		Yii::$app->getSession()->setFlash('success', Yii::t('company-user', 'message_delete_success'));
		
		// Redirect to index
		return $this->redirect(['index', 'company_id' => $this->companyId]);
	}
	
	/**
	 * Search users
	 *
	 * @param string $q
	 *
	 * @return array
	 * @throws \yii\base\InvalidConfigException
	 */
	public function actionSearch($q) {
		$q = urldecode($q);
		
		$result = [];
		
		$query = User::find()->joinWith([
			'profile',
			'telegram',
		])->limit(20);
		
		$query->andFilterWhere(['like', User::tableName().'.username', $q]);
		$query->orFilterWhere(['like', User::tableName().'.email', $q]);
		$query->orFilterWhere(['like', UserProfile::tableName().'.phone', $q]);
		$query->orFilterWhere(['like', 'telegram.username', $q]);
		$query->orFilterWhere(['like', 'CONCAT('.UserProfile::tableName().'.last_name, " ", '.UserProfile::tableName().'.first_name, " ", '.UserProfile::tableName().'.middle_name)', $q]);
		
		$models = $query->all();
		if ($models) {
			foreach ($models as $model) {
				$result[] = [
					'id' => $model->id,
					'fio' => $model->getFio(),
					'username' => $model->username,
					'email' => $model->email,
					'phone' => $model->profile->phone,
					'telegram' => ($model->telegram && $model->telegram->username) ? $model->telegram->username : null,
					'image' => $model->avatar->getImageSrc(80, 80, Mode::CROP_CENTER),
				];
			}
		}
		
		Yii::$app->response->format = Response::FORMAT_JSON;
		return $result;
	}
	
	/**
	 * Finds the CompanyUser model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 *
	 * @param integer $id
	 * @param bool|false $own
	 *
	 * @return CompanyUser the loaded model
	 */
	protected function findModel($id, $own = false) {
		return CompanyUser::findBy($id, true, 'company-user', [], false, $own);
	}
}