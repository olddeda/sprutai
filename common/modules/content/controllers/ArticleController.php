<?php
namespace common\modules\content\controllers;

use common\modules\base\components\Controller;
use common\modules\base\extensions\editable\EditableAction;
use common\modules\base\helpers\enum\Boolean;
use common\modules\content\helpers\enum\Status;
use common\modules\content\models\Article;
use common\modules\content\models\Page;
use common\modules\content\models\search\ArticleSearch;
use common\modules\rbac\helpers\enum\Role;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

/**
 * ArticleController implements the CRUD actions for Article model.
 */
class ArticleController extends Controller
{
	/**
	 * @inheritdoc
	 */
	public function actions() {
		return ArrayHelper::merge(parent::actions(), [
			'editable' => [
				'class' => EditableAction::class,
				'modelClass' => Article::class,
			],
		]);
	}
	
	/**
	 * Lists all Content models.
	 * @return mixed
	 */
	public function actionIndex() {
		$searchModel = new ArticleSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(Article::tableName().'.company_id = 0');
		
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
		$model = Article::find()->where([
			'type' => Article::type(),
			'status' => Status::TEMP,
			'created_by' => Yii::$app->user->id,
		])->one();
		if (!$model) {
			$model = new Article();
			$model->type = Article::type();
			$model->status = Status::TEMP;
			$model->is_main = Boolean::NO;
			$model->created_by = Yii::$app->user->id;
			$model->save(false);
		}
		$model->is_main = Boolean::NO;
		$model->pinned = Boolean::NO;
		$model->status = Status::DRAFT;
		$model->notification = true;
		if (!Yii::$app->user->hasRole([Role::SUPERADMIN, Role::ADMIN, Role::EDITOR]))
			$model->status = Status::MODERATED;
		
		if (!Article::find()->andWhere([
			'author_id' => Yii::$app->user->id,
			'status' => Status::ENABLED,
		])->count() && $page = Page::findBySlug('instruction-author-beginner')) {
			$model->text = $page->text;
		}
		
		// Validate and save
		if ($model->load(Yii::$app->request->post())) {
			
			if (isset($_POST['draft']))
				$model->status = Status::DRAFT;

			$model->seo->delete();
			
			if ($model->save()) {
				
				// Set message
				Yii::$app->getSession()->setFlash('success', Yii::t('content-article', 'message_create_success'));
				
				// Redirect to view
				if (Yii::$app->user->hasRole([Role::SUPERADMIN, Role::ADMIN, Role::EDITOR]))
					return $this->redirect(['view', 'id' => $model->id]);
				return $this->redirect(['index']);
			}
		}
		
		// Render view
		return $this->render('create', [
			'model' => $model,
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
		
		$isUser = !Yii::$app->user->hasRole([Role::SUPERADMIN, Role::ADMIN, Role::EDITOR]);
		if ($isUser && in_array($model->status, [Status::MODERATED, Status::ENABLED])) {
			throw new NotFoundHttpException(Yii::t('content-article', 'error_moderated'));
		}

		if (is_null($model->text_new)) {
		    $model->text_new = $model->text;
		    $model->is_backup = true;
		    $model->save(false);
        }
		
		// Validate and save
		if ($model->load(Yii::$app->request->post())) {
			
			if (isset($_POST['draft']))
				$model->status = Status::DRAFT;
			
			if ($isUser) {
				if (!in_array($model->status, [Status::DRAFT, Status::MODERATED])) {
					$model->status = Status::MODERATED;
				}
			}
			
			if ($model->save()) {
				
				// Set message
				Yii::$app->getSession()->setFlash('success', Yii::t('content-article', 'message_update_success'));
				
				// Redirect to view
				if (Yii::$app->user->hasRole([Role::SUPERADMIN, Role::ADMIN, Role::EDITOR]))
					return $this->redirect(['view', 'id' => $model->id]);
				return $this->redirect(['index']);
			}
		}
		
		// Render view
		return $this->render('update', [
			'model' => $model,
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
		Yii::$app->getSession()->setFlash('success', Yii::t('content-article', 'message_delete_success'));
		
		// Redirect to index
		return $this->redirect(['index']);
	}

    /**
     * @param $id
     *
     * @throws NotFoundHttpException
     */
	public function actionBackup($id) {

        // Load model
        $model = $this->findModel($id, true);

        $isUser = !Yii::$app->user->hasRole([Role::SUPERADMIN, Role::ADMIN, Role::EDITOR]);
        if ($isUser && in_array($model->status, [Status::MODERATED, Status::ENABLED])) {
            throw new NotFoundHttpException(Yii::t('content-article', 'error_moderated'));
        }

        $model->text_new = Yii::$app->request->post('text_new');
        $model->is_backup = true;
        $model->save(false);
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
		return Article::findBy($id, true, 'content-article', ['tags', 'paymentTypes', 'contentModuleCatalogItems'], false, $own);
	}
}