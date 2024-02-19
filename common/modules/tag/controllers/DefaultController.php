<?php
namespace common\modules\tag\controllers;

use common\modules\tag\helpers\enum\Type;
use Yii;
use yii\web\NotFoundHttpException;
use yii\helpers\ArrayHelper;
use yii\web\Response;

use common\modules\base\extensions\editable\EditableAction;

use common\modules\base\components\Controller;
use common\modules\base\components\Debug;

use common\modules\base\helpers\enum\Status;

use common\modules\tag\models\Tag;
use common\modules\tag\models\search\TagSearch;

/**
 * DefaultController implements the CRUD actions for Tag model.
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
				'modelClass' => Tag::class,
			],
		]);
	}
	
	/**
	 * Lists all Tag models.
	 * @return mixed
	 */
	public function actionIndex() {
		$searchModel = new TagSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		
		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}
	
	/**
	 * Creates a new Tag model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 * @return mixed
	 */
	public function actionCreate() {
		
		// Create model
		$model = Tag::find()->where([
			'status' => Status::TEMP,
			'created_by' => Yii::$app->user->id,
		])->one();
		if (!$model) {
			$model = new Tag();
			$model->status = Status::TEMP;
			$model->created_by = Yii::$app->user->id;
			$model->save(false);
		}
		$model->status = Status::ENABLED;
		$model->is_none = true;
		
		// Validate and save
		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			
			// Set message
			Yii::$app->getSession()->setFlash('success', Yii::t('tag', 'message_create_success'));
			
			// Redirect to view
			return $this->redirect(['index']);
		}

        // Render view
        return $this->render('create', [
            'model' => $model,
        ]);
	}
	
	/**
	 * Updates an existing Tag model.
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
			Yii::$app->getSession()->setFlash('success', Yii::t('tag', 'message_update_success'));
			
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
	 * Deletes an existing Tag model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 *
	 * @param integer $id
	 *
	 * @return \yii\web\Response
	 * @throws NotFoundHttpException
	 */
	public function actionDelete($id) {
		
		// Find model and delete
		$this->findModel($id, true)->delete();
		
		// Set message
		Yii::$app->getSession()->setFlash('success', Yii::t('tag', 'message_delete_success'));
		
		// Redirect to index
		return $this->redirect(['index']);
	}
	
	public function actionSearch($q = '') {
		$q = urldecode($q);
		$selected = Yii::$app->request->get('selected');
		
		$result = [
			'results' => [],
		];
		
		$query = Tag::find()->limit(100)->orderBy(['title' => SORT_ASC]);
		
		$query->andFilterWhere(['like', Tag::tableName().'.title', $q]);
		
		$models = $query->all();
		if ($models) {
			foreach ($models as $model) {
				if ($model->title) {
					$item =  [
						'id' => $model->id,
						'text' => $model->title,
					];
					if ($selected && $selected == $model->id)
						$item['selected'] = true;
					
					$result['results'][] = $item;
				}
			}
		}
		
		Yii::$app->response->format = Response::FORMAT_JSON;
		return $result;
	}
	
	/**
	 * Finds the Tag model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 *
	 * @param integer $id
	 * @param bool|false $own
	 *
	 * @return Tag the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel($id, $own = false) {
		return Tag::findBy($id, true, 'tag', [], false, $own);
	}
}