<?php
namespace client\controllers\projects;

use client\components\Controller;
use client\controllers\ContentController;

use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\Url;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

use common\modules\base\helpers\enum\Status;

use common\modules\tag\models\Tag;


use common\modules\content\models\ContentStat;
use common\modules\content\models\Event;
use common\modules\content\models\search\EventSearch;

use common\modules\payment\models\Payment;
use common\modules\payment\models\search\PaymentSearch;

use common\modules\vote\models\Vote;

use common\modules\project\models\Project;
use common\modules\project\models\search\ProjectSearch;

class DefaultController extends ContentController
{
	/**
	 * @var string
	 */
	public $modelClass = '\common\modules\project\models\Project';
	
	/**
	 * @var string
	 */
	public $routeView = '/projects/view';
	
	/**
	 * @inheritdoc
	 */
	public function behaviors() {
		return ArrayHelper::merge(parent::behaviors(), [
			'access' => [
				'class' => AccessControl::class,
				'rules' => [
					[
						'allow' => true,
						'actions' => ['event', 'event-view', 'payment', 'comment'],
						'roles' => ['?', '@'],
					],
				],
			],
		]);
	}
	
	/**
	 * Lists events of Project model.
	 * @return mixed
	 */
	public function actionEvent($id) {
		
		// Find model
		$model = $this->findModel($id);
		
		// Create event search
		$searchModel = new EventSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		$dataProvider->query->andWhere([
			Event::tableName().'.module_type' => $model->getModuleType(),
			Event::tableName().'.module_id' => $model->id,
		]);
		
		if (!Yii::$app->user->getIsAdmin() && !Yii::$app->user->getIsEditor()) {
			$dataProvider->query->andWhere([
				Event::tableName().'.status' => Status::ENABLED,
			]);
		}
		
		// Render view
		return $this->render('event', [
			'model' => $model,
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}
	
	/**
	 * Show event
	 * @param $project_id
	 * @param $id
	 *
	 * @throws NotFoundHttpException
	 */
	public function actionEventView($project_id, $id) {
		
		// Find project
		$project = $this->findModel($project_id);
		
		// Find event
		$model = Event::findById($id, true, 'project-event');
		
		// Render view
		return $this->render('event-view', [
			'project' => $project,
			'model' => $model,
		]);
	}
	
	/**
	 * Lists payments of Project model.
	 * @return mixed
	 */
	public function actionPayment($id) {
		
		// Find model
		$model = $this->findModel($id);
		
		// Create payment search
		$searchModel = new PaymentSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		$dataProvider->query->andWhere([
			Payment::tableName().'.module_type' => $model->getModuleType(),
			Payment::tableName().'.module_id' => $model->id,
			Payment::tableName().'.status' => \common\modules\payment\helpers\enum\Status::PAID,
		]);
		
		// Render view
		return $this->render('payment', [
			'model' => $model,
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}
	
	/**
	 * Lists comments of Project model.
	 * @return mixed
	 */
	public function actionComment($id) {
		
		// Find model
		$model = $this->findModel($id);
		
		// Render view
		return $this->render('comment', [
			'model' => $model,
		]);
	}
	
	/**
	 * Finds the Project model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 *
	 * @param integer $id
	 * @param bool|false $own
	 *
	 * @return Project the loaded model
	 */
	protected function findModel($id, $own = false) {
		
		// Create project search
		$query = Project::find()->andWhere([
			Project::tableName().'.id' => (int)$id,
		]);
		
		if (!Yii::$app->user->getIsAdmin() && !Yii::$app->user->getIsEditor()) {
			$query->andWhere([
				Project::tableName().'.status' => Status::ENABLED,
			]);
		}
		
		foreach ([\common\modules\vote\models\Vote::CONTENT_VOTE, \common\modules\vote\models\Vote::CONTENT_FAVORITE] as $entity) {
			$query->withVoteAggregate($entity);
			$query->withUserVote($entity);
		}
		
		$model = $query->one();
		if ($model === null)
			throw new NotFoundHttpException(Yii::t('project', 'error_not_exists'));
		
		
		return $model;
	}
}