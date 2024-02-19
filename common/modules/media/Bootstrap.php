<?php

namespace common\modules\media;

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
		if ($app->hasModule('media') && ($module = $app->getModule('media')) instanceof Module) {

			// Make sure to register the base folder as alias as well or things like assets won't work anymore
			\Yii::setAlias('@common/modules/media', __DIR__);

			if ($app instanceof ConsoleApplication) {
				$module->controllerNamespace = 'common\modules\media\commands';
				
				// Add migration path
				Yii::$app->controllerMap['migrate']['migrationNamespaces'][] = 'common\modules\media\migrations';
			}
			else {
				
				// Set rules
				if ($module->urlRules && count($module->urlRules)) {
					$configUrlRule = [
						'prefix' => 'media',
						'rules' => $module->urlRules,
					];
					$app->urlManager->addRules([new GroupUrlRule($configUrlRule)], false);
				}
				
				$app->urlManager->addRules([new GroupUrlRule([
					'rules' => [
						$module->rulesCrop => 'media/crop/index'
					]
				])], false);
			}

			// Add i18n support
			if ($app->has('i18n')) {
				$app->i18n->translations['media*'] = [
					'class' => 'yii\i18n\PhpMessageSource',
					'sourceLanguage' => 'en',
					'basePath' => '@common/modules/media/messages',
				];
			}

		}
    }
}
