<?php

namespace common\modules\comments;

use Yii;

use yii\base\Module as BaseModule;

/**
 * Class Module
 * @package common\modules\comments
 */
class Module extends BaseModule
{
	/**
	 * @var string module name
	 */
	public static $name = 'comments';

	/**
	 * @var string|null
	 */
	public $userIdentityClass = null;

	/**
	 * @var string comment model class, by default its common\modules\comments\models\Comment::className();
	 * You can override functions (getAuthor, getAvatar, ect) in your own comment model class
	 */
	public $commentModelClass = null;

	/**
	 * @var string the namespace that controller classes are in.
	 * This namespace will be used to load controller classes by prepending it to the controller
	 * class name.
	 */
	public $controllerNamespace = 'common\modules\comments\controllers';
	
	/**
	 * Encrypt entity
	 *
	 * @param $class
	 *
	 * @return string
	 */
	public function encryptEntity($class) {
		return hash('crc32', $class);
	}

}
