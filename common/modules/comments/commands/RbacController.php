<?php

namespace common\modules\comments\commands;

use Yii;

use common\modules\rbac\components\RbacController as BaseController;

/**
 * Task runner commands for Content rbac.
 *
 * @package common\modules\content\commands
 */
class RbacController extends BaseController
{
	/**
	 * @var array tasks
	 */
	public $tasks = [
        [
            'name' => 'Comment',
            'description' => 'Комментарии',
            'roles' => ['Admin', 'User'],
            'permissions' => [

                // Default
                [
                    'name' => 'comment.default.index',
                    'description' => 'Список комментариев',
                ],
                [
                    'name' => 'comment.default.create',
                    'description' => 'Добавление комментария',
                ],
                [
                    'name' => 'comment.default.update',
                    'description' => 'Редактирование комментария',
                ],
                [
                    'name' => 'comment.default.delete',
                    'description' => 'Удаление комментария',
                ]
            ],
        ],

		[
			'name' => 'Client.Comments',
			'description' => '[Client] Комментарии',
			'roles' => ['Admin'],
			'permissions' => [
				
				// Default
				[
					'name' => 'client.comments.default.index',
					'description' => 'Статистика',
				],
				[
					'name' => 'client.comments.default.create',
					'description' => 'Добавление комментария',
				],
				[
					'name' => 'client.comments.default.delete',
					'description' => 'Удаление комментария',
				],

				// Manage
				[
					'name' => 'client.comments.manage.index',
					'description' => 'Список комментариев',
				],
				[
					'name' => 'client.comments.manage.update',
					'description' => 'Редактирование комментария',
				],
				[
					'name' => 'client.comments.manage.delete',
					'description' => 'Удаление комментария',
				],
				[
					'name' => 'client.comments.manage.statistics',
					'description' => 'Статистика комментариев',
				],
			],
		],
		[
			'name' => 'Client.Comments.User',
			'description' => '[Client] Комментарии пользователя',
			'roles' => ['User'],
			'permissions' => [

				// Default
				[
					'name' => 'client.comments.default.create',
					'description' => 'Добавление комментария',
				],
				[
					'name' => 'client.comments.default.delete',
					'description' => 'Удаление комментария',
				],
			],
		],
	];
}