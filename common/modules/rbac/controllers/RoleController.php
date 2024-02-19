<?php

namespace common\modules\rbac\controllers;

use Yii;
use yii\web\NotFoundHttpException;

use common\modules\rbac\components\Item;
use common\modules\rbac\components\Role;

class RoleController extends ItemControllerAbstract
{
    /**
	 * @var string
	 */
    protected $modelClass = 'common\modules\rbac\models\Role';

	/**
	 * @var int
	 */
    protected $type = Item::TYPE_ROLE;

    /**
	 * @inheritdoc
	 */
    protected function getItem($name) {
        $role = Yii::$app->authManager->getRole($name);
        if ($role instanceof Role)
            return $role;

        throw new NotFoundHttpException;
    }
}