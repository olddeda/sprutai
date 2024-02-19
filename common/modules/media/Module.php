<?php
namespace common\modules\media;

use Yii;
use yii\base\Module as BaseModule;
use yii\base\InvalidConfigException;

use common\modules\media\helpers\enum\Type;
use common\modules\media\models\MediaPlaceholder;

/**
 * Class Module
 * @package common\modules\media
 *
 * @property \creocoder\flysystem\LocalFilesystem $fs
 */
class Module extends BaseModule
{
	/**
	 * @var string module name
	 */
	public static $name = 'media';

	/**
	 * @var string the namespace that controller classes are in.
	 * This namespace will be used to load controller classes by prepending it to the controller
	 * class name.
	 */
	public $controllerNamespace = 'common\modules\media\controllers';

	/**
	 * @var string the rules crop
	 */
	public $rulesCrop = 'static/<path:.+>.<ext:\w+|png|jpg>';
	
	/**
	 * @var array The rules to be used in URL management.
	 */
	public $urlRules = [
		'imperavi/upload/<module_type:\d+>/<module_id:\d+>' 	=> 'imperavi/upload',
		'imperavi/<module_type:\d+>' 							=> 'imperavi/index',
	];

    /**
     * @var string host name
     */
    public $hostName = 'sprut.ai';

    /**
     * @var string host scheme
     */
    public $hostScheme = 'https';

	/**
	 * @var string for name of filesystem
	 */
	public $fsAdapter = 'fs';
	
	/**
	 * @var string fs root path
	 */
	public $fsRootPath = 'media';
	
	/**
	 * @var string fs original path
	 */
	public $fsOriginalPath = 'original';
	
	/**
	 * @var string fs cache path
	 */
	public $fsCachePath = 'cache';

	/**
	 * @var array allowed media types
	 */
	public $allowedTypes = [
		Type::IMAGE => [
			'image/jpeg',
			'image/jpg',
			'image/png',
			'image/gif',
		],
		Type::VIDEO => [
			'video/mp4'
		]
	];

	/**
	 * @var array allowed media max sizes
	 */
	public $allowedMaxSize = [
		Type::IMAGE => 20000000,//20971520, // 20 mb
		Type::FILE => 52428800, // 50 mb
	];

    /**
     * @var array allowed image resolution
     */
	public $allowedImageResolution = [
	    'min' => 300,
        'max' => 5000,
    ];

	/**
	 * @var string default image ext
	 */
	public $imageExt = 'jpg';
	
	/** @var string path to placeholder image */
	public $placeholderPath;

	/**
	 * Initializes the module.
	 *
	 * This method is called after the module is created and initialized with property values
	 * given in configuration. The default implementation will initialize [[controllerNamespace]]
	 * if it is not set.
	 *
	 * If you override this method, please make sure you call the parent implementation.
	 */
	public function init() {
		parent::init();
	}

	/**
	 * @return null|static
	 */
	public static function module() {
		return static::getInstance();
	}

	/**
	 * Get filesystem object
	 * @return null|object
	 * @throws \yii\base\InvalidConfigException
	 */
	public function getFs() {
		if (!Yii::$app->has($this->fsAdapter))
			throw new InvalidConfigException('filesystem adapter '.$this->fsAdapter.' not found');
		return Yii::$app->get($this->fsAdapter);
	}
	
	/**
	 * Get placeholder
	 * @return PlaceHolder|null
	 */
	public function getPlaceholder() {
		if ($this->placeholderPath) {
			return new MediaPlaceholder(['path' => $this->placeholderPath]);
		}
		else
			return null;
	}
}
