<?php

namespace common\modules\rbac\controllers;

use Yii;
use yii\web\NotFoundHttpException;

use common\modules\rbac\components\Item;
use common\modules\rbac\components\Task;

class TaskController extends ItemControllerAbstract
{
	/**
	 * @var string
	 */
	protected $modelClass = 'common\modules\rbac\models\Task';

	/**
	 * @var int
	 */
	protected $type = Item::TYPE_TASK;

	/**
	 * @inheritdoc
	 */
	protected function getItem($name) {
		$task = Yii::$app->authManager->getTask($name);
		if ($task instanceof Task)
			return $task;

		throw new NotFoundHttpException;
	}
}