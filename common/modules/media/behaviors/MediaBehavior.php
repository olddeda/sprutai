<?php
namespace common\modules\media\behaviors;

use common\modules\base\components\ActiveQuery;
use common\modules\base\components\flysystem\AwsS3Filesystem;
use common\modules\base\components\flysystem\LocalFilesystem;
use common\modules\base\extensions\base\ModuleTrait;
use common\modules\base\helpers\enum\Status;
use common\modules\media\components\Image;
use common\modules\media\helpers\enum\Mode;
use common\modules\media\helpers\enum\Type;
use common\modules\media\models\Media;
use common\modules\media\models\MediaFile;
use common\modules\media\models\MediaFormat;
use common\modules\media\models\MediaImage;
use common\modules\media\widgets\fileapi\widgets\AvatarWidget;
use common\modules\media\widgets\fileapi\widgets\ImageSlimWidget;
use common\modules\media\widgets\fileapi\widgets\ImageWidget;
use common\modules\media\widgets\fileapi\widgets\VideoWidget;
use common\modules\media\widgets\fileinput\ImageWidget as FileInputImageWidget;
use common\modules\media\widgets\show\ImageShowWidget;
use Yii;
use yii\base\Behavior;
use yii\base\InvalidArgumentException;
use yii\db\ActiveRecord;
use yii\helpers\Url;

/**
 * This is the behavior for media
 *
 * @property MediaImage $mediaImage
 */
class MediaBehavior extends Behavior
{
	use ModuleTrait;

    /**
     * @var string
     */
	public $attribute;

    /**
     * @var string
     */
	public $relation;

    /**
     * @var integer
     */
	public $type;
	
	private $_attributes;
	
	private $_mediaImage;
	private $_mediaFile;
	
	static public $_cached = [];

	public function init()
    {
		parent::init();
		
		if (is_null($this->attribute))
			throw new InvalidArgumentException('Empty param "attribute"');
		
		if (is_null($this->type))
			throw new InvalidArgumentException('Empty param "type"');
		
		$this->_attributes[$this->attribute] = $this;
		$this->_attributes[$this->field] = '';
	}
	
	/**
	 * @return array
	 */
	public function events() {
		return [
			ActiveRecord::EVENT_AFTER_FIND => 'afterFind',
		];
	}
	
	/**
	 * Uploader for avatar
	 * @param array $params
	 *
	 * @return string
	 * @throws \Exception
	 */
	public function uploaderAvatar($params = []) {
		if (!isset($params['width']))
			throw new InvalidArgumentException('Empty param "width"');
		
		if (!isset($params['height']))
			throw new InvalidArgumentException('Empty param "height"');
		
		if (!isset($params['format']))
			$params['format'] = Mode::RESIZE;
		
		if (!isset($params['crop']))
			$params['crop'] = true;
		
		return AvatarWidget::widget([
			'model' => $this->owner,
			'mediaType' => $this->type,
			'attribute' => $this->field,
			'crop' => $params['crop'],
			'settings' => [
				'data' => [
					'media_hash' => $this->getMediaImage(true, true)->hash,
				],
				'elements' => [
					'preview' => [
						'width' => $params['width'],
						'height' => $params['height'],
						'format' => MediaFormat::format($params['width'], $params['height'], $params['format']),
					],
				],
			]
		]);
	}
	
	/**
	 * Get image path
	 * @param $width
	 * @param $height
	 *
	 * @return bool
	 */
	public function getAvatar($width, $height) {
		$media = $this->getMediaImage();
		if ($media && $media->fileExists)
			return $media->getImageSrc($width, $height, Mode::CROP_CENTER);
		return false;
	}
	
	/**
	 * Uploader for image
	 * @param array $params
	 *
	 * @return string
	 * @throws \Exception
	 */
	public function uploaderImage($params = []) {
		if (!isset($params['width']))
			throw new InvalidArgumentException('Empty param "width"');
		
		if (!isset($params['height']))
			throw new InvalidArgumentException('Empty param "height"');
		
		if (!isset($params['format']))
			$params['format'] = Mode::RESIZE;
		
		if (!isset($params['crop']))
			$params['crop'] = false;
		
		$mediaImage = $this->getMediaImage(true, true);
		
		$_params = [
			'model' => $this->owner,
			'mediaType' => $this->type,
			'attribute' => $this->field,
			'crop' => $params['crop'],
			'settings' => [
				'data' => [
					'media_hash' => $mediaImage->hash,
				],
				'elements' => [
					'preview' => [
						'width' => $params['width'],
						'height' => $params['height'],
						'format' => $params['format'],
					],
				],
			]
		];
		
		if (isset($params['cropperSettings']))
			$_params['cropperSettings'] = $params['cropperSettings'];
		
		return ImageWidget::widget($_params);
	}
	
	/**
	 * Uploader for image
	 * @param array $params
	 *
	 * @return string
	 * @throws \Exception
	 */
	public function uploaderVideo($params = []) {
		$mediaImage = $this->getMediaFile(true, true);
		
		$_params = [
			'model' => $this->owner,
			'mediaType' => $this->type,
			'attribute' => $this->field,
			'settings' => [
				'data' => [
					'media_hash' => $mediaImage->hash,
				],
			]
		];
		
		return VideoWidget::widget($_params);
	}
	
	/**
	 * Uploader for image
	 * @param array $params
	 *
	 * @return string
	 * @throws \Exception
	 */
	public function uploaderImageSlim($params = []) {
		if (!isset($params['settings']))
			$params['settings'] = [];
		
		if (!isset($params['format']))
			$params['format'] = Mode::RESIZE;
		
		if (!isset($params['crop']))
			$params['crop'] = false;
		
		$mediaImage = $this->getMediaImage(true, true);
		
		$params['settings']['meta']['media_hash'] = $mediaImage->hash;
		
		$_params = [
			'settings' => $params['settings'],
			'mediaType' => $this->type,
			'mediaHash' => $mediaImage->hash,
			'name' => $this->field,
			'attribute' => $this->field,
			'crop' => $params['crop'],
		];
		
		if ($mediaImage->getFileExists()) {
			$src = $mediaImage->getFileHttp(true).$mediaImage->getFile();
			$_params['value'] = $src;
		}
		
		return ImageSlimWidget::widget($_params);
	}
	
	/**
	 * Uploader for multiple images
	 * @param array $params
	 *
	 * @return string
	 * @throws \Exception
	 */
	public function uploaderImages($params = []) {
		if (!isset($params['width']))
			throw new InvalidArgumentException('Empty param "width"');
		
		if (!isset($params['height']))
			throw new InvalidArgumentException('Empty param "height"');
		
		if (!isset($params['format']))
			$params['format'] = Mode::RESIZE;
		
		if (!isset($params['crop']))
			$params['crop'] = false;
		
		$_params = [
			'model' => $this->owner,
			'attribute' => $this->field,
			'mediaType' => $this->type,
			'multiple' => true,
			'width' => $params['width'],
			'height' => $params['height'],
			'format' => $params['format'],
			'fileInputPluginOptions' => [
				'showRemove' => true,
				'showUpload' => true,
				'resizeImages' => true,
				'dropZoneEnabled' => false,
				'uploadUrl' => Url::to([
					'/media/default/upload-multiple',
					'class' => base64_encode(get_class($this->owner)),
					'hash' => $this->owner->hash,
					'type' => $this->type,
					'attribute' => $this->attribute,
					'width' => $params['width'],
					'height' => $params['height'],
					'format' => $params['format'],
				]),
				'uploadAsync' => true,
				'previewSettings' => [
					'image' => [
						'width' => $params['width'].'px',
						'height' => $params['height'].'px',
					],
				],
				'fileActionSettings' => [
					'indicatorNew' => '<i class="glyphicon glyphicon-hourglass"></i>',
					'indicatorSuccess' => '<i class="glyphicon glyphicon-ok"></i>',
					'indicatorError' => '<i class="glyphicon glyphicon-remove"></i>',
					'indicatorLoading' => '<i class="glyphicon glyphicon-time"></i>',
				]
			],
		];
		
		return FileInputImageWidget::widget($_params);
	}
	
	/**
	 * Show image
	 * @param array $params
	 *
	 * @return string
	 * @throws \Exception
	 */
	public function showImage($params = []) {
		
		if (!isset($params['width']))
			throw new InvalidArgumentException('Empty param "width"');
		
		if (!isset($params['height']))
			throw new InvalidArgumentException('Empty param "height"');
		
		return ImageShowWidget::widget([
			'model' => $this->owner,
			'width' => $params['width'],
			'height' => $params['height'],
		]);
	}
	
	/**
	 * Show images
	 * @param array $params
	 *
	 * @return string
	 * @throws \Exception
	 */
	public function showImages($params = []) {
		
		if (!isset($params['width']))
			throw new InvalidArgumentException('Empty param "width"');
		
		if (!isset($params['height']))
			throw new InvalidArgumentException('Empty param "height"');
		
		return ImageShowWidget::widget([
			'model' => $this->owner,
			'width' => $params['width'],
			'height' => $params['height'],
			'multiple' => true,
		]);
	}
	
	/**
	 * Check image exists physical
	 * @return bool
	 */
	public function getFileExists() {
		$media = $this->getMediaImage();
		return ($media && $media->getFileExists());
	}
	
	/**
	 * Get image info
	 * @param bool $useTime
	 *
	 * @return array
	 */
	public function getImageInfo($useTime = true, $original = false) {
		$info = [
			'http' => null,
			'path' => null,
			'file' => null,
            'original' => null,
		];
		
		$media = $this->getMediaImage();
		if ($media && $media->getFileExists()) {
			$info['http'] = $media->getFileHttp($original);
			$info['path'] = $media->getFileUrl($original);
			$info['file'] = $media->getFile($original);
            $info['original'] = $media->getFileOriginal($original);
			if ($useTime) {
                $info['file'] .= '?'.$media->updated_at;
                $info['original'] .= '?'.$media->updated_at;
            }
		}
		return $info;
	}
	
	/**
	 * Get image src
	 * @param $width
	 * @param $height
	 * @param int $mode
	 * @param bool $useTimestamp
	 *
	 * @return bool|string
	 */
	public function getImageSrc($width, $height, $mode = Mode::CROP_CENTER, $useTimestamp = false) {
		$media = $this->getMediaImage();
		if ($media && $media->getFileExists())
			return $media->getImageSrc($width, $height, $mode, false, $useTimestamp);
		if ($placeholder = Yii::$app->getModule('media')->getPlaceholder()) {
			if ($placeholder->getFileExists())
				return $placeholder->getImageSrc($width, $height, $mode, false, $useTimestamp);
		}
		return false;
	}
	
	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getMedia() {
        return $this->owner->hasOne(MediaImage::class, ['module_id' => 'id'])->onCondition([
            MediaImage::tableName().'.module_type' => $this->owner->moduleType,
            MediaImage::tableName().'.attribute' => $this->attribute,
            MediaImage::tableName().'.type' => $this->type,
            MediaImage::tableName().'.is_main' => true,
            MediaImage::tableName().'.status' => 1,
        ])->where([]);
	}
	
	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getMediaLogo() {
		return $this->owner->hasOne(MediaImage::class, ['module_id' => 'id'])->onCondition([
			MediaImage::tableName().'.module_type' => $this->owner->moduleType,
			MediaImage::tableName().'.attribute' => 'logo',
			MediaImage::tableName().'.type' => $this->type,
			MediaImage::tableName().'.is_main' => true,
			MediaImage::tableName().'.status' => 1,
		])->where([]);
	}
	
	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getMediaAvatar() {
	    $alias = 'ma';
		return $this->owner->hasOne(MediaImage::class, ['module_id' => 'id'])->onCondition([
			$alias.'.module_type' => $this->owner->moduleType,
			$alias.'.attribute' => 'avatar',
            $alias.'.type' => $this->type,
			$alias.'.is_main' => true,
			$alias.'.status' => 1,
		])->andOnCondition([
			'<>', $alias.'.status', Status::DELETED,
		])->where([])->alias($alias);
	}
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMediaAvatarComments() {
        $alias = 'mac';
        return $this->owner->hasOne(MediaImage::class, ['module_id' => 'id'])->onCondition([
            $alias.'.module_type' => $this->owner->moduleType,
            $alias.'.attribute' => 'avatar',
            $alias.'.type' => $this->type,
            $alias.'.is_main' => true,
            $alias.'.status' => 1,
        ])->andOnCondition([
            '<>', $alias.'.status', Status::DELETED,
        ])->where([])->alias($alias);
    }
	
	/**
	 * Get media
	 * @return array|MediaImage|null
	 */
	public function getMediaImage($own = false, $create = false) {
		if (!$this->_mediaImage || $own) {
		    if ($this->relation && $this->owner->hasMethod('get'.ucfirst($this->relation))) {
		        $relation = $this->relation;
		        $this->_mediaImage = $this->owner->$relation;
            }
		    else if ($this->owner->isRelationPopulated('media')) {
		        if ($this->owner->type == 6) {
		            $this->_mediaImage = $this->owner->mediaLogo;
                }
		        else {
                    $this->_mediaImage = $this->owner->media;
                }
            }
            else if ($this->owner->isRelationPopulated('mediaLogo')) {
                $this->_mediaImage = $this->owner->mediaLogo;
            }
			else if (!$own && $this->owner->hasMethod('getMedia')) {
				if ($this->attribute == 'image')
					$this->_mediaImage = $this->owner->media;
				else if ($this->attribute == 'logo')
					$this->_mediaImage = $this->owner->mediaLogo;
				else if ($this->attribute == 'avatar') {
                    $this->_mediaImage = $this->owner->mediaAvatar;
                }
				else
					$this->_mediaImage = $this->getMedia()->one();
			}
			else {
				$query = $this->_getQuery(['and', ['not in', 'status', [Status::DELETED]], ['is_main' => true]]);
				//if ($own)
				//	$query->andWhere(['created_by' => Yii::$app->user->id]);
				
				$this->_mediaImage = $query->one();
			}
			
			if (!$this->_mediaImage && $create) {
				$this->_mediaImage = new MediaImage();
				$this->_mediaImage->module_type = $this->owner->moduleType;
				$this->_mediaImage->module_id = $this->owner->id;
				$this->_mediaImage->type = $this->type;
				$this->_mediaImage->attribute = $this->attribute;
				$this->_mediaImage->status = Status::TEMP;
				$this->_mediaImage->is_main = true;
				$this->_mediaImage->save();
			}
		}
		return $this->_mediaImage;
	}
	
	/**
	 * Get media
	 * @return array|MediaFile|null
	 */
	public function getMediaFile($own = false, $create = false) {
		if (!$this->_mediaFile || $own) {
			if (!$own && $this->owner->hasMethod('getMedia')) {
				$this->_mediaFile = $this->getMedia()->one();
			}
			else {
				$query = $this->_getQuery(['and', ['not in', 'status', [Status::DELETED]], ['is_main' => true]]);
				$this->_mediaFile = $query->one();
			}
			
			if (!$this->_mediaFile && $create) {
				$this->_mediaFile = new MediaFile();
				$this->_mediaFile->module_type = $this->owner->moduleType;
				$this->_mediaFile->module_id = $this->owner->id;
				$this->_mediaFile->type = $this->type;
				$this->_mediaFile->attribute = $this->attribute;
				$this->_mediaFile->status = Status::TEMP;
				$this->_mediaFile->is_main = true;
				$this->_mediaFile->save();
			}
		}
		return $this->_mediaFile;
	}
	
	/**
	 * Get images
	 * @return array|ActiveRecord[]
	 */
	public function getImages($orderBy = null) {
		
		// Set default order
		if (!$orderBy) {
			$orderBy = [
				'is_main' => SORT_DESC,
				'sequence' => SORT_ASC,
				'id' => SORT_ASC
			];
		}
		
		// Create query
		$query = $this->_getQuery(['and', ['not in', 'status', [Status::TEMP, Status::DELETED]], ['is_main' => false]]);
		$query->orderBy($orderBy);
		
		// Find models
		return $query->all();
	}
	
	/**
	 * Set media
	 * @param MediaImage $model
	 */
	public function setMediaImage(MediaImage $model) {
		$this->_mediaImage = $model;
	}
	
	/**
	 * Check has image
	 * @return bool
	 */
	public function hasImage() {
		$mediaImage = $this->getMediaImage();
		return ($mediaImage && $mediaImage->status == Status::ENABLED) ? true : false;
	}
	
	/**
	 * Check has image
	 * @return bool
	 */
	public function hasImages() {
		return $this->_getQuery(['and', ['not in', 'status', [Status::TEMP, Status::DELETED]]])->count();
	}
	
	/**
	 * @return string
	 */
	public function getField() {
		return 'media_'.$this->attribute;
	}

    /**
     * @param string $path
     * @param string $pathOriginal
     *
     * @return MediaImage
     */
	public function saveFile($path, $pathOriginal) {
		
		/** @var common/modules/media/Module $module */
		$module = Yii::$app->getModule('media');
		
		/** @var $fs AwsS3Filesystem $filesystem */
		$fs = $module->fs;
		
		/** @var $fs LocalFilesystem $filesystem */
		$fsLocal = Yii::$app->fsLocal;
		
		if (file_exists($path)) {
			$pathInfo = pathinfo($path);
			
			/** @var MediaImage $model */
			$model = $this->getMediaImage(true, true);
			$model->ext = $pathInfo['extension'];
			$model->size = filesize($path);
			
			$fileDir = $model->getFilePath(true);
			$fileDirCache = $model->getFilePath(false);
			$filePath = $fileDir.$model->getFile(false);
			$filePathOriginal = $fileDir.$model->getFileOriginal(false);
			
			// Recreate dir
			if ($fs->has($fileDir))
				$fs->deleteDir($fileDir);
			$fs->createDir($fileDir);
			
			if ($fs->has($fileDirCache))
				$fs->deleteDir($fileDirCache);
			
			if ($fsLocal->has($fileDirCache))
				$fsLocal->deleteDir($fileDirCache);
			$fsLocal->createDir($fileDirCache);
			
			// If file is image
			$fileData = file_get_contents($path);
			if ($model->type == Type::IMAGE) {
				$fs->write($filePath, $fileData);
				
				$img = new Image();
				$img->load($fileData);
				
				$model->width = $img->getWidth();
				$model->height = $img->getHeight();
			}

			if ($pathOriginal) {
                $fileData = file_get_contents($pathOriginal);
                $fs->write($filePathOriginal, $fileData);
            }
			
			// Save model
			$model->status = Status::ENABLED;
			$model->save();
		}

		return $model;
	}
	
	/**
	 * Check exists file
	 * @param object $fileName
	 * @return bool
	 */
	public static function fileExists($fileName) {
		
		/** @var \creocoder\flysystem\Filesystem $fs */
		$fs = Yii::$app->fs;
		
		if ($fs instanceof \creocoder\flysystem\AwsS3Filesystem)
			return true;
		
		return file_exists($fileName);
	}
	
	/**
	 * Make dir reqursive
	 *
	 * @return
	 * @param object $dir
	 * @param object $mode[optional]
	 */
	public static function makeDirectory($dir, $mode = 0777) {
		$__oldumask = umask(0);
		$currpath = '';
		
		$webRoot = realpath(dirname(__FILE__).'/../../..');
		$serverRoot = explode(DIRECTORY_SEPARATOR, $webRoot.DIRECTORY_SEPARATOR.'media');
		
		foreach (explode(DIRECTORY_SEPARATOR, $dir) as $part) {
			$currpath .= $part.DIRECTORY_SEPARATOR;
			if (in_array($part, $serverRoot))
				continue;
			if ($part != '' && !is_dir($currpath)) {
				if (is_file($currpath))
					return null;
				if (!mkdir($currpath, $mode))
					return null;
			}
		}
		umask($__oldumask);
		return true;
	}
	
	/**
	 * Empty dir
	 * @param null $path
	 * @param bool|false $all
	 */
	public static function emptyDirectory($path = null, $all = false) {
		$path = pathinfo($path);
		if (!empty($path)) {
			$files = scandir($path['dirname']);
			$pattern = '/_'.$path['basename'].'$/';
			foreach ($files as $k => $v) {
				if (preg_match($pattern, $v) || $all) {
					unlink($path['dirname'].'/'.$v);
				}
			}
		}
	}
	
	/**
	 * Empty dir cache
	 * @param $dirname
	 * @param string $baseName
	 * @param bool|false $showLog
	 *
	 * @return bool
	 */
	public static function emptyDirectoryCache($dirname, $baseName) {
		if (is_dir($dirname))
			$dir_handle = opendir($dirname);
		
		if (!$dir_handle)
			return false;
		
		while ($file = readdir($dir_handle)) {
			if ($file != "." && $file != "..") {
				$fullPath = $dirname.DIRECTORY_SEPARATOR.$file;
				if (!is_dir($fullPath)) {
					if ($file == $baseName) {
						$fi = new FilesystemIterator($dirname, FilesystemIterator::SKIP_DOTS);
						if (iterator_count($fi) > 1) {
							
							$isAddCount = false;
							foreach ($fi as $fileinfo) {
								if (preg_match('/_'.$baseName.'$/', $fileinfo->getFilename())) {
									$isAddCount = true;
								}
							}
							if ($isAddCount)
								self::$count++;
							
							self::emptyDirectory($fullPath);
						}
					}
				}
				else
					self::emptyDirectoryCache($fullPath, $baseName);
			}
		}
		
		closedir($dir_handle);
		return true;
	}
	
	/**
	 * Private get media query
	 * @param array $additionWhere
	 *
	 * @return ActiveQuery
	 */
	private function _getQuery($additionWhere = []) {
		$params = [
			'module_type' => $this->owner->moduleType,
			'module_id' => $this->owner->id,
			'attribute' => $this->attribute,
			'type' => $this->type,
		];
		
		$query = Media::find()->where($params);
		
		if (is_array($additionWhere))
			$query->andWhere($additionWhere);
		
		return $query;
	}
	
	/**
	 * @inheritdoc
	 */
	public function canGetProperty($name, $checkVars = true) {
		return array_key_exists($name, $this->_attributes) ? true : parent::canGetProperty($name, $checkVars);
	}
	
	/**
	 * @inheritdoc
	 */
	public function canSetProperty($name, $checkVars = true) {
		return array_key_exists($name, $this->_attributes) ? true : parent::canSetProperty($name, $checkVars = true);
	}
	
	/**
	 * @inheritdoc
	 */
	public function __get($name) {
		if (isset($this->_attributes[$name]))
			return $this->_attributes[$name];
		return parent::__get($name);
	}
	
	/**
	 * @inheritdoc
	 */
	public function __set($name, $value){
		if (isset($this->_attributes[$name]))
			$this->_attributes[$name] = $value;
		else
			parent::__set($name, $value);
	}
	
	/**
	 * @inheritdoc
	 */
	public function afterFind() {}
}