<?php
namespace common\modules\plugin\commands;

use Yii;

use common\modules\rbac\components\RbacController as BaseController;

/**
 * Task runner commands for Plugin rbac.
 *
 * @package common\modules\plugin\commands
 */
class RbacController extends BaseController
{
	/**
	 * @var array tasks
	 */
	public $tasks = [
		
		// Client.Plugin
		[
			'name' => 'Client.Plugin',
			'description' => '[Client] Плагины',
			'roles' => ['Admin'],
			'permissions' => [

				// Default
				[
					'name' => 'client.plugin.default.index',
					'description' => 'Плагины',
				],
				[
					'name' => 'client.plugin.default.view',
					'description' => 'Просмотр плагина',
				],
				[
					'name' => 'client.plugin.default.create',
					'description' => 'Создание плагина',
				],
				[
					'name' => 'client.plugin.default.update',
					'description' => 'Редактирование плагина',
				],
				[
					'name' => 'client.plugin.default.delete',
					'description' => 'Удаление плагина',
				],
				[
					'name' => 'client.plugin.default.editable',
					'description' => 'Редактирование поля',
				],

                // Payer
                [
                    'name' => 'client.plugin.payer.index',
                    'description' => 'Участники',
                ],
                [
                    'name' => 'client.plugin.payer.view',
                    'description' => 'Просмотр участника',
                ],
                [
                    'name' => 'client.plugin.payer.create',
                    'description' => 'Создание участика',
                ],
                [
                    'name' => 'client.plugin.payer.update',
                    'description' => 'Редактирование участника',
                ],
                [
                    'name' => 'client.plugin.payer.delete',
                    'description' => 'Удаление участника',
                ],
                [
                    'name' => 'client.plugin.payer.editable',
                    'description' => 'Редактирование поля',
                ],
				
				// Instructions
				[
					'name' => 'client.plugin.instruction.index',
					'description' => 'Инструкция',
				],
				[
					'name' => 'client.plugin.instruction.update',
					'description' => 'Редактирование инструкции',
				],
				
				// Versions
				[
					'name' => 'client.plugin.version.index',
					'description' => 'Версии',
				],
				[
					'name' => 'client.plugin.version.view',
					'description' => 'Просмотр версии',
				],
				[
					'name' => 'client.plugin.version.create',
					'description' => 'Создание версии',
				],
				[
					'name' => 'client.plugin.version.update',
					'description' => 'Редактирование версии',
				],
				[
					'name' => 'client.plugin.version.delete',
					'description' => 'Удаление версии',
				],
				[
					'name' => 'client.plugin.version.editable',
					'description' => 'Редактирование поля',
				],
				[
					'name' => 'client.plugin.version.select-provider',
					'description' => 'Выбор провайдера',
				],
				[
					'name' => 'client.plugin.version.select-repository',
					'description' => 'Выбор репозитория',
				],
				[
					'name' => 'client.plugin.version.select-release',
					'description' => 'Выбор версии',
				],
				[
					'name' => 'client.plugin.version.authorize',
					'description' => 'Авторизация провайдера',
				],
			],
		],
		
		// Client.Plugin.User
		[
			'name' => 'Client.Plugin.User',
			'description' => '[Client] Плагины для пользователя',
			'roles' => ['User'],
			'permissions' => [
				
				// Default
				[
					'name' => 'client.plugin.default.index',
					'description' => 'Плагины',
				],
				[
					'name' => 'client.plugin.default.view',
					'description' => 'Просмотр плагина',
				],
				[
					'name' => 'client.plugin.default.create',
					'description' => 'Создание плагина',
				],
				[
					'name' => 'client.plugin.default.update',
					'description' => 'Редактирование плагина',
				],
				[
					'name' => 'client.plugin.default.editable',
					'description' => 'Редактирование поля',
				],
				
				// Payer
				[
					'name' => 'client.plugin.payer.index',
					'description' => 'Участники',
				],
				
				// Instructions
				[
					'name' => 'client.plugin.instruction.index',
					'description' => 'Инструкция',
				],
				[
					'name' => 'client.plugin.instruction.update',
					'description' => 'Редактирование инструкции',
				],
				
				// Versions
				[
					'name' => 'client.plugin.version.index',
					'description' => 'Версии',
				],
				[
					'name' => 'client.plugin.version.view',
					'description' => 'Просмотр версии',
				],
				[
					'name' => 'client.plugin.version.create',
					'description' => 'Создание версии',
				],
				[
					'name' => 'client.plugin.version.update',
					'description' => 'Редактирование версии',
				],
				[
					'name' => 'client.plugin.version.delete',
					'description' => 'Удаление версии',
				],
				[
					'name' => 'client.plugin.version.editable',
					'description' => 'Редактирование поля',
				],
				[
					'name' => 'client.plugin.version.authorize',
					'description' => 'Авторизация провайдера',
				],
				[
					'name' => 'client.plugin.version.select-provider',
					'description' => 'Выбор провайдера',
				],
				[
					'name' => 'client.plugin.version.select-repository',
					'description' => 'Выбор репозитория',
				],
				[
					'name' => 'client.plugin.version.select-release',
					'description' => 'Выбор версии',
				],
				[
					'name' => 'client.plugin.version.authorize',
					'description' => 'Авторизация провайдера',
				],
			],
		],
		
		// Client.Plugin.Editor
		[
			'name' => 'Client.Plugin.Editor',
			'description' => '[Client] Плагины для редактора',
			'roles' => ['Editor'],
			'permissions' => [
				
				// Default
				[
					'name' => 'client.plugin.default.index',
					'description' => 'Плагины',
				],
				[
					'name' => 'client.plugin.default.view',
					'description' => 'Просмотр плагина',
				],
				[
					'name' => 'client.plugin.default.create',
					'description' => 'Создание плагина',
				],
				[
					'name' => 'client.plugin.default.update',
					'description' => 'Редактирование плагина',
				],
				[
					'name' => 'client.plugin.default.editable',
					'description' => 'Редактирование поля',
				],
				
				// Payer
				[
					'name' => 'client.plugin.payer.index',
					'description' => 'Участники',
				],
				
				// Instructions
				[
					'name' => 'client.plugin.instruction.index',
					'description' => 'Инструкция',
				],
				[
					'name' => 'client.plugin.instruction.update',
					'description' => 'Редактирование инструкции',
				],
				
				// Versions
				[
					'name' => 'client.plugin.version.index',
					'description' => 'Версии',
				],
				[
					'name' => 'client.plugin.version.view',
					'description' => 'Просмотр версии',
				],
				[
					'name' => 'client.plugin.version.create',
					'description' => 'Создание версии',
				],
				[
					'name' => 'client.plugin.version.update',
					'description' => 'Редактирование версии',
				],
				[
					'name' => 'client.plugin.version.delete',
					'description' => 'Удаление версии',
				],
				[
					'name' => 'client.plugin.version.editable',
					'description' => 'Редактирование поля',
				],
				[
					'name' => 'client.plugin.version.select-provider',
					'description' => 'Выбор провайдера',
				],
				[
					'name' => 'client.plugin.version.select-repository',
					'description' => 'Выбор репозитория',
				],
				[
					'name' => 'client.plugin.version.select-release',
					'description' => 'Выбор версии',
				],
				[
					'name' => 'client.plugin.version.authorize',
					'description' => 'Авторизация провайдера',
				],
			],
		],
	];
}