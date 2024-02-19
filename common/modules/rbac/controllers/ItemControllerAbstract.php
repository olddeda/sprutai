<?php

namespace common\modules\rbac\controllers;

use Yii;
use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

use common\modules\base\components\Debug;

use common\modules\base\components\Controller;

use common\modules\rbac\models\search\Search;
use common\modules\rbac\models\search\SearchParent;
use common\modules\rbac\models\search\SearchChild;
use common\modules\rbac\models\forms\AssignForm;

abstract class ItemControllerAbstract extends Controller
{
	/**
	 * @param  string $name
	 *
	 * @return \common\modules\rbac\models\Role|\common\modules\rbac\models\Permission
	 */
	abstract protected function getItem($name);

	/**
	 * @var int
	 */
	protected $type;

	/**
	 * @var string
	 */
	protected $modelClass;

	/**
	 * @throws InvalidConfigException
	 */
	public function init() {
		parent::init();
		if ($this->modelClass === null)
			throw new InvalidConfigException('Model class should be set');
		if ($this->type === null)
			throw new InvalidConfigException('Auth item type should be set');
	}

	/**
	 * Lists all created items.
	 * @return string
	 */
	public function actionIndex() {
		$filterModel = new Search($this->type);

		return $this->render('index', [
			'filterModel' => $filterModel,
			'dataProvider' => $filterModel->search(Yii::$app->request->get()),
		]);
	}

	/**
	 * View item
	 * @param $name
	 *
	 * @return string
	 * @throws InvalidConfigException
	 */
	public function actionView($name) {
		Url::remember('', 'actions-redirect');

		/** @var \common\modules\rbac\models\Role|\common\modules\rbac\models\Task|\common\modules\rbac\models\Permission $model */
		$item = $this->getItem($name);

		// Create model from item
		$model = Yii::createObject([
			'class' => $this->modelClass,
			'scenario' => 'update',
			'item' => $item,
		]);

		// Parent data provider
		$parentFilterModel = new SearchParent($name);
		$parentDataProvider = $parentFilterModel->search(Yii::$app->request->get());

		// Child data provider
		$childFilterModel = new SearchChild($name);
		$childDataProvider = $childFilterModel->search(Yii::$app->request->get());

		// Render view
		return $this->render('view', [
			'model' => $model,
			'parentFilterModel' => $parentFilterModel,
			'parentDataProvider' => $parentDataProvider,
			'childFilterModel' => $childFilterModel,
			'childDataProvider' => $childDataProvider,
		]);
	}

	/**
	 * Shows create form.
	 * @return string|Response
	 * @throws \yii\base\InvalidConfigException
	 */
	public function actionCreate() {

		/** @var \common\modules\rbac\models\Role|\common\modules\rbac\models\Task|\common\modules\rbac\models\Permission $model */
		$model = Yii::createObject([
			'class' => $this->modelClass,
			'scenario' => 'create',
		]);

		// Enable AJAX validation
		$this->performAjaxValidation($model);

		// Validate and save
		if ($model->load(\Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['index']);
		}

		// Render view
		return $this->render('create', [
			'model' => $model,
		]);
	}

	/**
	 * Shows update form.
	 *
	 * @param string $name
	 *
	 * @return string|Response
	 * @throws NotFoundHttpException
	 * @throws \yii\base\InvalidConfigException
	 */
	public function actionUpdate($name) {
		Url::remember('', 'actions-redirect');

		/** @var \common\modules\rbac\models\Role|\common\modules\rbac\models\Task|\common\modules\rbac\models\Permission $model */
		$item = $this->getItem($name);

		// Create model from item
		$model = Yii::createObject([
			'class' => $this->modelClass,
			'scenario' => 'update',
			'item' => $item,
		]);

		// Enable AJAX validation
		$this->performAjaxValidation($model);

		// Validate and save
		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['index']);
		}

		// Render view
		return $this->render('update', [
			'model' => $model,
		]);
	}

	/**
	 * Assign child
	 * @param $name
	 *
	 * @return Response
	 */
	public function actionAssign($name) {

		// Find item
		$item = $this->getItem($name);

		// Create assign form
		$assignModel = new AssignForm;
		$assignModel->name = $item->name;

		// Validate and save
		if ($assignModel->load(Yii::$app->request->post()) && $assignModel->validate())
			$assignModel->save();

		// Redirect to back
		return $this->redirect(Url::previous('actions-redirect'));
	}

	/**
	 * Revoke item.
	 *
	 * @param string $name
	 *
	 * @return Response
	 * @throws NotFoundHttpException
	 */
	public function actionRevoke($name) {

		// Find item
		$item = $this->getItem($name);

		// Create model from item
		$model = Yii::createObject([
			'class' => $this->modelClass,
			'item' => $item,
		]);

		$revokes = [];
		if (Yii::$app->request->get('parent'))
			$revokes['parent'] = Yii::$app->authManager->getItem(Yii::$app->request->get('parent'));
		if (Yii::$app->request->get('child'))
			$revokes['child'] = Yii::$app->authManager->getItem(Yii::$app->request->get('child'));

		// Revoke item
		$model->revoke($revokes);

		// Redirect to back
		return $this->redirect(Url::previous('actions-redirect'));
	}

	/**
	 * Deletes item.
	 *
	 * @param string $name
	 *
	 * @return Response
	 * @throws NotFoundHttpException
	 */
	public function actionDelete($name) {

		// Find item
		$item = $this->getItem($name);

		// Remove item
		Yii::$app->authManager->remove($item);

		return $this->redirect(['index']);
	}

	/**
	 * Performs ajax validation.
	 *
	 * @param Model $model
	 *
	 * @throws \yii\base\ExitException
	 */
	protected function performAjaxValidation(Model $model) {
		if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
			Yii::$app->response->format = Response::FORMAT_JSON;
			echo json_encode(ActiveForm::validate($model));
			Yii::$app->end();
		}
	}
}
