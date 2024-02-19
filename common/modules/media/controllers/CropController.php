<?php

namespace common\modules\media\controllers;
;
use Yii;
use yii\web\HttpException;
use yii\base\InvalidArgumentException;

use creocoder\flysystem\AwsS3Filesystem;
use creocoder\flysystem\LocalFilesystem;

use common\modules\base\components\Controller;
use common\modules\base\components\Debug;

use common\modules\media\Module;
use common\modules\media\models\MediaFormat;
use common\modules\media\helpers\enum\Mode;
use common\modules\media\components\Image;

/**
 * Class DefaultController
 * @package common\modules\media\controllers
 */
class CropController extends Controller
{
	/**
	 * Behaviors
	 * @return array
	 */
	public function behaviors() {
		return [];
	}

    /**
     * Crop file
     *
     * @param string $path
     * @param string $ext
     *
     * @throws HttpException
     */
	public function actionIndex($path, $ext) {
		
		/** @var AwsS3Filesystem $filesystem */
		$filesystem = Module::module()->fs;

		/** @var LocalFilesystem $filesystemLocal */
		$filesystemLocal = Yii::$app->fsLocal;

		// Get path info
		$request = explode('?', $_SERVER['REQUEST_URI']);
		$info = pathinfo($request[0]);
		$infoDir = str_replace('/static', '', $info['dirname']);
		$infoDir = str_replace('/client', '', $infoDir);

		// Get format
        $basename = $info['basename'];
        $format = null;
        if (strpos($info['basename'], 'x') !== false) {
            $formatData = explode('_', $info['basename']);
            $basename = end($formatData);
            $format = (count($formatData) > 1 && strpos($formatData[0], 'x') !== false) ? MediaFormat::get($formatData[0]) : null;
        }

		// Get paths
		$requestFilePath = $infoDir.'/'.$info['basename'];
		$originalFilePath = str_replace($this->module->fsCachePath, $this->module->fsOriginalPath, $infoDir.'/'.$basename);
		
		// Check original file path
		if (!$filesystem->has($originalFilePath)) {
			
			$placeholderFilePath = '/media/original/placeholder'.Yii::$app->request->baseUrl;
			$placeholderFilePath .= '/'.$this->module->placeholder->attribute.'.'.$this->module->placeholder->ext;
			$originalFilePath = $placeholderFilePath;
		}
		
		// Create image instance
		$fileData = $filesystem->read($originalFilePath);
		$img = new Image();
		$img->load($fileData);

		// If file not exists
		if (!$filesystemLocal->has($requestFilePath)) {
		    if ($format) {
                switch ($format->mode) {
                    case Mode::RESIZE:
                        $img->resize($format->width, $format->height);
                        break;
                    case Mode::CROP_TOP:
                        $img->cropAndScale([
                            'width' => $format->width,
                            'height' => $format->height,
                            'cropRatio' => 1,
                            'simple' => true,
                            'centerX' => true,
                            'aspect' => true,
                        ]);
                        break;
                    case Mode::CROP_CENTER:
                        $img->cropAndScale([
                            'width' => $format->width,
                            'height' => $format->height,
                            'cropRatio' => 1,
                            'simple' => true,
                            'centerX' => true,
                            'centerY' => true,
                            'aspect' => true,
                        ]);
                        break;
                    case Mode::RESIZE_WIDTH:
                        $img->resize($format->width, $format->height);
                        break;
                    default:
                        throw new InvalidArgumentException('Unknown crop mode');
                        break;
                }
            }

			// Save image
			$filesystemLocal->write($requestFilePath, $img->get($ext));
		}
		
		
		// Show image
		//if ($filesystem instanceof AwsS3Filesystem) {
		//	return $this->redirect($filesystem->url.$requestFilePath, 301);
		//}
		//else {

        $fileInfo = $filesystemLocal->getMetadata($requestFilePath);

        Yii::$app->response->headers->set('Last-Modified', gmdate('D, d M Y H:i:s', $fileInfo['timestamp']) . ' GMT');
        Yii::$app->response->headers->set('Cache-Control', 'public, max-age=0');

        $fileData = $filesystemLocal->read($requestFilePath);
		$img = new Image();
		$img->load($fileData);
		$img->show($ext);

		exit(200);
	}
}