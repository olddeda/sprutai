<?php
namespace common\modules\media\controllers;

use common\modules\base\components\Controller;
use common\modules\base\helpers\enum\ModuleType;
use common\modules\base\helpers\enum\Status;
use common\modules\company\models\Company;
use common\modules\content\models\Content;
use common\modules\media\components\Image;
use common\modules\media\helpers\enum\Mode;
use common\modules\media\helpers\enum\Type;
use common\modules\media\models\Media;
use common\modules\media\models\MediaImage;
use common\modules\media\widgets\fileinput\ImageWidget;
use common\modules\rbac\helpers\enum\Role;
use Yii;
use yii\helpers\Json;
use yii\web\Response;

/**
 * Class DefaultController
 * @package common\modules\media\controllers
 */
class DefaultController extends Controller
{
	
	/**
	 * @param $action
	 *
	 * @return bool
	 * @throws \yii\web\BadRequestHttpException
	 */
	public function beforeAction($action) {
		if (in_array($action->id, ['upload', 'upload-slim', 'upload-content-builder', 'upload-content-builder-large', 'upload-content-builder-slider']))
			$this->enableCsrfValidation = false;
		return parent::beforeAction($action);
	}
	
	/**
	 * Statistics
	 */
	public function actionIndex() {
		return $this->render('index', []);
	}

	/**
	 * Upload file
	 */
	public function actionUpload() {
		$result = [
			'error' => false,
		];
		
		$mediaHash = Yii::$app->request->post('media_hash');
		if ($mediaHash && isset($_FILES['file']) && !$_FILES['file']['error']) {

			// Find media model
			$media = Media::findByHash($mediaHash, [Role::EDITOR]);
			
			if ($media) {
				$media->is_main = true;
				$result = $this->_saveUpload($_FILES['file'], $media);
			}
			else
				$result['error'] = Yii::t('media', 'upload_error');
		}
		else
			$result['error'] = Yii::t('media', 'upload_error');

		Yii::$app->response->format = Response::FORMAT_JSON;
		echo Json::encode($result);
		die;
	}
	
	/**
	 * Upload file
	 */
	public function actionUploadSlim() {
		$result = [
			'success' => false,
		];
		
		$slim = Yii::$app->request->post('slim', []);
		
		if (is_array($slim)) {
			foreach ($slim as $s) {
				$info = Json::decode($s);
				
				$field = $info['output']['field'];
				
				if (isset($_FILES[$field]) && !$_FILES[$field]['error'] && $info && isset($info['meta']['media_hash'])) {
					$mediaHash = $info['meta']['media_hash'];
					
					// Find media model
					$media = Media::findByHash($mediaHash, [Role::EDITOR]);
					
					
					if ($media) {
						$media->is_main = true;
						
						$crop = false;
						if (isset($info['actions']['crop'])) {
							$crop = [
								'w' => $info['actions']['crop']['width'],
								'h' => $info['actions']['crop']['height'],
								'x' => $info['actions']['crop']['x'],
								'y' => $info['actions']['crop']['y'],
							];
						}
						$crop = false;
						
						$res = $this->_saveUpload($_FILES[$field], $media, $crop);
						
						$result['success'] = $res['error'] ? false : true;
						$result['message'] = $res['error'] ? $res['error'] : $res['success'];
					}
					else
						$result['error'] = Yii::t('media', 'upload_error');
				}
			}
		}
		
		$result['status']  = $result['success'] ? 'success' : 'failed';
		
		Yii::$app->response->format = Response::FORMAT_JSON;
		echo Json::encode($result);
		die;
	}
	
	/**
	 * Upload files multiple
	 */
	public function actionUploadMultiple() {
		$response = [
			'error' => Yii::t('media', 'upload_error'),
		];
		
		// Collect files
		$files = [];
		if (is_array($_FILES)) {
			foreach ($_FILES as $data) {
				foreach ($data as $key => $row) {
					foreach ($row as $idx => $val)
						$files[$idx][$key] = $val;
				}
			}
 		}
		
 		// Get params
		$classHash = Yii::$app->request->get('class');
		$hash = Yii::$app->request->get('hash');
		$type = Yii::$app->request->get('type');
		$attribute = Yii::$app->request->get('attribute');
		
		$width = Yii::$app->request->get('width');
		$height = Yii::$app->request->get('height');
		$format = Yii::$app->request->get('format');
		
		if ($classHash && $hash && !is_null($type) && $attribute && count($files)) {
			
			// Find model
			$class = Yii::createObject(['class' => base64_decode($classHash)]);
			$model = $class::findByHash($hash);
			if ($model) {
				
				unset($response['error']);
				
				foreach ($files as $file) {
					
					// Prepare media params
					$params = [
						'module_type' => $model->getModuleType(),
						'module_id' => $model->id,
						'type' => $type,
						'attribute' => $attribute,
						'is_main' => false,
						'status' => Status::TEMP,
					];
					
					// Find temp model
					$media = Media::find()->where($params)->one();
					if (is_null($media)) {
						$media = new Media();
						$media->setAttributes($params);
						$media->status = Status::PROCESS;
						$media->save();
					}
					
					// Save file
					$response = $this->_saveUpload($file, $media);
					$response['html'] = ImageWidget::row($media, $width, $height, $format);
				}
			}
		}
		
		
		Yii::$app->response->format = Response::FORMAT_JSON;
		echo Json::encode($response);
		die;
	}
	
	public function actionUploadContentBuilder() {
		$json = [];
		
		$count = Yii::$app->request->get('count');
		$b64str = Yii::$app->request->post('hidimg-'.$count);
		$imgType = Yii::$app->request->post('hidtype-'.$count);
		$customVal = Yii::$app->request->post(('hidcustomval-'.$count));
		
		$hash = null;
		$moduleType = null;
		if ($customVal) {
			$cv = unserialize(base64_decode($customVal));
			$hash = $cv['hash'];
			$moduleType = $cv['module_type'];
		}
		
		if (!is_null($hash) && !is_null($moduleType) && $b64str && $imgType) {
			$model = null;
			if ($moduleType == ModuleType::CONTENT)
				$model = Content::findByHash($hash);
			else if ($moduleType == ModuleType::COMPANY)
				$model = Company::findByHash($hash);
			
			if ($model) {
				$ext = ($imgType == 'png') ? 'png' : 'jpg';
				
				$tmp = '/tmp/cb'.$hash.time().rand(1000, 9999).'.'.$ext;
				
				file_put_contents($tmp, base64_decode($b64str));
				
				$file = [
					'tmp_name' => $tmp,
					'size' => filesize($tmp),
					'name' => $tmp,
					'type' => 'image/'.$imgType,
				];
				
				$media = new MediaImage();
				$media->type = Type::IMAGE;
				$media->module_type = ModuleType::CONTENT;
				$media->module_id = $model->id;
				$media->attribute = 'image';
				$media->save(false);
				
				$result = $this->_saveUpload($file, $media);
				if ($result['success']) {
					$path = $result['url'].'1600x_'.$result['file'];
					
					$json['url'] = $path;
				}
				else {
					$json['error'] = $result['error'];
				}
			}
		}
		else {
			$json['error'] = 'No image';
		}
		
		echo Json::encode($json);
		die;
	}
	
	public function actionUploadContentBuilderLarge() {
		$json = [];
		
		$customVal = Yii::$app->request->post('hidRefId');
		
		$hash = null;
		$moduleType = null;
		if ($customVal) {
			$cv = unserialize(base64_decode($customVal));
			$hash = $cv['hash'];
			$moduleType = $cv['module_type'];
		}
		
		if (!is_null($hash) && !is_null($moduleType) && isset($_FILES['fileImage']) && !$_FILES['fileImage']['error']) {
			
			$model = null;
			if ($moduleType == ModuleType::CONTENT)
				$model = Content::findByHash($hash);
			else if ($moduleType == ModuleType::COMPANY)
				$model = Company::findByHash($hash);
			
			if ($model) {
				$media = new MediaImage();
				$media->type = Type::IMAGE;
				$media->module_type = ModuleType::CONTENT;
				$media->module_id = $model->id;
				$media->attribute = 'image';
				$media->save(false);
				
				$json = [];
				
				$result = $this->_saveUpload($_FILES['fileImage'], $media);
				if ($result['success']) {
					$path = $result['url'].'3000x_'.$result['file'];
					$json['url'] = $path;
				}
				else {
					$json['error'] = $result['error'];
				}
			}
		}
		else {
			$json['error'] = 'No image';
			$json['data'] = [
				'get' => $_GET,
				'post' => $_POST,
				'files' => $_FILES,
			];
		}
		
		echo Json::encode($json);
		die;
	}
	
	public function actionUploadContentBuilderSlider() {
		$hash = Yii::$app->request->post('hidCustomVal');
		if ($hash && isset($_FILES['fileImage']) && !$_FILES['fileImage']['error']) {
			$model = Content::findByHash($hash);
			if ($model) {
				
				$media = new MediaImage();
				$media->type = Type::IMAGE;
				$media->module_type = ModuleType::CONTENT;
				$media->module_id = $model->id;
				$media->attribute = 'image';
				$media->save(false);
				
				$result = $this->_saveUpload($_FILES['fileImage'], $media);
				if ($result['success']) {
					$path = $result['url'].'1600x1600x_'.$result['file'];
					echo '<html><body onload="parent.sliderImageSaved(\''.$path.'\')"></body></html>';
					Yii::$app->end();
				}
				else {
					echo '<html><body onload="alert(\''.$result['error'].'\')"></body></html>';
					Yii::$app->end();
				}
			}
		}
	}
	
	/**
	 * @param $file
	 * @param $model Media
	 *
	 * @return mixed
	 */
	private function _saveUpload($file, $model, $crop = false) {
		$result = [
			'error' => false,
			'success' => false,
		];
		
		/** @var common/modules/media/Module $module */
		$module = Yii::$app->getModule('media');
		
		/** @var $fs \creocoder\flysystem\AwsS3Filesystem $filesystem */
		$fs = $module->fs;
		
		/** @var $fs \creocoder\flysystem\LocalFilesystem $filesystem */
		$fsLocal = Yii::$app->fsLocal;
		
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
			$fileOrignalName = $model->getFileName().'.original.'.$model->ext;
			$filePath = $fileDir.$fileName;
			$filePathOrig = $fileDir.$fileOrignalName;
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
			
			if ($fs->has($filePathTmp))
				$fs->delete($filePathTmp);
			
			if ($fs->has($filePathOrig))
				$fs->delete($filePathOrig);
			
			// If file is image
			if ($model->type == Type::IMAGE) {
			    $data = file_get_contents($file['tmp_name']);
				
				// Create image instance
				$img = new Image();
				$img->load($data);
				
				if (is_array($crop)) {
					$img->cropAndScale([
						'width' => (int)$crop['w'],
						'height' => (int)$crop['h'],
						'cropX' => (int)$crop['x'],
						'cropY' => (int)$crop['y'],
					]);

                    $fs->write($filePathOrig, $data);
					
					$result['original'] = [
						'url' => $model->getFileHttp(true),
						'file' => $model->getFile(),
						'source' => $fileOrignalName,
					];
				}

				$fs->write($filePath, $data);
				
				// Save image original size
				$model->width = $img->getWidth();
				$model->height = $img->getHeight();
				
				// Remove tmp file
				if (file_exists($filePathTmp)) {
                    unlink($filePathTmp);
                }
				
				$result['success'] = Yii::t('media', 'upload_image_success');
			}
			else {
				$fs->write($filePath, file_get_contents($file['tmp_name']));
			}
			
			// Save model
			$model->status = Status::ENABLED;
			$model->save();
			
			// Set result
			$result['url'] = $model->getFileHttp(($model->type != Type::IMAGE));
			$result['file'] = $model->getFile();
 		}
		
		return $result;
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
	}
	
	public function actionModal() {
		$result = [];
		
		$mediaHash = Yii::$app->request->post('media_hash');
		
		// Find media model
		$model = Media::findByHash($mediaHash);
		if ($model) {
			
			$isMainOld = $model->is_main;
			$modelMainOld = Media::find()->andWhere([
				'module_type' => $model->module_type,
				'module_id' => $model->module_id,
				'type' => $model->type,
				'is_main' => true,
			])->one();
			
			if ($model->load(Yii::$app->request->post()) && $model->save()) {
				$result['success'] = Yii::t('media', 'message_update_success');
				
				if ($model->is_main) {
					$isModelMainOldFile = (!is_null($modelMainOld) && $modelMainOld->getFileExists());
					$result['main']['old'] = [
						'src' => ($isModelMainOldFile) ? $modelMainOld->getImageSrc(140, 140, Mode::CROP_CENTER) : false,
						'hash' => (!is_null($modelMainOld)) ? $modelMainOld->hash : false,
					];
					$result['main']['new'] = [
						'src' => $model->getImageSrc(170, 170, Mode::CROP_CENTER),
						'hash' => $model->hash,
					];
				}
			}
			else {
				return $this->renderAjax('modal', [
					'model' => $model,
				]);
			}
		}
		else {
			$result['error'] = Yii::t('media', 'error_not_exists');
		}
		
		Yii::$app->response->format = Response::FORMAT_JSON;
		echo Json::encode($result);
	}
}