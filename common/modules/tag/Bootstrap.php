<?php
namespace common\modules\tag;

use Yii;
use yii\base\Application;
use yii\base\BootstrapInterface;
use yii\console\Application as ConsoleApplication;

/**
 * Bootstrap
 * @package common\modules\tag
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
		if ($app->hasModule('tag') && ($module = $app->getModule('tag')) instanceof Module) {

			// Make sure to register the base folder as alias as well or things like assets won't work anymore
			\Yii::setAlias('@common/modules/tag', __DIR__);

			if ($app instanceof ConsoleApplication) {
				
				// Add controller namespace path
				$module->controllerNamespace = 'common\modules\tag\commands';
				
				// Add migration path
				Yii::$app->controllerMap['migrate']['migrationNamespaces'][] = 'common\modules\tag\migrations';
			}
			else {
			}

			// Add i18n support
			if ($app->has('i18n')) {
				$app->i18n->translations['tag*'] = [
					'class' => 'yii\i18n\PhpMessageSource',
					'sourceLanguage' => 'en',
					'basePath' => '@common/modules/tag/messages',
				];
			}
		}
	}
}
