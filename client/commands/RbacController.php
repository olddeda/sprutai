<?php
namespace client\commands;

use Yii;

use common\modules\rbac\components\RbacController as BaseController;

/**
 * Task runner commands for rbac.
 *
 * @package client\commands
 */
class RbacController extends BaseController
{
	/**
	 * @var array tasks
	 */
	public $tasks = [
		[
			'name' => 'Client.Content.App',
			'description' => '[Client] Контент',
			'roles' => [
				'User',
				'Admin'
			],
			'permissions' => [
				
				// Projects
				[
					'name' => 'client.projects.index',
					'description' => 'Список проектов',
				],
				[
					'name' => 'client.projects.view',
					'description' => 'Просмотр проекта',
				],
				[
					'name' => 'client.projects.event',
					'description' => 'Собыитя проекта',
				],
				[
					'name' => 'client.projects.payment',
					'description' => 'Платежи проекта',
				],
				[
					'name' => 'client.projects.comment',
					'description' => 'Комментарии проекта проекта',
				],
				
				// Favorite
				[
					'name' => 'client.favorites.index',
					'description' => 'Избранное статьи',
				],
				[
					'name' => 'client.favorites.news',
					'description' => 'Избранное новости',
				],
				[
					'name' => 'client.favorites.blog',
					'description' => 'Избранное блоги',
				],
				[
					'name' => 'client.favorites.project',
					'description' => 'Избранное проекты',
				],
				[
					'name' => 'client.favorites.plugin',
					'description' => 'Избранное плагины',
				],
				[
					'name' => 'client.favorites.author',
					'description' => 'Избранные авторы',
				],
				[
					'name' => 'client.favorites.tag',
					'description' => 'Избранные теги',
				],
				[
					'name' => 'client.favorites.company',
					'description' => 'Избранные компании',
				],
				
			],
		],
	];
}