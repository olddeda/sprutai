<?php
namespace common\modules\lookup\commands;

use common\modules\rbac\components\RbacController as BaseController;

/**
 * Task runner commands for Lookup rbac.
 *
 * @package common\modules\lookup\commands
 */
class RbacController extends BaseController
{
	
	/**
	 * @var array tasks
	 */
	public $tasks = [
		[
			'name' => 'Client.Lookup',
			'description' => '[Client] Справочники',
			'roles' => ['Admin'],
			'permissions' => [
				[
					'name' => 'client.lookup.default.index',
					'description' => 'Список справочников',
				],
				[
					'name' => 'client.lookup.default.view',
					'description' => 'Просмотр справочника',
				],
				[
					'name' => 'client.lookup.default.create',
					'description' => 'Создание справочника',
				],
				[
					'name' => 'client.lookup.default.update',
					'description' => 'Редактирование справочника',
				],
				[
					'name' => 'client.lookup.default.delete',
					'description' => 'Удаление справочника',
				],
				[
					'name' => 'client.lookup.default.editable',
					'description' => 'Редактирование поля',
				],
			],
		],
	];
}