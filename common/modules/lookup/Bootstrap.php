<?php
namespace common\modules\lookup;

use Yii;
use yii\base\Application;
use yii\base\BootstrapInterface;
use yii\console\Application as ConsoleApplication;

/**
 * Bootstrap
 * @package common\modules\lookup
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
		/** @var \yii\db\ActiveRecord $modelName */
		if ($app->hasModule('lookup') && ($module = $app->getModule('lookup')) instanceof Module) {

			// Make sure to register the base folder as alias as well or things like assets won't work anymore
			\Yii::setAlias('@app/modules/lookup', __DIR__);

			if ($app instanceof ConsoleApplication) {
				
				// Add controller namespace path
				$module->controllerNamespace = 'common\modules\lookup\commands';
				
				// Add migration path
				Yii::$app->controllerMap['migrate']['migrationNamespaces'][] = 'common\modules\lookup\migrations';
			}
			else {
			}

			// Add i18n support
			if ($app->has('i18n')) {
				$app->i18n->translations['lookup*'] = [
					'class' => 'yii\i18n\PhpMessageSource',
					'sourceLanguage' => 'en',
					'basePath' => '@app/modules/lookup/messages',
				];
			}
		}
	}
}
