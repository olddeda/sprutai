<?php
namespace common\modules\menu\commands;

use Yii;

use common\modules\rbac\components\RbacController as BaseController;

/**
 * Task runner commands for Menu rbac.
 *
 * @package common\modules\menu\commands
 */
class RbacController extends BaseController
{
	/**
	 * @var array tasks
	 */
	public $tasks = [
		
		// Client
		[
			'name' => 'Client.Menu',
			'description' => '[Menu] Меню',
			'roles' => [
				'Admin',
			],
			'permissions' => [
				
				// Default
				[
					'name' => 'client.menu.default.index',
					'description' => 'Список меню',
				],
				[
					'name' => 'client.menu.default.view',
					'description' => 'Просмотр меню',
				],
				[
					'name' => 'client.menu.default.create',
					'description' => 'Создание меню',
				],
				[
					'name' => 'client.menu.default.update',
					'description' => 'Редактирование меню',
				],
				[
					'name' => 'client.menu.default.delete',
					'description' => 'Удаление меню',
				],
				[
					'name' => 'client.menu.default.editable',
					'description' => 'Редактирование одного поля',
				],
				
				// Nested
				[
					'name' => 'client.menu.nested.create',
					'description' => 'Создание связи',
				],
				[
					'name' => 'client.menu.nested.update',
					'description' => 'Редактирование связи',
				],
				[
					'name' => 'client.menu.nested.delete',
					'description' => 'Удаление связи',
				],
				
				// Nested Items
				[
					'name' => 'client.menu.nested-item.create',
					'description' => 'Создание связи',
				],
				[
					'name' => 'client.menu.nested-item.update',
					'description' => 'Редактирование связи',
				],
				[
					'name' => 'client.menu.nested-item.delete',
					'description' => 'Удаление связи',
				],
			],
		],
	];
}