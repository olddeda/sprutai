<?php

namespace common\modules\media;

use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\helpers\FileHelper;
use yii\web\UploadedFile;

class Manager extends Component
{
	/**
	 * Parameter passed when upload file
	 */
	const PARAM_NAME = 'file';
	/**
	 * Path of storage in web
	 * @var string
	 */
	public $storageUrl = '@web/storage';

	/**
	 * Path of storage in filesystem
	 * @var string
	 */
	public $storagePath = '@webroot/storage';

	/**
	 * Temp folder for temporary files
	 * @var string
	 */
	public $tempPath = '@webroot/storage/temp';

	/**
	 * @var string
	 */
	public $attachmentFileTable = '{{%attachment_file}}';

	/**
	 * Instantiated AttachmentFile attributes
	 */
	protected $modelsInstances = [];

	/**
	 * @inheritdoc
	 *
	 * @throws InvalidConfigException
	 */
	public function init() {
		parent::init();
		$this->createDirectory($this->storagePath);
		$this->createDirectory($this->tempPath);
	}

	/**
	 * @return static
	 */
	public static function getInstance() {
		return Yii::$app->mediaManager;
	}

	/**
	 * @return UploadedFile
	 */
	public static function getUploadedFile() {
		return UploadedFile::getInstanceByName(self::PARAM_NAME);
	}

	/**
	 * @return UploadedFile[]
	 */
	public static function getUploadedFiles() {
		return UploadedFile::getInstancesByName(self::PARAM_NAME);
	}

	/**
	 * @return string
	 */
	public function getStorageUrl() {
		return FileHelper::normalizePath(Yii::getAlias($this->storageUrl), '/').'/';
	}

	/**
	 * @return string
	 */
	public function getStoragePath() {
		return FileHelper::normalizePath(Yii::getAlias($this->storagePath)).DIRECTORY_SEPARATOR;
	}

	/**
	 * @return string
	 */
	public function getTempPath() {
		return FileHelper::normalizePath(Yii::getAlias($this->tempPath)).DIRECTORY_SEPARATOR;
	}

	/**
	 * Ensure or create a folder
	 *
	 * @param $path
	 *
	 * @throws InvalidConfigException
	 * @throws \yii\base\Exception
	 */
	public function createDirectory($path) {
		if (!FileHelper::createDirectory($path))
			throw new InvalidConfigException('Directory '.$path.' doesn\'t exist or cannot be created.');
	}

	/**
	 * Add media model
	 * @param $ownerClass
	 * @param $attribute
	 * @param $config
	 *
	 * @return object
	 * @throws InvalidConfigException
	 */
	public function addMediaModel($ownerClass, $attribute, $config) {
		$name = $ownerClass.$attribute;
		return $this->modelsInstances[$name] = Yii::createObject($config);
	}

	/**
	 * Get media model
	 * @param $ownerClass
	 * @param $attribute
	 *
	 * @return null
	 * @throws InvalidConfigException
	 */
	public function getMediaModel($ownerClass, $attribute) {
		$name = $ownerClass.$attribute;
		if (!isset($this->modelsInstances[$name])) {

			//try to create model that attaches AttachBehavior
			Yii::createObject(['class' => $ownerClass]);
			if (!isset($this->modelsInstances[$name])) {
				return null;
			}
		}

		return $this->modelsInstances[$name];
	}
}