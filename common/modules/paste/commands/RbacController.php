<?php
namespace common\modules\paste\commands;

use Yii;

use common\modules\rbac\components\RbacController as BaseController;

/**
 * Task runner commands for Paste rbac.
 *
 * @package common\modules\paste\commands
 */
class RbacController extends BaseController
{
	/**
	 * @var array tasks
	 */
	public $tasks = [
		
		// Client.Paste
		[
			'name' => 'Client.Paste',
			'description' => '[Client] Фрагменты кода',
			'roles' => ['Admin', 'User'],
			'permissions' => [
				
				// Default
				[
					'name' => 'client.paste.default.index',
					'description' => 'Список фрагментов кода',
				],
				[
					'name' => 'client.paste.default.view',
					'description' => 'Просмотр фрагмента кода',
				],
				[
					'name' => 'client.paste.default.create',
					'description' => 'Создание фрагмента кода',
				],
				[
					'name' => 'client.paste.default.update',
					'description' => 'Редактирование фрагмента кода',
				],
				[
					'name' => 'client.paste.default.delete',
					'description' => 'Удаление фрагмента кода',
				],
				[
					'name' => 'client.paste.default.editable',
					'description' => 'Редактирование поля',
				],
				
				
			],
		],
	];
}