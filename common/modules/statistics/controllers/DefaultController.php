<?php
namespace common\modules\statistics\controllers;

use Yii;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

use common\modules\base\components\Controller;

use common\modules\rbac\components\AccessControl;

use common\modules\statistics\models\Statistics;
use common\modules\store\models\search\StatisticsSearch;

class DefaultController extends Controller
{
	/** @var object */
	public $moduleClass;
	
	/** @var integer */
	public $moduleType;
	
	/** @var integer */
	public $moduleId;
	
	/**
	 * @inheritdoc
	 */
	public function behaviors() {
		return ArrayHelper::merge(parent::behaviors(), [
			'access' => [
				'class' => AccessControl::className(),
				'rules' => [
					[
						'allow' => true,
						'actions' => ['set'],
					],
				],
			],
			'verbs' => [
				'class' => VerbFilter::className(),
				'actions' => [
					'set' => ['post'],
				],
			],
		]);
	}
	
	
	/**
	 * Lists all Statistics models.
	 * @return mixed
	 */
	public function actionIndex() {
		
		$searchModel = new StatisticsSearch();
		if (!is_null($this->moduleType))
			$searchModel->module_type = $this->moduleType;
		if (!is_null($this->moduleId))
			$searchModel->module_id = $this->moduleId;
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		
		// Render view
		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}
	
	/**
	 * Set statistics
	 */
	public function actionSet() {
		$moduleType = Yii::$app->request->post('module_type');
		$moduleId = Yii::$app->request->post('module_id');
		$type = Yii::$app->request->post('type');
		
		if (!is_null($moduleType) && !is_null($moduleId) && !is_null($type))
			Statistics::set($type, $moduleType, $moduleId);
	}
}