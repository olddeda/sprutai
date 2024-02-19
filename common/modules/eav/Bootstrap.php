<?php
namespace common\modules\eav;

use Yii;
use yii\base\BootstrapInterface;
use yii\web\GroupUrlRule;
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

		/** @var Module $module */
		/** @var \yii\db\ActiveRecord $modelName */
		if ($app->hasModule('eav') && ($module = $app->getModule('eav')) instanceof Module) {
			
			Yii::setAlias('@common/modules/eav', __DIR__);

			if ($app instanceof ConsoleApplication) {
				$module->controllerNamespace = 'common\modules\eav\commands';
				
				// Add migration path
				Yii::$app->controllerMap['migrate']['migrationNamespaces'][] = 'common\modules\eav\migrations';
			}
			else {
				
			}

			// Add i18n support
			if ($app->has('i18n')) {
				$app->i18n->translations['eav*'] = [
					'class' => 'yii\i18n\PhpMessageSource',
					'sourceLanguage' => 'en',
					'basePath' => '@common/modules/eav/messages',
				];
			}

		}
    }
}
