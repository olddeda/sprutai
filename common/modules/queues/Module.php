<?php
namespace common\modules\queues;

use yii\base\Module as BaseModule;

/**
 * Class Module
 * @package common\modules\queue
 */
class Module extends BaseModule
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'common\modules\queues\controllers';
    
    /**
     * @inheritdoc
     */
    public $defaultRoute = 'default/index';
	
	/**
	 * @var array
	 */
    public $jobs = [];
	
	/**
	 * @var bool
	 */
	public $canPushAgain = true;
	
	/**
	 * @var bool
	 */
	public $canExecStop = false;
	
	/**
	 * @var bool
	 */
	public $canWorkerStop = false;
}
