<?php
namespace common\modules\banner\commands;

use Yii;

use common\modules\rbac\components\RbacController as BaseController;

/**
 * Task runner commands for Banner rbac.
 *
 * @package common\modules\banner\commands
 */
class RbacController extends BaseController
{
	/**
	 * @var array tasks
	 */
	public $tasks = [
		
		// Client.Banner
		[
			'name' => 'Client.Banner',
			'description' => '[Client] Баннеры',
			'roles' => ['Admin'],
			'permissions' => [
				
				// Default
				[
					'name' => 'client.banner.default.index',
					'description' => 'Список баннеров',
				],
				[
					'name' => 'client.banner.default.view',
					'description' => 'Просмотр баннера',
				],
				[
					'name' => 'client.banner.default.create',
					'description' => 'Создание баннера',
				],
				[
					'name' => 'client.banner.default.update',
					'description' => 'Редактирование баннера',
				],
				[
					'name' => 'client.banner.default.delete',
					'description' => 'Удаление баннера',
				],
				[
					'name' => 'client.banner.default.editable',
					'description' => 'Редактирование поля',
				],
				
				
			],
		],
	];
}