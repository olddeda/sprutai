<?php
namespace common\modules\achievement;

use Yii;
use yii\base\Application;
use yii\base\BootstrapInterface;
use yii\console\Application as ConsoleApplication;

/**
 * Bootstrap
 * @package common\modules\achievement
 */
class Bootstrap implements BootstrapInterface
{
	/**
	 * Bootstrap method to be called during application bootstrap stage.
	 *
	 * @param Application $app the application currently running
	 */
	public function bootstrap($app) {

		/** @var Module $module */
		if ($app->hasModule('achievement') && ($module = $app->getModule('achievement')) instanceof Module) {
			if ($app instanceof ConsoleApplication) {
				$module->controllerNamespace = 'common\modules\achievement\commands';

				Yii::$app->controllerMap['migrate']['migrationNamespaces'][] = 'common\modules\achievement\migrations';
			}

			// Add i18n support
			if ($app->has('i18n')) {
				$app->i18n->translations['achievement*'] = [
					'class' => 'yii\i18n\PhpMessageSource',
					'sourceLanguage' => 'en',
					'basePath' => '@app/modules/achievement/messages',
				];
			}
		}
	}
}
