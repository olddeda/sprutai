<?php
namespace client\controllers\companies;

use client\components\Controller;

use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\Url;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

use common\modules\base\helpers\enum\Status;
use common\modules\base\components\Debug;

use common\modules\content\models\Portfolio;

use common\modules\company\models\Company;
use common\modules\company\helpers\enum\Type;

class PortfolioController extends Controller
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
	public function behaviors() {
		return ArrayHelper::merge(parent::behaviors(), [
			'access' => [
				'class' => AccessControl::class,
				'rules' => [
					[
						'allow' => true,
						'actions' => ['index', 'view'],
						'roles' => ['?', '@'],
					],
				],
			],
		]);
	}
	
	/**
	 * List portfolio works
	 *
	 * @return string
	 */
	public function actionIndex() {
		
		/** @var \common\modules\content\models\query\ContentQuery $query */
		$query = Portfolio::find()->joinWith([
			'media',
			'statistics',
			'stat',
			'tags',
			'company',
			'author' => function($query) {
				$query->joinWith('profile');
			},
		])->andWhere([
			Portfolio::tableName().'.company_id' => $this->companyId,
			Portfolio::tableName().'.status' => Status::ENABLED
		])->votes()->limit(10);
		
		$query->orderBy([
			'pinned' => SORT_DESC,
			'date_at' => SORT_DESC,
		]);
		
		// Prepare provider
		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'pagination' => [
				'pageSize' => 10,
			],
		]);
		
		// Render view
		return $this->render('index', [
			'dataProvider' => $dataProvider,
			'company' => $this->companyModel,
		]);
	}
	
	/**
	 * Displays a single Company model.
	 *
	 * @param integer $id
	 *
	 * @return string
	 * @throws NotFoundHttpException
	 * @throws \yii\db\Exception
	 */
	public function actionView($id) {
		
		// Load model
		$model = $this->loadModel($id);
		
		// Set visit
		$model->setStat();
		
		// Render view
		return $this->render('view', [
			'model' => $model,
			'company' => $this->companyModel,
		]);
	}
	
	/**
	 * Load portfolio model
	 * @param $id
	 *
	 * @return Company
	 * @throws NotFoundHttpException
	 * @throws \yii\db\Exception
	 */
	private function loadModel($id) {
		return Portfolio::findBy($id, true, 'content-portfolio', ['tags']);
	}
}