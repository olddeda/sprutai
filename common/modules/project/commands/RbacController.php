<?php
namespace common\modules\project\commands;

use Yii;

use common\modules\rbac\components\RbacController as BaseController;

/**
 * Task runner commands for Project rbac.
 *
 * @package common\modules\project\commands
 */
class RbacController extends BaseController
{
	/**
	 * @var array tasks
	 */
	public $tasks = [
		
		// Client.Project
		[
			'name' => 'Client.Project',
			'description' => '[Client] Проекты',
			'roles' => ['Admin'],
			'permissions' => [

				// Default
				[
					'name' => 'client.project.default.index',
					'description' => 'Проекты',
				],
				[
					'name' => 'client.project.default.view',
					'description' => 'Просмотр проекта',
				],
				[
					'name' => 'client.project.default.create',
					'description' => 'Создание проекта',
				],
				[
					'name' => 'client.project.default.update',
					'description' => 'Редактирование проекта',
				],
				[
					'name' => 'client.project.default.delete',
					'description' => 'Удаление проекта',
				],
				[
					'name' => 'client.project.default.editable',
					'description' => 'Редактирование поля',
				],

                // Payer
                [
                    'name' => 'client.project.payer.index',
                    'description' => 'Участники',
                ],
                [
                    'name' => 'client.project.payer.view',
                    'description' => 'Просмотр участника',
                ],
                [
                    'name' => 'client.project.payer.create',
                    'description' => 'Создание участика',
                ],
                [
                    'name' => 'client.project.payer.update',
                    'description' => 'Редактирование участника',
                ],
                [
                    'name' => 'client.project.payer.delete',
                    'description' => 'Удаление участника',
                ],
                [
                    'name' => 'client.project.payer.editable',
                    'description' => 'Редактирование поля',
                ],
				
				// Event
				[
					'name' => 'client.project.event.index',
					'description' => 'События',
				],
				[
					'name' => 'client.project.event.view',
					'description' => 'Просмотр события',
				],
				[
					'name' => 'client.project.event.create',
					'description' => 'Создание события',
				],
				[
					'name' => 'client.project.event.update',
					'description' => 'Редактирование события',
				],
				[
					'name' => 'client.project.event.delete',
					'description' => 'Удаление события',
				],
				[
					'name' => 'client.project.event.editable',
					'description' => 'Редактирование поля',
				],
			],
		],
		
		// Client.Project
		[
			'name' => 'Client.Project.Editor',
			'description' => '[Client] Проекты для редактора',
			'roles' => ['Editor'],
			'permissions' => [
				
				// Default
				[
					'name' => 'client.project.default.index',
					'description' => 'Проекты',
				],
				[
					'name' => 'client.project.default.view',
					'description' => 'Просмотр проекта',
				],
				[
					'name' => 'client.project.default.create',
					'description' => 'Создание проекта',
				],
				[
					'name' => 'client.project.default.update',
					'description' => 'Редактирование проекта',
				],
				[
					'name' => 'client.project.default.editable',
					'description' => 'Редактирование поля',
				],
				
				// Payer
				[
					'name' => 'client.project.payer.index',
					'description' => 'Участники',
				],
				[
					'name' => 'client.project.payer.view',
					'description' => 'Просмотр участника',
				],
				[
					'name' => 'client.project.payer.create',
					'description' => 'Создание участика',
				],
				[
					'name' => 'client.project.payer.update',
					'description' => 'Редактирование участника',
				],
				[
					'name' => 'client.project.payer.editable',
					'description' => 'Редактирование поля',
				],
				
				// Event
				[
					'name' => 'client.project.event.index',
					'description' => 'События',
				],
				[
					'name' => 'client.project.event.view',
					'description' => 'Просмотр события',
				],
				[
					'name' => 'client.project.event.create',
					'description' => 'Создание события',
				],
				[
					'name' => 'client.project.event.update',
					'description' => 'Редактирование события',
				],
				[
					'name' => 'client.project.event.editable',
					'description' => 'Редактирование поля',
				],
			],
		],
	];
}