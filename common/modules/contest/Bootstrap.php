<?php
namespace common\modules\contest;

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
		if ($app->hasModule('contest') && ($module = $app->getModule('contest')) instanceof Module) {

			// Make sure to register the base folder as alias as well or things like assets won't work anymore
			\Yii::setAlias('@common/modules/contest', __DIR__);

			if ($app instanceof \yii\console\Application) {
				$module->controllerNamespace = 'common\modules\contest\commands';
				
				// Add migration path
				Yii::$app->controllerMap['migrate']['migrationNamespaces'][] = 'common\modules\contest\migrations';
			}

			// Add i18n support
			if ($app->has('i18n')) {
				$app->i18n->translations['contest*'] = [
					'class' => 'yii\i18n\PhpMessageSource',
					'sourceLanguage' => Yii::$app->sourceLanguage,
					'basePath' => '@common/modules/contest/messages',
				];
			}
		}
    }
}
