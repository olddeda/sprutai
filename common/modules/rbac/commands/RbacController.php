<?php

namespace common\modules\rbac\commands;

use Yii;

use common\modules\rbac\components\RbacController as BaseController;

/**
 * Task runner commands for Rbac.
 *
 * @package common\modules\rbac\commands
 */
class RbacController extends BaseController
{
	/**
	 * @var array tasks
	 */
	public $tasks = [
		[
			'name' => 'Client.Rbac',
			'description' => '[Client] Rbac',
			'roles' => ['Admin'],
			'permissions' => [
				
				// Role
				[
					'name' => 'client.rbac.role.index',
					'description' => 'Список ролей',
				],
				[
					'name' => 'client.rbac.role.view',
					'description' => 'Просмотр роли',
				],
				[
					'name' => 'client.rbac.role.create',
					'description' => 'Создание роли',
				],
				[
					'name' => 'client.rbac.role.update',
					'description' => 'Редактирование роли',
				],
				[
					'name' => 'client.rbac.role.delete',
					'description' => 'Удаление роли',
				],
				[
					'name' => 'client.rbac.role.assign',
					'description' => 'Назначение роли',
				],
				[
					'name' => 'client.rbac.role.revoke',
					'description' => 'Отзыв роли',
				],
				
				// Task
				[
					'name' => 'client.rbac.task.index',
					'description' => 'Список задач',
				],
				[
					'name' => 'client.rbac.task.view',
					'description' => 'Просмотр задачи',
				],
				[
					'name' => 'client.rbac.task.create',
					'description' => 'Создание задачи',
				],
				[
					'name' => 'client.rbac.task.update',
					'description' => 'Редактирование задачи',
				],
				[
					'name' => 'client.rbac.task.delete',
					'description' => 'Удаление задачи',
				],
				[
					'name' => 'client.rbac.task.assign',
					'description' => 'Назначение задачи',
				],
				[
					'name' => 'client.rbac.task.revoke',
					'description' => 'Отзыв задачи',
				],
				
				// Permission
				[
					'name' => 'client.rbac.permission.index',
					'description' => 'Список разрешений',
				],
				[
					'name' => 'client.rbac.permission.view',
					'description' => 'Просмотр разрешения',
				],
				[
					'name' => 'client.rbac.permission.create',
					'description' => 'Создание разрешения',
				],
				[
					'name' => 'client.rbac.permission.update',
					'description' => 'Редактирование разрешения',
				],
				[
					'name' => 'client.rbac.permission.delete',
					'description' => 'Удаление разрешения',
				],
				[
					'name' => 'client.rbac.permission.assign',
					'description' => 'Назначение разрешения',
				],
				[
					'name' => 'client.rbac.permission.revoke',
					'description' => 'Отзыв разрешения',
				],
			],
		],
	];
}