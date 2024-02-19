<?php
namespace common\modules\queues;

use Yii;
use yii\base\Application;
use yii\base\BootstrapInterface;
use yii\console\Application as ConsoleApplication;
use yii\web\GroupUrlRule;

/**
 * Bootstrap
 * @package common\modules\content
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
		if ($app->hasModule('queues') && ($module = $app->getModule('queues')) instanceof Module) {
			
			\Yii::setAlias('@common/modules/queues', __DIR__);

			if ($app instanceof ConsoleApplication) {
				$module->controllerNamespace = 'common\modules\queues\commands';
				
				// Add migration path
				Yii::$app->controllerMap['migrate']['migrationNamespaces'][] = 'common\modules\queues\migrations';
			}
			else {
				
				$app->urlManager->addRules([[
					'class' => GroupUrlRule::class,
					'prefix' => $module->id,
					'rules' => [
						'' => 'default/index',
						'jobs' => 'job/index',
						'job/<id:\d+>/<action\w+>' => 'job/view-<action>',
						'workers' => 'worker/index',
						'<controller:\w+>/<id:\d+>' => '<controller>/view',
						'<controller:\w+>/<action\w+>/<id:\d+>' => '<controller>/<action>',
						'<controller:\w+>/<action\w+>' => '<controller>/<action>',
					],
				]], false);
				
			}

			// Add i18n support
			if ($app->has('i18n')) {
				$app->i18n->translations['queues*'] = [
					'class' => 'yii\i18n\PhpMessageSource',
					'sourceLanguage' => 'en',
					'basePath' => '@common/modules/queues/messages',
				];
			}
		}
	}
}
