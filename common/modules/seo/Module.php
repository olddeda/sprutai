<?php
namespace common\modules\seo;

use Yii;
use yii\base\Module as BaseModule;

/**
 * Class Module
 * @package comment\modules\seo
 */
class Module extends BaseModule
{
	/**
	 * @var string name of the component to use for database access
	 */
	public $db = 'db';
	
	/**
	 * @var string the namespace that controller classes are in.
	 * This namespace will be used to load controller classes by prepending it to the controller
	 * class name.
	 */
    public $controllerNamespace = 'common\modules\seo\controllers';

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
	 * @return \yii\db\Connection the database connection.
	 */
	public function getDb() {
		return Yii::$app->{$this->db};
	}
}
