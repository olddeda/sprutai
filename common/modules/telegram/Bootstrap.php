<?php
namespace common\modules\telegram;

use Yii;
use yii\base\BootstrapInterface;

/**
 * Bootstrap class registers translations and needed application components.
 */
class Bootstrap implements BootstrapInterface
{
    /**
	 * @inheritdoc
	 */
    public function bootstrap($app) {

		/** @var Module $module */
		/** @var \yii\db\ActiveRecord $modelName */
		if ($app->hasModule('telegram') && ($module = $app->getModule('telegram')) instanceof Module) {

			// Make sure to register the base folder as alias as well or things like assets won't work anymore
			\Yii::setAlias('@common/modules/telegram', __DIR__);

			if ($app instanceof \yii\console\Application) {
				$module->controllerNamespace = 'common\modules\telegram\commands';
				
				// Add migration path
				Yii::$app->controllerMap['migrate']['migrationNamespaces'][] = 'common\modules\telegram\migrations';
			}

			// Add i18n support
			if ($app->has('i18n')) {
				$app->i18n->translations['telegram*'] = [
					'class' => 'yii\i18n\PhpMessageSource',
					'sourceLanguage' => Yii::$app->sourceLanguage,
					'basePath' => '@common/modules/telegram/messages',
				];
			}
		}
    }
}
