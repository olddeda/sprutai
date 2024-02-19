<?php
namespace common\modules\notification;

use common\modules\base\components\Debug;
use Yii;
use yii\base\BootstrapInterface;
use yii\console\Application as ConsoleApplication;

/**
 * Bootstrap class registers translations and needed application components.
 */
class Bootstrap implements BootstrapInterface
{
    /**
	 * @inheritdoc
	 */
    public function bootstrap($app) {

		/** @var \common\modules\notification\Module $module */
		/** @var \yii\db\ActiveRecord $modelName */
		if ($app->hasModule('notification') && ($module = $app->getModule('notification')) instanceof Module) {

			// Make sure to register the base folder as alias as well or things like assets won't work anymore
			\Yii::setAlias('@common/modules/notification', __DIR__);

			if ($app instanceof ConsoleApplication) {
				$module->controllerNamespace = 'common\modules\notification\commands';
				
				// Add migration path
				Yii::$app->controllerMap['migrate']['migrationNamespaces'][] = 'common\modules\notification\migrations';
			}
			else {
			}

			// Add i18n support
			if ($app->has('i18n')) {
				$app->i18n->translations['notification*'] = [
					'class' => 'yii\i18n\PhpMessageSource',
					'sourceLanguage' => 'en',
					'basePath' => '@common/modules/notification/messages',
				];
			}
			
			foreach ($module->providers as $providerName => $provider) {
				if (empty($provider['events']))
					continue;
				$module->attachEvents($providerName, $provider);
			}

		}
    }
}
