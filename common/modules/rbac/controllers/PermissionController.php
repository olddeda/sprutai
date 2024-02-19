<?php

namespace common\modules\rbac\controllers;

use Yii;
use yii\web\NotFoundHttpException;

use common\modules\rbac\components\Item;
use common\modules\rbac\components\Permission;

class PermissionController extends ItemControllerAbstract
{
    /**
	 * @var string
	 */
    protected $modelClass = 'common\modules\rbac\models\Permission';
    
    /**
	 * @var int
	 */
    protected $type = Item::TYPE_PERMISSION;

    /**
	 * @inheritdoc
	 */
    protected function getItem($name) {
        $permission = Yii::$app->authManager->getPermission($name);
        if ($permission instanceof Permission)
            return $permission;

        throw new NotFoundHttpException;
    }
}