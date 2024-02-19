<?php
namespace common\modules\hub;

use Yii;
use yii\base\Application;
use yii\base\BootstrapInterface;
use yii\console\Application as ConsoleApplication;

/**
 * Bootstrap
 * @package common\modules\hub
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
		if ($app->hasModule('hub') && ($module = $app->getModule('hub')) instanceof Module) {
			if ($app instanceof ConsoleApplication) {
				$module->controllerNamespace = 'common\modules\hub\commands';

				Yii::$app->controllerMap['migrate']['migrationNamespaces'][] = 'common\modules\hub\migrations';
			}

			// Add i18n support
			if ($app->has('i18n')) {
				$app->i18n->translations['hub*'] = [
					'class' => 'yii\i18n\PhpMessageSource',
					'sourceLanguage' => 'en',
					'basePath' => '@common/modules/hub/messages',
				];
			}
		}
	}
}
