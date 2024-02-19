<?php
namespace common\modules\backup;

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
		if ($app->hasModule('base') && ($module = $app->getModule('backup')) instanceof Module) {

			// Make sure to register the backup folder as alias as well or things like assets won't work anymore
			\Yii::setAlias('@common/modules/backup', __DIR__);

			if ($app instanceof \yii\console\Application) {
				$module->controllerNamespace = 'common\modules\backup\commands';
				
				// Add migration path
				Yii::$app->controllerMap['migrate']['migrationNamespaces'][] = 'common\modules\backup\migrations';
			}

			// Add i18n support
			if ($app->has('i18n')) {
				$app->i18n->translations['backup*'] = [
					'class' => 'yii\i18n\PhpMessageSource',
					'sourceLanguage' => 'en',
					'basePath' => '@common/modules/backup/messages',
				];
			}
		}
    }
}
