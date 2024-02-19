<?php
namespace common\modules\queues\controllers;

use Yii;
use yii\data\ArrayDataProvider;

use common\modules\base\components\Controller;

/**
 * Class DefaultController
 * @package common\modules\queues\controllers
 */
class DefaultController extends Controller
{

	public function actionIndex() {
		
		$dataProvider = new ArrayDataProvider([
			'allModels' => Yii::$app->getModule('queues')->jobs,
		]);
		
		return $this->render('index', [
			'dataProvider' => $dataProvider,
		]);
	}
	
	public function actionRun($id) {
		$job = Yii::$app->getModule('queues')->jobs[$id];
		
		Yii::$app->queue->push(Yii::createObject([
			'class' => $job['class']
		]));
		
		// Set message
		Yii::$app->getSession()->setFlash('success', Yii::t('queues', 'message_run_success', ['title' => $job['name']]));
		
		return $this->redirect(['index']);
	}
}