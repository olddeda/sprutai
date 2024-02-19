<?php
namespace client\controllers;

use Yii;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

use common\modules\base\extensions\editable\EditableAction;

use common\modules\base\components\Controller;
use common\modules\base\components\Debug;
use common\modules\base\helpers\enum\Status;

use common\modules\rbac\components\AccessControl;

use common\modules\user\models\User;

use common\modules\paste\models\Paste;
use common\modules\paste\models\search\PasteSearch;

/**
 * PastesController implements the CRUD actions for Paste model.
 */
class PastesController extends Controller
{
	/**
	 * @inheritdoc
	 */
	public function behaviors() {
		return array_merge(parent::behaviors(), [
			'access' => [
				'class' => AccessControl::class,
				'rules' => [
					[
						'allow' => true,
						'actions' => ['index', 'view', 'user'],
						'roles' => ['?', '@'],
					],
				],
			],
		]);
	}
	
	
	/**
	 * Lists all Paste models.
	 * @return mixed
	 */
	public function actionIndex() {
		$searchModel = new PasteSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		$dataProvider->query->andWhere([
			'is_private' => false,
			'status' => Status::ENABLED,
		]);

		// Render view
		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}
	
	/**
	 * List all Paste model by user id
	 * @param $user_id
	 *
	 * @return string
	 */
	public function actionUser($user_id) {
		$searchModel = new PasteSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		$dataProvider->query->andWhere([
			'created_by' => $user_id,
			'is_private' => false,
			'status' => Status::ENABLED,
		]);
		
		$user = User::findById($user_id,true, 'user');
		
		// Render view
		return $this->render('user', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
			'user' => $user,
		]);
	}

	/**
	 * Displays a single Paste model.
	 *
	 * @return string
	 */
	/**
	 * @param string $slug
	 *
	 * @return string
	 * @throws NotFoundHttpException
	 */
	public function actionView($slug) {
		
		/** @var Paste $model */
		$model = Paste::find()->where('slug = :slug', [
			':slug' => $slug,
		])->one();
		if (!$model)
			throw new NotFoundHttpException(Yii::t('paste', 'errors_not_found'));
		if ($model->is_private && !$model->getIsOwn()) {
			throw new NotFoundHttpException(Yii::t('paste', 'errors_private'));
		}

		// Render view
		return $this->render('view', [
			'model' => $model,
		]);
	}
}
