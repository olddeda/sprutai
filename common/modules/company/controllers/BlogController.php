<?php
namespace common\modules\company\controllers;

use Yii;
use yii\sphinx\Query;
use yii\web\NotFoundHttpException;
use yii\helpers\ArrayHelper;

use common\modules\base\extensions\editable\EditableAction;

use common\modules\base\components\Controller;
use common\modules\base\components\Debug;
use common\modules\base\helpers\enum\Boolean;

use common\modules\rbac\helpers\enum\Role;

use common\modules\content\helpers\enum\Status;
use common\modules\content\models\Blog;
use common\modules\content\models\search\BlogSearch;

use common\modules\company\models\Company;

/**
 * BlogController implements the CRUD actions for Blog model.
 */
class BlogController extends Controller
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
		$this->companyModel = Company::findOwn($this->companyId, true, 'company');
		
		return parent::beforeAction($action);
	}
	
	/**
	 * @inheritdoc
	 */
	public function actions() {
		return ArrayHelper::merge(parent::actions(), [
			'editable' => [
				'class' => EditableAction::class,
				'modelClass' => Blog::class,
			],
		]);
	}
	
	/**
	 * Lists all Content models.
	 * @return mixed
	 */
	public function actionIndex() {
		$searchModel = new BlogSearch();
		$dataProvider = $searchModel->search(ArrayHelper::merge(Yii::$app->request->queryParams, ['skip_author_id' => true]));
		$dataProvider->query->andWhere([Blog::tableName().'.company_id' => $this->companyId]);
		
		// Render view
		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
			'company' => $this->companyModel,
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
			'model' => $this->findModel($id),
			'company' => $this->companyModel,
		]);
	}
	
	/**
	 * Creates a new Content model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 * @return mixed
	 */
	public function actionCreate() {
		
		// Create model
		$model = Blog::find()->where([
			'type' => Blog::type(),
			'status' => Status::TEMP,
			'created_by' => Yii::$app->user->id,
			'company_id' => $this->companyId,
		])->one();
		if (!$model) {
			$model = new Blog();
			$model->type = Blog::type();
			$model->status = Status::TEMP;
			$model->is_main = Boolean::NO;
			$model->created_by = Yii::$app->user->id;
			$model->company_id = $this->companyId;
			$model->save(false);
		}
		$model->is_main = Boolean::NO;
		$model->pinned = Boolean::NO;
		$model->status = Status::DRAFT;
		
		// Validate and save
		if ($model->load(Yii::$app->request->post())) {
			
			if (isset($_POST['draft']))
				$model->status = Status::DRAFT;
			
			if ($model->save()) {
				
				// Set message
				Yii::$app->getSession()->setFlash('success', Yii::t('content-blog', 'message_create_success'));
				
				// Redirect to view
				if (Yii::$app->user->hasRole([Role::SUPERADMIN, Role::ADMIN, Role::EDITOR]))
					return $this->redirect(['view', 'company_id' => $this->companyId, 'id' => $model->id]);
				return $this->redirect(['index', 'company_id' => $this->companyId]);
			}
		}
		
		// Render view
		return $this->render('create', [
			'model' => $model,
			'company' => $this->companyModel,
		]);
	}
	
	/**
	 * Updates an existing Content model.
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
		if ($model->load(Yii::$app->request->post())) {
			if (isset($_POST['draft']))
				$model->status = Status::DRAFT;
			
			if ($model->save()) {
				
				// Set message
				Yii::$app->getSession()->setFlash('success', Yii::t('content-blog', 'message_update_success'));
				
				// Redirect to view
				if (Yii::$app->user->hasRole([Role::SUPERADMIN, Role::ADMIN, Role::EDITOR]))
					return $this->redirect(['view', 'company_id' => $this->companyId, 'id' => $model->id]);
				return $this->redirect(['index', 'company_id' => $this->companyId]);
			}
		}
		
		// Render view
		return $this->render('update', [
			'model' => $model,
			'company' => $this->companyModel,
		]);
	}
	
	/**
	 * Deletes an existing Content model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 *
	 * @param integer $id
	 *
	 * @return \yii\web\Response
	 * @throws \Throwable
	 * @throws \yii\db\StaleObjectException
	 */
	public function actionDelete($id) {
		
		// Find model and delete
		$this->findModel($id, true)->delete();
		
		// Set message
		Yii::$app->getSession()->setFlash('success', Yii::t('content-blog', 'message_delete_success'));
		
		// Redirect to index
		return $this->redirect(['index', 'company_id' => $this->companyId]);
	}
	
	/**
	 * Finds the Content model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 *
	 * @param integer $id
	 * @param bool $own
	 *
	 * @return array|null|\yii\db\ActiveRecord
	 */
	protected function findModel($id, $own = false) {
		$conditions = [];
		if (!Yii::$app->user->getIsAdmin() && !Yii::$app->user->getIsEditor())
			$conditions = ['in', 'author_id', $this->companyModel->getUsersIds()];
		return Blog::findBy($id, true, 'content-blog', ['tags'], false, $own, $conditions, ['user_id', 'created_by']);
	}
}