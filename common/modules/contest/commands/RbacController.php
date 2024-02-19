<?php
namespace common\modules\contest\commands;

use Yii;

use common\modules\rbac\components\RbacController as BaseController;

/**
 * Task runner commands for Contest rbac.
 *
 * @package common\modules\contest\commands
 */
class RbacController extends BaseController
{
	/**
	 * @var array tasks
	 */
	public $tasks = [
		
		// Client.Contest
		[
			'name' => 'Client.Contest',
			'description' => '[Client] Конкурсы',
			'roles' => ['Admin'],
			'permissions' => [
				
				// Default
				[
					'name' => 'client.contest.default.index',
					'description' => 'Список конкурсов',
				],
				[
					'name' => 'client.contest.default.view',
					'description' => 'Просмотр конкурса',
				],
				[
					'name' => 'client.contest.default.create',
					'description' => 'Создание конкурсв',
				],
				[
					'name' => 'client.contest.default.update',
					'description' => 'Редактирование конкурса',
				],
				[
					'name' => 'client.contest.default.delete',
					'description' => 'Удаление конкурса',
				],
				[
					'name' => 'client.contest.default.editable',
					'description' => 'Редактирование поля',
				],
				
				
			],
		],
		
		// Client.Contest.Editor
		[
			'name' => 'Client.Contest.Editor',
			'description' => '[Client] Конкурсы для модераторов',
			'roles' => ['Editor'],
			'permissions' => [
				
				// Default
				[
					'name' => 'client.contest.default.index',
					'description' => 'Список конкурсов',
				],
				[
					'name' => 'client.contest.default.view',
					'description' => 'Просмотр конкурса',
				],
				[
					'name' => 'client.contest.default.create',
					'description' => 'Создание конкурсв',
				],
				[
					'name' => 'client.contest.default.update',
					'description' => 'Редактирование конкурса',
				],
				[
					'name' => 'client.contest.default.editable',
					'description' => 'Редактирование поля',
				],
			],
		],
	];
}