<?php
namespace common\modules\event\commands;

use Yii;

use common\modules\rbac\components\RbacController as BaseController;

/**
 * Task runner commands for Event rbac.
 *
 * @package common\modules\event\commands
 */
class RbacController extends BaseController
{
	/**
	 * @var array tasks
	 */
	public $tasks = [
		
		// Client.Event
		[
			'name' => 'Client.Event',
			'description' => '[Client] События',
			'roles' => ['Admin'],
			'permissions' => [
				
				// Default
				[
					'name' => 'client.event.default.index',
					'description' => 'События',
				],
				[
					'name' => 'client.event.default.view',
					'description' => 'Просмотр события',
				],
				[
					'name' => 'client.event.default.create',
					'description' => 'Создание события',
				],
				[
					'name' => 'client.event.default.update',
					'description' => 'Редактирование события',
				],
				[
					'name' => 'client.event.default.delete',
					'description' => 'Удаление события',
				],
				[
					'name' => 'client.event.default.editable',
					'description' => 'Редактирование поля',
				],
			],
		],
	];
}