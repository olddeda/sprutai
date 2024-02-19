<?php
namespace common\modules\shortener;

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
		if ($app->hasModule('shortener') && ($module = $app->getModule('shortener')) instanceof Module) {

			// Make sure to register the base folder as alias as well or things like assets won't work anymore
			\Yii::setAlias('@common/modules/shortener', __DIR__);

			if ($app instanceof \yii\console\Application) {
				$module->controllerNamespace = 'common\modules\shortener\commands';
				
				// Add migration path
				Yii::$app->controllerMap['migrate']['migrationNamespaces'][] = 'common\modules\shortener\migrations';
			}

			// Add i18n support
			if ($app->has('i18n')) {
				$app->i18n->translations['shortener*'] = [
					'class' => 'yii\i18n\PhpMessageSource',
					'sourceLanguage' => Yii::$app->sourceLanguage,
					'basePath' => '@common/modules/shortener/messages',
				];
			}
		}
    }
}
