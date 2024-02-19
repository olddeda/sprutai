<?php
namespace common\modules\banner;

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
		if ($app->hasModule('banner') && ($module = $app->getModule('banner')) instanceof Module) {

			// Make sure to register the base folder as alias as well or things like assets won't work anymore
			\Yii::setAlias('@common/modules/banner', __DIR__);

			if ($app instanceof \yii\console\Application) {
				$module->controllerNamespace = 'common\modules\banner\commands';
				
				// Add migration path
				Yii::$app->controllerMap['migrate']['migrationNamespaces'][] = 'common\modules\banner\migrations';
			}

			// Add i18n support
			if ($app->has('i18n')) {
				$app->i18n->translations['banner*'] = [
					'class' => 'yii\i18n\PhpMessageSource',
					'sourceLanguage' => Yii::$app->sourceLanguage,
					'basePath' => '@common/modules/banner/messages',
				];
			}
		}
    }
}
