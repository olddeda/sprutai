<?php
namespace api\components;

use api\modules\v1\components\actions\Action;
use Yii;

use common\modules\rbac\components\AccessControl as BaseAccessControl;

use common\modules\user\components\User;

/**
 * Class AccessControl
 * @package api\components
 */
class AccessControl extends BaseAccessControl
{
    /**
     * This method is invoked right before an action is to be executed (after all possible filters.)
     * You may override this method to do last-minute preparation for the action.
     * @param $action the action to be executed.
     * @return boolean whether the action should continue to be executed.
     */
    public function beforeAction($action): bool
    {
        // Get action unique id
        $actionId = $this->_formatId($action->controller->id.'.'.$action->id);

        /** @var User $user */
        $user = Yii::$app->getUser();
        if ($user->can($actionId)) {
            return true;
        }

        $controller = $action->controller;
        do {
            if ($user->can($this->_formatId($controller->getUniqueId()).'.*')) {
                return true;
            }
            $controller = $controller->module;
        } while ($controller !== null);

        return parent::beforeAction($action);
    }
}