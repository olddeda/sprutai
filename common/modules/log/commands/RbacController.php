<?php

namespace common\modules\log\commands;

use Yii;

use common\modules\rbac\components\RbacController as BaseController;

/**
 * Task runner commands for Log rbac.
 *
 * @package common\modules\log\commands
 */
class RbacController extends BaseController
{
	/**
	 * @var array tasks
	 */
	public $tasks = [
		[
			'name' => 'Client.Log',
			'description' => '[Client] Логи',
			'roles' => ['Admin'],
			'permissions' => [
				
				// Default
				[
					'name' => 'client.log.default.index',
					'description' => 'Список логов',
				],
				[
					'name' => 'client.log.default.view',
					'description' => 'Просмотр лога',
				],
				[
					'name' => 'client.log.default.archive',
					'description' => 'Архивация логов',
				],
				[
					'name' => 'client.log.default.history',
					'description' => 'История логов',
				],
			],
		],
	];
}