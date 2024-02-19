<?php
namespace common\modules\company;

use Yii;
use yii\base\Application;
use yii\base\BootstrapInterface;
use yii\console\Application as ConsoleApplication;
use yii\web\GroupUrlRule;

/**
 * Bootstrap
 * @package common\modules\company
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
		if ($app->hasModule('company') && ($module = $app->getModule('company')) instanceof Module) {

			// Make sure to register the base folder as alias as well or things like assets won't work anymore
			\Yii::setAlias('@app/modules/company', __DIR__);

			if ($app instanceof ConsoleApplication) {
				
				// Add controller namespace path
				$module->controllerNamespace = 'common\modules\company\commands';
				
				// Add migration path
				Yii::$app->controllerMap['migrate']['migrationNamespaces'][] = 'common\modules\company\migrations';
			}
			else {
				
				// Set rules
				if ($module->urlRules && count($module->urlRules)) {
					$configUrlRule = [
						'prefix' => 'company',
						'rules' => $module->urlRules,
					];
					$app->urlManager->addRules([new GroupUrlRule($configUrlRule)], false);
				}
			}

			// Add i18n support
			if ($app->has('i18n')) {
				$app->i18n->translations['company*'] = [
					'class' => 'yii\i18n\PhpMessageSource',
					'sourceLanguage' => 'en',
					'basePath' => '@app/modules/company/messages',
				];
			}
		}
	}
}
