<?php
namespace common\modules\mailing;

use Yii;
use yii\base\Application;
use yii\base\BootstrapInterface;
use yii\console\Application as ConsoleApplication;

/**
 * Bootstrap
 * @package common\modules\mailing
 */
class Bootstrap implements BootstrapInterface
{
	/**
	 * Bootstrap method to be called during application bootstrap mailing.
	 *
	 * @param Application $app the application currently running
	 */
	public function bootstrap($app) {

		/** @var Module $module */
		/** @var \yii\db\ActiveRecord $modelName */
		if ($app->hasModule('mailing') && ($module = $app->getModule('mailing')) instanceof Module) {

			// Make sure to register the base folder as alias as well or things like assets won't work anymore
			\Yii::setAlias('@common/modules/mailing', __DIR__);

			if ($app instanceof ConsoleApplication) {
				
				// Add controller namespace path
				$module->controllerNamespace = 'common\modules\mailing\commands';
				
				// Add migration path
				Yii::$app->controllerMap['migrate']['migrationNamespaces'][] = 'common\modules\mailing\migrations';
			}
			else {
			}

			// Add i18n support
			if ($app->has('i18n')) {
				$app->i18n->translations['mailing*'] = [
					'class' => 'yii\i18n\PhpMessageSource',
					'sourceLanguage' => 'en',
					'basePath' => '@common/modules/mailing/messages',
				];
			}
		}
	}
}
