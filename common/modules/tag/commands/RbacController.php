<?php
namespace common\modules\tag\commands;

use Yii;

use common\modules\rbac\components\RbacController as BaseController;

/**
 * Task runner commands for Tag rbac.
 *
 * @package common\modules\tag\commands
 */
class RbacController extends BaseController
{
	/**
	 * @var array tasks
	 */
	public $tasks = [

        // Tag
        [
            'name' => 'Tag',
            'description' => '[Tag] Теги',
            'roles' => [
                'Admin',
            ],
            'permissions' => [

                // Default
                [
                    'name' => 'tag.default.index',
                    'description' => 'Список тегов',
                ],
                [
                    'name' => 'tag.default.view',
                    'description' => 'Просмотр тега',
                ],
                [
                    'name' => 'tag.default.create',
                    'description' => 'Создание тега',
                ],
                [
                    'name' => 'tag.default.update',
                    'description' => 'Редактирование тега',
                ],
                [
                    'name' => 'tag.default.delete',
                    'description' => 'Удаление тега',
                ],
                [
                    'name' => 'tag.default.search',
                    'description' => 'Поиск тега тега',
                ],
                [
                    'name' => 'tag.default.editable',
                    'description' => 'Редактирование одного поля',
                ],
            ],
        ],
		
		// Client
		[
			'name' => 'Client.Tag',
			'description' => '[Tag] Теги',
			'roles' => [
				'Admin',
			],
			'permissions' => [
				
				// Default
				[
					'name' => 'client.tag.default.index',
					'description' => 'Список тега',
				],
				[
					'name' => 'client.tag.default.view',
					'description' => 'Просмотр тега',
				],
				[
					'name' => 'client.tag.default.create',
					'description' => 'Создание тега',
				],
				[
					'name' => 'client.tag.default.update',
					'description' => 'Редактирование тега',
				],
				[
					'name' => 'client.tag.default.delete',
					'description' => 'Удаление тега',
				],
				[
					'name' => 'client.tag.default.search',
					'description' => 'Поиск тега тега',
				],
				[
					'name' => 'client.tag.default.editable',
					'description' => 'Редактирование одного поля',
				],
			],
		],
	];
}