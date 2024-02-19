<?php
namespace client\controllers;

use client\components\Controller;

use Yii;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;
use yii\helpers\ArrayHelper;

use common\modules\base\helpers\enum\Status;

use common\modules\payment\models\Payment;
use common\modules\payment\models\search\PaymentSearch;

use common\modules\plugin\models\Plugin;
use common\modules\plugin\models\Version;
use common\modules\plugin\models\search\VersionSearch;

/**
 * Class PluginsController
 * @package client\controllers
 */
class PluginsController extends ContentController
{
	/**
	 * @var string
	 */
	public $modelClass = '\common\modules\plugin\models\Plugin';
	
	/**
	 * @var string
	 */
	public $routeView = '/plugins/view';
	
	public $joinWith = [
		'mediaLogo',
		'version'
	];
	
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
						'actions' => ['instruction', 'version', 'payment', 'comment'],
						'roles' => ['?', '@'],
					],
					[
						'allow' => true,
						'actions' => ['download'],
						'roles' => ['@'],
					],
				],
			],
		]);
	}
	
	
	/**
	 * Displays a instruction Plugin model.
	 * @param integer $id
	 *
	 * @return string
	 * @throws NotFoundHttpException
	 */
	public function actionInstruction($id) {
		
		// Find model
		$model = $this->findModel($id);
		
		// Render view
		return $this->render('instruction', [
			'model' => $model,
		]);
	}
	
	/**
	 * Displays a versions Plugin model.
	 * @param integer $id
	 *
	 * @return string
	 * @throws NotFoundHttpException
	 */
	public function actionVersion($id) {
		
		// Find model
		$model = $this->findModel($id);
		
		// Create event search
		$searchModel = new VersionSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		$dataProvider->query->andWhere([
			Version::tableName().'.plugin_id' => $model->id,
			Version::tableName().'.status' => Status::ENABLED,
		]);
		
		// Render view
		return $this->render('version', [
			'model' => $model,
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}
	
	/**
	 * Lists payments of Plugin model.
	 *
	 * @param integer $id
	 *
	 * @return string
	 * @throws NotFoundHttpException
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
	 * Lists comments of Plugin model.
	 *
	 * @param integer $id
	 *
	 * @return string
	 * @throws NotFoundHttpException
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
	 * @param $id
	 *
	 * @return \yii\web\Response
	 * @throws NotFoundHttpException
	 */
	public function actionDownload($id) {
		
		/** @var Version $version */
		$version = Version::find()->where('id = :id AND status = :status', [
			'id' => $id,
			'status' => Status::ENABLED,
		])->one();
		if ($version === null)
			throw new NotFoundHttpException(Yii::t('plugin-version', 'error_not_exists'));
		
		if (!$version->plugin->getCanDownload())
			throw new NotFoundHttpException(Yii::t('plugin', 'error_not_paid_access'));
		
		if ($version->plugin_id == 704)
			return $this->redirect('http://sprut-1.ams3.digitaloceanspaces.com/67/rasp-ha-v1.0.zip');
		
		if ($version->plugin_id == 184)
			return $this->redirect('http://sprut-1.ams3.digitaloceanspaces.com/67/homebridge_node10.img.zip');
		
		/** @var /common/modules/media/Module $module */
		$module = Yii::$app->getModule('media');
		
		/** @var $fs \creocoder\flysystem\LocalFilesystem $filesystem */
		$fs = $module->fs;
		
		$fileDir = $version->getFilePath();
		$fileName = $version->getFile();
		$filePath = $fileDir.$fileName;
		
		if (!$fs->has($filePath))
			throw new NotFoundHttpException(Yii::t('plugin-version', 'error_not_exists'));
		
		$fileMimeType = $fs->getMimetype($filePath);
		$fileSize = $fs->getSize($filePath);
		
		header('Content-Disposition: attachment; filename='.$fileName.';');
		header('Content-Type: '.$fileMimeType);
		header('Content-Length: '.$fileSize);
		
		$stream = $fs->readStream($filePath);
		fpassthru($stream);
		exit;
		
	}
	
	/**
	 * Finds the Plugin model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 *
	 * @param integer $id
	 * @param bool|false $own
	 *
	 * @return array|Plugin|null|\yii\db\ActiveRecord
	 * @throws NotFoundHttpException
	 */
	protected function findModel($id, $own = false) {
		
		// Create plugin search
		$query = Plugin::find()->andWhere([
			Plugin::tableName().'.id' => (int)$id,
			Plugin::tableName().'.status' => Status::ENABLED
		]);
		
		foreach ([\common\modules\vote\models\Vote::CONTENT_VOTE, \common\modules\vote\models\Vote::CONTENT_FAVORITE] as $entity) {
			$query->withVoteAggregate($entity);
			$query->withUserVote($entity);
		}
		
		$model = $query->one();
		if ($model === null)
			throw new NotFoundHttpException(Yii::t('plugin', 'error_not_exists'));
		
		
		return $model;
	}
}