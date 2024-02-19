<?php
namespace common\modules\comments\controllers;

use common\modules\base\components\Debug;
use common\modules\payment\helpers\enum\State;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;

use common\modules\base\components\Controller;
use common\modules\base\helpers\enum\Status;

use common\modules\comments\Module;
use common\modules\comments\models\Comment;


/**
 * Class DefaultController
 * @package common\modules\comments\controllers
 */
class DefaultController extends Controller
{

	/**
	 * Behaviors
	 * @return array
	 */
	public function behaviors() {
		return ArrayHelper::merge(parent::behaviors(), [
			'verbs' => [
				'class' => VerbFilter::class,
				'actions' => [
					'create' => ['post'],
					'delete' => ['post', 'delete']
				],
			],
		]);
	}

	/**
	 * Create comment.
	 *
	 * @param $entity string encrypt entity
	 *
	 * @return array|null|Response
	 * @throws \yii\base\InvalidConfigException
	 */
	public function actionCreate($entity) {
		Yii::$app->response->format = Response::FORMAT_JSON;

		/* @var $module Module */
		$module = Yii::$app->getModule(Module::$name);

		$commentModelClass = $module->commentModelClass;

		$decryptEntity = Yii::$app->getSecurity()->decryptByKey($entity, $module::$name);

		if ($decryptEntity !== false) {
			$entityData = Json::decode($decryptEntity);
			
			$query = Comment::find()->where('entity = :entity AND entity_id = :entity_id AND created_by = :created_by', [
				':entity' => $entityData['entity'],
				':entity_id' => $entityData['entity_id'],
				':created_by' => Yii::$app->user->id,
			]);
			
			if (Yii::$app->request->post('comment-id')) {
				$query->andWhere('id = :id AND status = :status', [
					':id' => Yii::$app->request->post('comment-id'),
					':status' => Status::ENABLED,
				]);
			}
			else {
				$query->andWhere(['status' => Status::TEMP]);
			}
			
			/* @var $model Comment */
			$model = $query->one();
			if (is_null($model)) {
				$model = Yii::createObject($commentModelClass);
			}
			
			$model->setAttributes($entityData);
			if ($model->status == Status::TEMP) {
				$model->created_at = time();
				$model->updated_at = time();
			}
			$model->status = Status::ENABLED;
			
			if ($model->load(Yii::$app->request->post()) && $model->save()) {
				return [
					'status' => 'success',
				];
			}
			else {
				return [
					'status' => 'error',
					'errors' => ActiveForm::validate($model)
				];
			}
		}
		else {
			return [
				'status' => 'error',
				'message' => Yii::t('comments', 'Oops, something went wrong. Please try again later.')
			];
		}
	}

	/**
	 * Delete comment page.
	 *
	 * @param integer $id Comment ID
	 *
	 * @return string Comment text
	 * @throws NotFoundHttpException
	 */
	public function actionDelete($id) {
		if ($this->findModel($id)->deleteComment()) {
			return Yii::t('comments', 'message_comment_was_deleted');
		}
		else {
			Yii::$app->response->setStatusCode(500);
			return Yii::t('comments', 'message_delete_failed');
		}
	}

	/**
	 * Find model by ID.
	 *
	 * @param integer|array $id Comment ID
	 *
	 * @return null|Comment
	 * @throws NotFoundHttpException
	 */
	protected function findModel($id) {

		/** @var Comment $model */
		$commentModelClass = Yii::$app->getModule(Module::$name)->commentModelClass;
		if (($model = $commentModelClass::findOwn($id)) !== null) {
			return $model;
		}
		else {
			throw new NotFoundHttpException(Yii::t('comments', 'The requested page does not exist.'));
		}
	}
}
