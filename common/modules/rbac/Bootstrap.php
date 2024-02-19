<?php

namespace common\modules\rbac;

use Yii;
use yii\base\Application;
use yii\base\BootstrapInterface;
use yii\web\GroupUrlRule;

use common\modules\rbac\components\DbManager;

use common\modules\user\Module as UserModule;

/**
 * Bootstrap class registers translations and needed application components.
 */
class Bootstrap implements BootstrapInterface
{
    /**
	 * @inheritdoc
	 */
    public function bootstrap($app) {
    	
        if ($this->checkRbacModuleInstalled($app)) {
			
			
			// Make sure to register the base folder as alias as well or things like assets won't work anymore
			\Yii::setAlias('@common/modules/rbac', __DIR__);

			// Register translations
			if (!isset($app->get('i18n')->translations['rbac*'])) {
				$app->get('i18n')->translations['rbac*'] = [
					'class' => 'yii\i18n\PhpMessageSource',
					'basePath' => __DIR__.'/messages',
				];
			}
	
			

			/** @var Module $module **/
			$module = $app->getModule('rbac');
			
			if ($app instanceof \yii\console\Application) {
				$module->controllerNamespace = 'common\modules\rbac\commands';
				
				// Add migration path
				Yii::$app->controllerMap['migrate']['migrationNamespaces'][] = 'common\modules\rbac\migrations';
			}
			else {
				
				// Register auth manager
				if (!$this->checkAuthManagerConfigured($app)) {
					$app->set('authManager', [
						'class' => DbManager::className(),
						'cache' => 'cache',
					]);
				}
				
				// if common/modules/user extension is installed, copy admin list from there
				if ($this->checkUserModuleInstalled($app)) {
					$app->getModule('rbac')->admins = $app->getModule('user')->admins;
				}

				// Add rules
				$app->urlManager->addRules([new GroupUrlRule([
					'prefix' => 'rbac',
					'rules' => $module->urlRules,
				])], false);
			}
        }
    }
    
    /**
     * Verifies that common/modules/rbac is installed and configured.
     * @param Application $app
     * @return bool
     */
    protected function checkRbacModuleInstalled(Application $app) {
        return $app->hasModule('rbac') && $app->getModule('rbac') instanceof Module;
    }
    
    /**
     * Verifies that common/modules/user is installed and configured.
     * @param  Application $app
     * @return bool
     */
    protected function checkUserModuleInstalled(Application $app) {
        return $app->hasModule('user') && $app->getModule('user') instanceof UserModule;
    }
    
    /**
     * Verifies that authManager component is configured.
     * @param  Application $app
     * @return bool
     */
    protected function checkAuthManagerConfigured(Application $app) {
        return $app->authManager instanceof \yii\rbac\ManagerInterface;
    }
}
