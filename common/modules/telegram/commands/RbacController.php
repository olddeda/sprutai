<?php
namespace common\modules\telegram\commands;

use common\modules\rbac\components\RbacController as BaseController;

/**
 * Task runner commands for telegram rbac.
 *
 * @package common\modules\telegram\commands
 */
class RbacController extends BaseController
{
	/**
	 * @var array tasks
	 */
	public $tasks = [

        [
            'name' => 'Telegram',
            'description' => 'Telegram',
            'roles' => [
                'Admin',
            ],
            'permissions' => [

                // Chats
                [
                    'name' => 'telegram.chat.index',
                    'description' => 'Чаты',
                ],
                [
                    'name' => 'telegram.chat.view',
                    'description' => 'Просмотр чата',
                ],
                [
                    'name' => 'telegram.chat.create',
                    'description' => 'Создание чата',
                ],
                [
                    'name' => 'telegram.chat.update',
                    'description' => 'Редактирование чата',
                ],
                [
                    'name' => 'telegram.chat.delete',
                    'description' => 'Удаление чата',
                ],
                [
                    'name' => 'telegram.chat.editable',
                    'description' => 'Редактирование поля',
                ],
                [
                    'name' => 'telegram.chat.admin',
                    'description' => 'Администрирование чатов',
                ],
                [
                    'name' => 'telegram.chat.tags',
                    'description' => 'Теги чатов',
                ],

                // Stop
                [
                    'name' => 'telegram.stop.index',
                    'description' => 'Стоп-слова',
                ],
                [
                    'name' => 'telegram.stop.view',
                    'description' => 'Просмотр стоп-слова',
                ],
                [
                    'name' => 'telegram.stop.create',
                    'description' => 'Создание стоп-слова',
                ],
                [
                    'name' => 'telegram.stop.update',
                    'description' => 'Редактирование стоп-слова',
                ],
                [
                    'name' => 'telegram.stop.delete',
                    'description' => 'Удаление стоп-слова',
                ],
                [
                    'name' => 'telegram.atop.editable',
                    'description' => 'Редактирование поля',
                ],
                [
                    'name' => 'telegram.stop.admin',
                    'description' => 'Администрирование стоп-слов',
                ],
            ],
        ],
		
		[
			'name' => 'Client.Telegram',
			'description' => '[Client] Telegram',
			'roles' => [
				'Admin',
			],
			'permissions' => [
				
				// Default
				[
					'name' => 'client.telegram.default.hook-set',
					'description' => 'Установка хука',
				],
				[
					'name' => 'client.telegram.default.hook-unset',
					'description' => 'Удаление хука',
				],
				
				// Chats
				[
					'name' => 'client.telegram.chat.index',
					'description' => 'Чаты',
				],
				[
					'name' => 'client.telegram.chat.view',
					'description' => 'Просмотр чата',
				],
				[
					'name' => 'client.telegram.chat.create',
					'description' => 'Создание чата',
				],
				[
					'name' => 'client.telegram.chat.update',
					'description' => 'Редактирование чата',
				],
				[
					'name' => 'client.telegram.chat.delete',
					'description' => 'Удаление чата',
				],
				[
					'name' => 'client.telegram.chat.editable',
					'description' => 'Редактирование поля',
				],
			],
		],
		
		[
			'name' => 'Client.Telegram.User',
			'description' => '[Client] Telegram для пользователя',
			'roles' => [
				'Admin',
				'User',
			],
			'permissions' => [
				
				// Default
				[
					'name' => 'client.telegram.default.link',
					'description' => 'Подключение пользователя',
				],
			],
		],
	];
}