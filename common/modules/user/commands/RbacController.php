<?php

namespace common\modules\user\commands;

use Yii;

use common\modules\rbac\components\RbacController as BaseController;

/**
 * Task runner commands for User rbac.
 *
 * @package common\modules\user\commands
 */
class RbacController extends BaseController
{
	/**
	 * @var array tasks
	 */
	public $tasks = [
		[
			'name' => 'Client.User',
			'description' => '[Client] Управление пользователями',
			'roles' => [
				'Admin'
			],
			'permissions' => [
				
				// Default
				[
					'name' => 'client.user.default.search',
					'description' => 'Поиск пользователей',
				],
				[
					'name' => 'client.user.default.search-json',
					'description' => 'Поиск пользователей',
				],
				
				// Admin
				[
					'name' => 'client.user.admin.index',
					'description' => 'Список пользователей',
				],
				[
					'name' => 'client.user.admin.create',
					'description' => 'Создание пользователя',
				],
				[
					'name' => 'client.user.admin.update',
					'description' => 'Редактирование пользователя',
				],
				[
					'name' => 'client.user.admin.update-profile',
					'description' => 'Редактирование данных пользователя',
				],
				[
					'name' => 'client.user.admin.log',
					'description' => 'Информацию пользователя',
				],
				[
					'name' => 'client.user.admin.info',
					'description' => 'Лог действий пользователя',
				],
				[
					'name' => 'client.user.admin.assignments',
					'description' => 'Права пользователя',
				],
				[
					'name' => 'client.user.admin.confirm',
					'description' => 'Активация пользователя пользователя',
				],
				[
					'name' => 'client.user.admin.block',
					'description' => 'Блокирование пользователя',
				],
				[
					'name' => 'client.user.admin.delete',
					'description' => 'Удаление пользователя',
				],
				[
					'name' => 'client.user.admin.signin',
					'description' => 'Авторизация под пользователем',
				],
				[
					'name' => 'client.user.admin.logout',
					'description' => 'Возврат в свой аккаунт',
				],
				
			],
		],
		
		[
			'name' => 'Client.User.Editor',
			'description' => '[Client] Управление пользователями модераторы',
			'roles' => [
				'Editor'
			],
			'permissions' => [
				
				// Default
				[
					'name' => 'client.user.default.search',
					'description' => 'Поиск пользователей',
				],
				[
					'name' => 'client.user.default.search-json',
					'description' => 'Поиск пользователей',
				],
				
				// Admin
				[
					'name' => 'client.user.admin.index',
					'description' => 'Список пользователей',
				],
				[
					'name' => 'client.user.admin.update',
					'description' => 'Редактирование пользователя',
				],
				[
					'name' => 'client.user.admin.update-profile',
					'description' => 'Редактирование данных пользователя',
				],
				[
					'name' => 'client.user.admin.signin',
					'description' => 'Авторизация под пользователем',
				],
				[
					'name' => 'client.user.admin.logout',
					'description' => 'Возврат в свой аккаунт',
				],
			
			],
		],
		
		[
			'name' => 'Client.User.Profile',
			'description' => '[Client] Управление аккунтом',
			'roles' => [
				'Admin',
				'User',
			],
			'permissions' => [
				
				// Admin
				[
					'name' => 'client.user.admin.logout',
					'description' => 'Возврат в свой аккаунт',
				],
				
				// Profile
				[
					'name' => 'client.user.profile.index',
					'description' => 'Просмотр своего профиля',
				],
				[
					'name' => 'client.user.profile.view',
					'description' => 'Просмотр профиля пользователя',
				],
				
				// Payment
				[
					'name' => 'client.user.payment.index',
					'description' => 'Просмотр списка платежей',
				],
				[
					'name' => 'client.user.payment.accruals',
					'description' => 'Просмотр списка начислений',
				],
				
				// Contents
				[
					'name' => 'client.user.content.article',
					'description' => 'Просмотр статей пользователя',
				],
				[
					'name' => 'client.user.content.news',
					'description' => 'Просмотр новостей пользователя',
				],
				[
					'name' => 'client.user.content.blog',
					'description' => 'Просмотр блогов пользователя',
				],
				[
					'name' => 'client.user.content.project',
					'description' => 'Просмотр проектов пользователя',
				],
				[
					'name' => 'client.user.content.plugin',
					'description' => 'Просмотр плагинов пользователя',
				],
			
			],
		],
	];
}