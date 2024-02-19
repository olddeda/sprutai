<?php

namespace common\modules\base;

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
		if ($app->hasModule('base') && ($module = $app->getModule('base')) instanceof Module) {

			// Make sure to register the base folder as alias as well or things like assets won't work anymore
			\Yii::setAlias('@common/modules/base', __DIR__);

			if ($app instanceof \yii\console\Application) {
				$module->controllerNamespace = 'common\modules\base\commands';
			}

			// Add i18n support
			if ($app->has('i18n')) {
				$app->i18n->translations['base*'] = [
					'class' => 'yii\i18n\PhpMessageSource',
					'sourceLanguage' => 'en',
					'basePath' => '@common/modules/base/messages',
				];
			}
		}
    }
}
