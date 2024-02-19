<?php

namespace api\modules\v1;

use yii\base\BootstrapInterface;

/**
 * Class Bootstrap
 * @package api\modules\v1
 */
class Bootstrap implements BootstrapInterface
{
    /**
	 * @inheritdoc
	 */
    public function bootstrap($app) {

		/** @var Module $module */
		/** @var \yii\db\ActiveRecord $modelName */
		if ($app->hasModule('v1') && ($module = $app->getModule('v1')) instanceof Module) {

			// Make sure to register the base folder as alias as well or things like assets won't work anymore
			\Yii::setAlias('@api/modules/v1', __DIR__);

			if ($app instanceof \yii\console\Application) {
				$module->controllerNamespace = 'api\modules\v1\commands';
			}

			// Add i18n support
			if ($app->has('i18n')) {
				$app->i18n->translations['v1*'] = [
					'class' => 'yii\i18n\PhpMessageSource',
					'sourceLanguage' => 'en',
					'basePath' => '@api/modules/v1/messages',
				];
			}
		}
    }
}
