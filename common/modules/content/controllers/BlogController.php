<?php
namespace common\modules\content\controllers;

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

/**
 * BlogController implements the CRUD actions for Blog model.
 */
class BlogController extends Controller
{
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
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(Blog::tableName().'.company_id = 0');
		
		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
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
			'model' => $this->findModel($id, true),
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
		])->one();
		if (!$model) {
			$model = new Blog();
			$model->type = Blog::type();
			$model->status = Status::TEMP;
			$model->is_main = Boolean::NO;
			$model->created_by = Yii::$app->user->id;
			$model->save(false);
		}
		$model->is_main = Boolean::NO;
		$model->pinned = Boolean::NO;
		
		$model->status = Status::DRAFT;
		//if (!Yii::$app->user->hasRole([Role::SUPERADMIN, Role::ADMIN, Role::EDITOR]))
		//	$model->status = Status::MODERATED;
		
		// Validate and save
		if ($model->load(Yii::$app->request->post())) {
			
			if (isset($_POST['draft']))
				$model->status = Status::DRAFT;
			
			if ($model->save()) {
				
				// Set message
				Yii::$app->getSession()->setFlash('success', Yii::t('content-blog', 'message_create_success'));
				
				// Redirect to view
				if (Yii::$app->user->hasRole([Role::SUPERADMIN, Role::ADMIN, Role::EDITOR]))
					return $this->redirect(['view', 'id' => $model->id]);
				return $this->redirect(['index']);
			}
			else {
				
				// Render view
				return $this->render('create', [
					'model' => $model,
				]);
			}
		}
		else {
			
			// Render view
			return $this->render('create', [
				'model' => $model,
			]);
		}
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
		
		//if (!Yii::$app->user->hasRole([Role::SUPERADMIN, Role::ADMIN, Role::EDITOR])) {
		//	if (in_array($model->status, [Status::MODERATED, Status::ENABLED])) {
		//		throw new NotFoundHttpException(Yii::t('content-blog', 'error_moderated'));
		//	}
		//	$model->status = Status::MODERATED;
		//}
		
		// Validate and save
		if ($model->load(Yii::$app->request->post())) {
			if (isset($_POST['draft']))
				$model->status = Status::DRAFT;
			
			if ($model->save()) {
				
				// Set message
				Yii::$app->getSession()->setFlash('success', Yii::t('content-blog', 'message_update_success'));
				
				// Redirect to view
				if (Yii::$app->user->hasRole([Role::SUPERADMIN, Role::ADMIN, Role::EDITOR]))
					return $this->redirect(['view', 'id' => $model->id]);
				return $this->redirect(['index']);
			}
			else {
				
				// Render view
				return $this->render('update', [
					'model' => $model,
				]);
			}
		}
		else {
			
			// Render view
			return $this->render('update', [
				'model' => $model,
			]);
		}
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
		return $this->redirect(['index']);
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
		return Blog::findBy($id, true, 'content-blog', ['tags', 'paymentTypes'], false, $own);
	}
}