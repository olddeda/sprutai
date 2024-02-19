<?php
namespace client;

use Yii;
use yii\base\BootstrapInterface;
use yii\base\Module as BaseModule;

/**
 * Client module definition class
 */
class Module extends BaseModule implements BootstrapInterface
{
	/**
	 * {@inheritdoc}
	 */
	public $id = 'client';
	
	/**
	 * {@inheritdoc}
	 */
	public function bootstrap($app) {
		if ($app instanceof \yii\console\Application) {
			$this->controllerNamespace = 'client\commands';
		}
	}
}
