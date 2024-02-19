<?php
namespace common\modules\plugin;

use Yii;
use yii\base\Application;
use yii\base\BootstrapInterface;
use yii\console\Application as ConsoleApplication;
use yii\web\GroupUrlRule;

/**
 * Bootstrap
 * @package common\modules\plugin
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
		if ($app->hasModule('plugin') && ($module = $app->getModule('plugin')) instanceof Module) {

			// Make sure to register the base folder as alias as well or things like assets won't work anymore
			\Yii::setAlias('@common/modules/plugin', __DIR__);

			if ($app instanceof ConsoleApplication) {
				$module->controllerNamespace = 'common\modules\plugin\commands';
				
				// Add migration path
				Yii::$app->controllerMap['migrate']['migrationNamespaces'][] = 'common\modules\plugin\migrations';
			}
			else {
				
				if ($module->urlRules && count($module->urlRules)) {
					$configUrlRule = [
						'prefix' => 'plugin',
						'rules' => $module->urlRules,
					];
					$app->urlManager->addRules([new GroupUrlRule($configUrlRule)], false);
				}
			}

			// Add i18n support
			if ($app->has('i18n')) {
				$app->i18n->translations['plugin*'] = [
					'class' => 'yii\i18n\PhpMessageSource',
					'sourceLanguage' => 'en',
					'basePath' => '@common/modules/plugin/messages',
				];
			}
		}
	}
}
