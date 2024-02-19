<?php

namespace common\modules\log\controllers;

use Yii;
use yii\data\ArrayDataProvider;
use yii\web\NotFoundHttpException;

use common\modules\base\components\Controller;

use common\modules\log\Log;

/**
 * Default controller for the `log` module
 */
class DefaultController extends Controller
{
	/**
	 * @var \common\modules\log\Module
	 */
	public $module;
	
	/**
	 * Renders the index view for the module
	 * @return string
	 */
	public function actionIndex() {
		return $this->render('index', [
			'dataProvider' => new ArrayDataProvider([
				'allModels' => $this->module->getLogs(),
				'sort' => [
					'attributes' => [
						'name',
						'size' => ['default' => SORT_DESC],
						'updatedAt' => ['default' => SORT_DESC],
					],
				],
				'pagination' => ['pageSize' => 0],
			]),
		]);
	}
	
	/**
	 * @param string $slug
	 * @param string $stamp
	 *
	 * @return string
	 * @throws NotFoundHttpException
	 */
	public function actionView($slug, $stamp = null) {
		$log = $this->find($slug, $stamp);
		if ($log->isExist) {
			return Yii::$app->response->sendFile($log->fileName, $log->downloadName, ['inline' => true]);
		} else {
			throw new NotFoundHttpException('Log not found.');
		}
	}
	
	public function actionArchive($slug) {
		if ($this->find($slug, null)->archive(date('YmdHis'))) {
			return $this->redirect([
				'history',
				'slug' => $slug
			]);
		} else {
			throw new NotFoundHttpException('Log not found.');
		}
	}
	
	public function actionHistory($slug) {
		$log = $this->find($slug, null);
		
		return $this->render('history', [
			'name' => $log->name,
			'dataProvider' => new ArrayDataProvider([
				'allModels' => $this->module->getHistory($log),
				'sort' => [
					'attributes' => [
						'fileName',
						'size' => ['default' => SORT_DESC],
						'updatedAt' => ['default' => SORT_DESC],
					],
					'defaultOrder' => ['updatedAt' => SORT_DESC],
				],
			]),
		]);
	}
	
	/**
	 * @param string $slug
	 * @param null|string $stamp
	 *
	 * @return Log
	 * @throws NotFoundHttpException
	 */
	protected function find($slug, $stamp) {
		if ($log = $this->module->findLog($slug, $stamp)) {
			return $log;
		} else {
			throw new NotFoundHttpException('Log not found.');
		}
	}
}
