<?php
namespace common\modules\favorite;

use Yii;
use yii\base\Application;
use yii\base\BootstrapInterface;
use yii\console\Application as ConsoleApplication;

/**
 * Bootstrap
 * @package common\modules\favorite
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
		if ($app->hasModule('favorite') && ($module = $app->getModule('favorite')) instanceof Module) {
			if ($app instanceof ConsoleApplication) {
				$module->controllerNamespace = 'common\modules\favorite\commands';

				Yii::$app->controllerMap['migrate']['migrationNamespaces'][] = 'common\modules\favorite\migrations';
			}

			// Add i18n support
			if ($app->has('i18n')) {
				$app->i18n->translations['favorite*'] = [
					'class' => 'yii\i18n\PhpMessageSource',
					'sourceLanguage' => 'en',
					'basePath' => '@common/modules/favorite/messages',
				];
			}
		}
	}
}
