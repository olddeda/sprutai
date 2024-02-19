<?php

namespace common\modules\user\filters;

use yii\base\ActionFilter;
use yii\web\NotFoundHttpException;

/**
 * FrontendFilter is used to restrict access to admin controller in frontend when using Yii2-user with Yii2
 * advanced template.
 */
class FrontendFilter extends ActionFilter
{
    /**
     * @var array
     */
    public $controllers = [
		'admin'
	];

    /**
     * @param \yii\base\Action $action
     *
     * @return bool
     * @throws \yii\web\NotFoundHttpException
     */
    public function beforeAction($action) {
        if (in_array($action->controller->id, $this->controllers))
            throw new NotFoundHttpException('Not found');
        return true;
    }
}
