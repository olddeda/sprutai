<?php

namespace common\modules\media\commands;

use Yii;

use common\modules\rbac\components\RbacController as BaseController;

/**
 * Task runner commands for media rbac.
 *
 * @package common\modules\media\commands
 */
class RbacController extends BaseController
{
	/**
	 * @var array tasks
	 */
	public $tasks = [

        [
            'name' => 'Media',
            'description' => 'Медиа',
            'roles' => ['Admin'],
            'permissions' => [

                // Upload
                [
                    'name' => 'media.upload.slim',
                    'description' => 'Поддержка Slim',
                ],
                [
                    'name' => 'media.upload.delete',
                    'description' => 'Удаление медиа',
                ],
            ]
        ],

        [
            'name' => 'Media.User',
            'description' => 'Медиа Пользователь',
            'roles' => ['User'],
            'permissions' => [

                // Upload
                [
                    'name' => 'media.upload.slim',
                    'description' => 'Поддержка Slim',
                ],
                [
                    'name' => 'media.upload.delete',
                    'description' => 'Удаление медиа',
                ],
            ]
        ],

		
		// Client
		[
			'name' => 'Client.Media',
			'description' => '[Client] Медиа',
			'roles' => ['Admin'],
			'permissions' => [
				
				// Default
				[
					'name' => 'client.media.default.index',
					'description' => 'Статистика',
				],
				[
					'name' => 'client.media.default.upload',
					'description' => 'Загрузка файла',
				],
				[
					'name' => 'client.media.default.upload-slim',
					'description' => 'Загрузка файла Slim',
				],
				[
					'name' => 'client.media.default.upload-multiple',
					'description' => 'Множественная загрузка файлов',
				],
				[
					'name' => 'client.media.default.upload-content-builder',
					'description' => 'Загрузка изображений ContentBuilder',
				],
				[
					'name' => 'client.media.default.upload-content-builder-large',
					'description' => 'Загрузка больших изображений ContentBuilder',
				],
				[
					'name' => 'client.media.default.upload-content-builder-slider',
					'description' => 'Загрузка изображений ContentBuilder для Slider',
				],
				[
					'name' => 'client.media.default.modal',
					'description' => 'Редактирование файла в модальном окне',
				],
				[
					'name' => 'client.media.default.delete',
					'description' => 'Удаление файла',
				],
				
				// Image
				[
					'name' => 'client.media.image.index',
					'description' => 'Список изображений',
				],
				[
					'name' => 'client.media.image.view',
					'description' => 'Просмотр изображения',
				],
				
				// Formats
				[
					'name' => 'client.media.format.index',
					'description' => 'Список форматов',
				],
				[
					'name' => 'client.media.format.view',
					'description' => 'Просмотр формата',
				],
				[
					'name' => 'client.media.format.create',
					'description' => 'Создание формата',
				],
				[
					'name' => 'client.media.format.update',
					'description' => 'Редактирование формата',
				],
				[
					'name' => 'client.media.format.delete',
					'description' => 'Удаление формата',
				],
			],
		],
		
		// Client
		[
			'name' => 'Client.Media.User',
			'description' => '[Client] Медиа для пользователя',
			'roles' => ['User'],
			'permissions' => [
				
				// Default
				[
					'name' => 'client.media.default.upload',
					'description' => 'Загрузка файла',
				],
				[
					'name' => 'client.media.default.upload-slim',
					'description' => 'Загрузка файла Slim',
				],
				[
					'name' => 'client.media.default.upload-multiple',
					'description' => 'Множественная загрузка файлов',
				],
				[
					'name' => 'client.media.default.upload-content-builder',
					'description' => 'Загрузка изображений ContentBuilder',
				],
				[
					'name' => 'client.media.default.upload-content-builder-large',
					'description' => 'Загрузка больших изображений ContentBuilder',
				],
				[
					'name' => 'client.media.default.upload-content-builder-slider',
					'description' => 'Загрузка изображений ContentBuilder для Slider',
				],
				[
					'name' => 'client.media.default.delete',
					'description' => 'Удаление файла',
				],
			],
		],
	];
}