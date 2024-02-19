<?php

namespace common\modules\settings\commands;

use Yii;

use common\modules\rbac\components\RbacController as BaseController;

/**
 * Task runner commands for Settings rbac.
 *
 * @package common\modules\settings\commands
 */
class RbacController extends BaseController
{
	
	/**
	 * @var array tasks
	 */
	public $tasks = [
		[
			'name' => 'Client.Module.Settings',
			'description' => '[Client] Настройки',
			'roles' => ['Admin'],
			'permissions' => [
				
				// Default
				[
					'name' => 'client.settings.default.index',
					'description' => 'Список настроек',
				],
				[
					'name' => 'client.settings.default.create',
					'description' => 'Создание настройки',
				],
				[
					'name' => 'client.settings.default.update',
					'description' => 'Редактирование настройки',
				],
				[
					'name' => 'client.settings.default.delete',
					'description' => 'Удаление настройки',
				],
			],
		],
	];
}