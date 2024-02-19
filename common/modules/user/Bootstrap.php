<?php

namespace common\modules\user;

use Yii;
use yii\base\BootstrapInterface;
use yii\console\Application as ConsoleApplication;
use yii\authclient\Collection;
use yii\i18n\PhpMessageSource;
use yii\web\GroupUrlRule;

/**
 * Bootstrap class registers module and user application component. It also creates some url rules which will be applied
 * when UrlManager.enablePrettyUrl is enabled.
 */
class Bootstrap implements BootstrapInterface
{
	/** @var array Model's map */
	private $_modelMap = [
		'User' => 'common\modules\user\models\User',
		'UserAccount' => 'common\modules\user\models\UserAccount',
		'UserProfile' => 'common\modules\user\models\UserProfile',
		'UserAddress' => 'common\modules\user\models\UserAddress',
		'UserSubscribe' => 'common\modules\user\models\UserSubscribe',
		'UserToken' => 'common\modules\user\models\UserToken',
		'SigninForm' => 'common\modules\user\models\forms\SigninForm',
		'SignupForm' => 'common\modules\user\models\forms\SignupForm',
		'ResendForm' => 'common\modules\user\models\forms\ResendForm',
		'SettingsForm' => 'common\modules\user\models\forms\SettingsForm',
		'ForgotForm' => 'common\modules\user\models\forms\ForgotForm',
		'UserSearch' => 'common\modules\user\models\search\UserSearch',
	];

	/** @inheritdoc */
	public function bootstrap($app) {

		/** @var Module $module */
		/** @var \yii\db\ActiveRecord $modelName */
		if ($app->hasModule('user') && ($module = $app->getModule('user')) instanceof Module) {
			$this->_modelMap = array_merge($this->_modelMap, $module->modelMap);
			foreach ($this->_modelMap as $name => $definition) {
				$class = "common\\modules\\user\\models\\".$name;
				Yii::$container->set($class, $definition);

				$modelName = is_array($definition) ? $definition['class'] : $definition;
				$module->modelMap[$name] = $modelName;
				if (in_array($name, [
					'User',
					'UserProfile',
					'UserAddress',
					'UserSubscribe',
					'UserToken',
					'UserAccount'
				])) {
					Yii::$container->set($name.'Query', function () use ($modelName) {
						return $modelName::find();
					});
				}
			}

			Yii::$container->setSingleton(Finder::class, [
				'userQuery' => Yii::$container->get('UserQuery'),
				'profileQuery' => Yii::$container->get('UserProfileQuery'),
				'addressQuery' => Yii::$container->get('UserAddressQuery'),
				'subscribeQuery' => Yii::$container->get('UserSubscribeQuery'),
				'tokenQuery' => Yii::$container->get('UserTokenQuery'),
				'accountQuery' => Yii::$container->get('UserAccountQuery'),
			]);

			if ($app instanceof ConsoleApplication) {
				$module->controllerNamespace = 'common\modules\user\commands';
				
				// Add migration path
				//Yii::$app->controllerMap['migrate']['migrationNamespaces'][] = 'common\modules\user\migrations';
			}
			else {
				Yii::$container->set('yii\web\User', [
					'enableAutoLogin' => true,
					'loginUrl' => ['/user/security/signin'],
					'identityClass' => $module->modelMap['User'],
				]);

				if ($module->urlRules && count($module->urlRules)) {
					$configUrlRule = [
						'prefix' => $module->urlPrefix,
						'rules' => $module->urlRules,
					];

					if ($module->urlPrefix != 'user')
						$configUrlRule['routePrefix'] = 'user';
					$app->urlManager->addRules([new GroupUrlRule($configUrlRule)], false);
				}

				if (!$app->has('authClientCollection')) {
					$app->set('authClientCollection', [
						'class' => Collection::class,
					]);
				}
			}

			if (!isset($app->get('i18n')->translations['user*'])) {
				$app->get('i18n')->translations['user*'] = [
					'class' => PhpMessageSource::class,
					'basePath' => __DIR__.'/messages',
				];
			}

			Yii::$container->set('common\modules\user\Mailer', $module->mailer);
		}
	}
}
