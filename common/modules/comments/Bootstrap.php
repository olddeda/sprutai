<?php

namespace common\modules\comments;

use Yii;
use yii\base\Application;
use yii\base\BootstrapInterface;
use yii\console\Application as ConsoleApplication;

use common\modules\comments\models\Comment;

/**
 * Bootstrap
 * @package common\modules\content
 */
class Bootstrap implements BootstrapInterface
{
	/**
	 * Bootstrap method to be called during application bootstrap stage.
	 *
	 * @param Application $app the application currently running
	 */
	public function bootstrap($app) {

		/** @var Module $module */
		/** @var \yii\db\ActiveRecord $modelName */
		if ($app->hasModule('comments') && ($module = $app->getModule('comments')) instanceof Module) {

			// Make sure to register the base folder as alias as well or things like assets won't work anymore
			\Yii::setAlias('@common/modules/comments', __DIR__);

			if ($app instanceof ConsoleApplication) {
				$module->controllerNamespace = 'common\modules\comments\commands';
				
				// Add migration path
				Yii::$app->controllerMap['migrate']['migrationNamespaces'][] = 'common\modules\comments\migrations';
			}
			else {
				if ($module->userIdentityClass === null) {
					$module->userIdentityClass = Yii::$app->getUser()->identityClass;
				}
				if ($module->commentModelClass === null) {
					$module->commentModelClass = Comment::class;
				}
			}

			// Add i18n support
			if ($app->has('i18n')) {
				$app->i18n->translations['comments*'] = [
					'class' => 'yii\i18n\PhpMessageSource',
					'sourceLanguage' => 'en',
					'basePath' => '@common/modules/comments/messages',
				];
			}
		}
	}
}
