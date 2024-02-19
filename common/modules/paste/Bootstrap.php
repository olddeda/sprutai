<?php
namespace common\modules\paste;

use Yii;
use yii\base\BootstrapInterface;
use yii\web\GroupUrlRule;

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
		if ($app->hasModule('paste') && ($module = $app->getModule('paste')) instanceof Module) {

			// Make sure to register the base folder as alias as well or things like assets won't work anymore
			\Yii::setAlias('@common/modules/paste', __DIR__);

			if ($app instanceof \yii\console\Application) {
				$module->controllerNamespace = 'common\modules\paste\commands';
				
				// Add migration path
				Yii::$app->controllerMap['migrate']['migrationNamespaces'][] = 'common\modules\paste\migrations';
			}
			else {
				
				// Set rules
				if ($module->urlRules && count($module->urlRules)) {
					$configUrlRule = [
						'prefix' => Module::$name,
						'rules' => $module->urlRules,
					];
					$app->urlManager->addRules([new GroupUrlRule($configUrlRule)], false);
				}
			}

			// Add i18n support
			if ($app->has('i18n')) {
				$app->i18n->translations['paste*'] = [
					'class' => 'yii\i18n\PhpMessageSource',
					'sourceLanguage' => Yii::$app->sourceLanguage,
					'basePath' => '@common/modules/paste/messages',
				];
			}
		}
    }
}
