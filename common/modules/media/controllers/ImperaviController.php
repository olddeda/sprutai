<?php

namespace common\modules\media\controllers;

use common\modules\media\helpers\enum\Mode;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\web\Response;

use common\modules\base\components\Controller;
use common\modules\base\helpers\enum\Status;

use common\modules\media\models\Media;
use common\modules\media\components\Image;
use common\modules\media\helpers\enum\Type;

/**
 * Class ImperaviController
 * @package common\modules\media\controllers
 */
class ImperaviController extends Controller
{
	/**
	 * Behaviors
	 * @return array
	 */
	public function behaviors() {
		return [
			'verbs' => [
				'class' => VerbFilter::class,
				'actions' => [
					'create' => ['post'],
					'upload' => ['post'],
					'delete' => ['post', 'delete']
				],
			],
		];
	}

	/**
	 * List files
	 */
	public function actionIndex() {
		$result = [];
		
		$moduleType = Yii::$app->request->get('module_type');
		if ($moduleType) {
			
			$query = Media::find()->andWhere('module_type = :module_type AND status = :status', [
				':module_type' => $moduleType,
				':status' => Status::ENABLED,
			]);
			
			if (!Yii::$app->user->getIsAdmin() && Yii::$app->user->getIsEditor()) {
				$query->andWhere(['created_by' => Yii::$app->user->id]);
			}
			
			$models = $query->all();
			foreach ($models as $model) {
				$result[] = [
					'id' => $model->id,
					'title' => $model->title,
					'thumb' => $model->getImageSrc(96, 72, Mode::CROP_CENTER),
					'url' => $model->getImageSrc(2000, 2000, Mode::RESIZE),
				];
			}
		}
		

		Yii::$app->response->format = Response::FORMAT_JSON;
		echo Json::encode($result);
		return Yii::$app->end();
	}

	/**
	 * Upload file
	 */
	public function actionUpload() {
		$result = [
			//'error' => false,
		];

		/** @var common/modules/media/Module $module */
		$module = Yii::$app->getModule('media');
		
		/** @var $fs \creocoder\flysystem\AwsS3Filesystem $filesystem */
		$fs = $module->fs;
		
		/** @var $fs \creocoder\flysystem\LocalFilesystem $filesystem */
		$fsLocal = Yii::$app->fsLocal;

		$moduleType = Yii::$app->request->get('module_type');
		$moduleId = Yii::$app->request->get('module_id');
		
		if ($moduleType && $moduleId && isset($_FILES['file'])) {
			for ($i = 0; $i < count($_FILES['file']['error']); $i++) {
				$file = [
					'error' => $_FILES['file']['error'][$i],
					'name' => $_FILES['file']['name'][$i],
					'type' => $_FILES['file']['type'][$i],
					'size' => $_FILES['file']['size'][$i],
					'tmp_name' => $_FILES['file']['tmp_name'][$i],
				];
				
				if (!$file['error']) {
					
					// Find media model
					$model = new Media();
					$model->module_type = $moduleType;
					$model->module_id = $moduleId;
					$model->type = Type::IMAGE;
					$model->status = Status::ENABLED;
					$model->attribute = 'image';
					if ($model->save()) {
						
						// Check size
						if ($file['size'] > $module->allowedMaxSize)
							$result['error'] = Yii::t('media', 'upload_wrong_size', [Yii::$app->formatter->asBytes($module->allowedMaxSize[$model->type])]);
						// Check type
						else if (!in_array($file['type'], $module->allowedTypes[$model->type]))
							$result['error'] = Yii::t('media', 'upload_wrong_type');
						else {
							
							// Get file info
							$pathInfo = pathinfo($file['name']);
							
							// Set extension and size
							$model->ext = strtolower($pathInfo['extension']);
							$model->size = $file['size'];
							
							$fileDir = $model->getFilePath(true);
							$fileDirCache = $model->getFilePath(false);
							
							$fileName = $model->getFile(false);
							$filePath = $fileDir.$fileName;
							$filePathTmp = $filePath.'.tmp';
							
							// Recreate dir
							if ($fs->has($fileDir))
								$fs->deleteDir($fileDir);
							$fs->createDir($fileDir);
							if ($fs->has($fileDirCache))
								$fs->deleteDir($fileDirCache);
							//$fs->createDir($fileDirCache);
							
							if ($fsLocal->has($fileDirCache))
								$fsLocal->deleteDir($fileDirCache);
							$fsLocal->createDir($fileDirCache);
							
							if ($fs->has($filePath))
								$fs->delete($filePath);
							
							if ($fsLocal->has($filePathTmp))
								$fsLocal->delete($filePathTmp);
							
							// If file is image
							if ($model->type == Type::IMAGE) {
								
								// Save tmp file
								$fsLocal->write($filePathTmp, file_get_contents($file['tmp_name']));
								
								// Create image instance
								$img = new Image();
								$img->load($fsLocal->read($filePathTmp));
								$fs->write($filePath, $img->get($module->imageExt));
								
								// Save image original size
								$model->width = $img->getWidth();
								$model->height = $img->getHeight();
								
								// Remove tmp file
								$fsLocal->delete($filePathTmp);
							}
							
							// Save model
							$model->status = Status::ENABLED;
							$model->save();
							
							// Set result
							$result['file-'.$i] = [
								'id' => $model->id,
								'url' => $model->getImageSrc(1000, 1000, Mode::RESIZE_WIDTH),
							];
						}
					}
					else {
						$result['error'] = true;
						$result['message'] = Yii::t('media', 'upload_error');
					}
				}
				else {
					$result['error'] = true;
					$result['message'] = Yii::t('media', 'upload_error');
				}
			}
		}
		else {
			$result['error'] = true;
			$result['message'] = Yii::t('media', 'upload_error');
		}

		Yii::$app->response->format = Response::FORMAT_JSON;
		echo Json::encode($result);
		die;
	}

	/**
	 * Delete file
	 */
	public function actionDelete() {
		$result = [
			'error' => false,
		];

		$mediaHash = Yii::$app->request->post('media_hash');
		if ($mediaHash) {

			// Find media model
			$model = Media::findByHash($mediaHash);
			if ($model) {
				$model->delete();

				// If file is image
				if ($model->type == Type::IMAGE)
					$result['success'] = Yii::t('media', 'delete_image_success');
			}
			else
				$result['error'] = Yii::t('media', 'delete_error');
		}
		else
			$result['error'] = Yii::t('media', 'delete_error');

		Yii::$app->response->format = Response::FORMAT_JSON;
		echo Json::encode($result);
		die;
	}
}