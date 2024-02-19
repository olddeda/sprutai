<?php
namespace common\modules\company\commands;

use common\modules\rbac\components\RbacController as BaseController;

/**
 * Task runner commands for Company rbac.
 *
 * @package common\modules\company\commands
 */
class RbacController extends BaseController
{
	
	/**
	 * @var array tasks
	 */
	public $tasks = [
		[
			'name' => 'Client.Company',
			'description' => '[Client] Компании',
			'roles' => ['Admin'],
			'permissions' => [
				
				// Default
				[
					'name' => 'client.company.default.index',
					'description' => 'Список компаний',
				],
				[
					'name' => 'client.company.default.view',
					'description' => 'Просмотр компании',
				],
				[
					'name' => 'client.company.default.create',
					'description' => 'Создание компании',
				],
				[
					'name' => 'client.company.default.update',
					'description' => 'Редактирование компании',
				],
				[
					'name' => 'client.company.default.delete',
					'description' => 'Удаление компании',
				],
				[
					'name' => 'client.company.default.portfolio',
					'description' => 'Портфолио компании',
				],
				[
					'name' => 'client.company.default.editable',
					'description' => 'Редактирование поля',
				],
				
				// Users
				[
					'name' => 'client.company.user.index',
					'description' => 'Список сотрудников',
				],
				[
					'name' => 'client.company.user.view',
					'description' => 'Просмотр сотрудника',
				],
				[
					'name' => 'client.company.user.create',
					'description' => 'Добавление сотрудника',
				],
				[
					'name' => 'client.company.user.update',
					'description' => 'Редактирование сотрудника',
				],
				[
					'name' => 'client.company.user.delete',
					'description' => 'Удаление сотрудника',
				],
				[
					'name' => 'client.company.user.editable',
					'description' => 'Редактирование поля',
				],
				[
					'name' => 'client.company.user.search',
					'description' => 'Поиск пользователей',
				],
				
				// Address
				[
					'name' => 'client.company.address.index',
					'description' => 'Список адресов',
				],
				[
					'name' => 'client.company.address.view',
					'description' => 'Просмотр адреса',
				],
				[
					'name' => 'client.company.address.create',
					'description' => 'Добавление адреса',
				],
				[
					'name' => 'client.company.address.update',
					'description' => 'Редактирование адреса',
				],
				[
					'name' => 'client.company.address.delete',
					'description' => 'Удаление адреса',
				],
				[
					'name' => 'client.company.address.editable',
					'description' => 'Редактирование поля',
				],
				
				// Discount
				[
					'name' => 'client.company.discount.index',
					'description' => 'Список скидок',
				],
				[
					'name' => 'client.company.discount.view',
					'description' => 'Просмотр скидок',
				],
				[
					'name' => 'client.company.discount.create',
					'description' => 'Добавление скидок',
				],
				[
					'name' => 'client.company.discount.update',
					'description' => 'Редактирование скидок',
				],
				[
					'name' => 'client.company.discount.delete',
					'description' => 'Удаление скидок',
				],
				[
					'name' => 'client.company.discount.editable',
					'description' => 'Редактирование поля',
				],
				
				// Article
				[
					'name' => 'client.company.article.index',
					'description' => 'Статьи',
				],
				[
					'name' => 'client.company.article.view',
					'description' => 'Просмотр статьи',
				],
				[
					'name' => 'client.company.article.create',
					'description' => 'Создание статьи',
				],
				[
					'name' => 'client.company.article.update',
					'description' => 'Редактирование статьи',
				],
				[
					'name' => 'client.company.article.delete',
					'description' => 'Удаление статьи',
				],
				[
					'name' => 'client.company.article.editable',
					'description' => 'Редактирование поля',
				],
				
				// News
				[
					'name' => 'client.company.news.index',
					'description' => 'Новости',
				],
				[
					'name' => 'client.company.news.view',
					'description' => 'Просмотр новости',
				],
				[
					'name' => 'client.company.news.create',
					'description' => 'Создание новости',
				],
				[
					'name' => 'client.company.news.update',
					'description' => 'Редактирование новости',
				],
				[
					'name' => 'client.company.news.delete',
					'description' => 'Удаление новости',
				],
				[
					'name' => 'client.company.news.editable',
					'description' => 'Редактирование поля',
				],
				
				// Blog
				[
					'name' => 'client.company.blog.index',
					'description' => 'Блоги',
				],
				[
					'name' => 'client.company.blog.view',
					'description' => 'Просмотр блога',
				],
				[
					'name' => 'client.company.blog.create',
					'description' => 'Создание блога',
				],
				[
					'name' => 'client.company.blog.update',
					'description' => 'Редактирование блога',
				],
				[
					'name' => 'client.company.blog.delete',
					'description' => 'Удаление блога',
				],
				[
					'name' => 'client.company.blog.editable',
					'description' => 'Редактирование поля',
				],
				
				// Portfolio
				[
					'name' => 'client.company.portfolio.index',
					'description' => 'Портфолио',
				],
				[
					'name' => 'client.company.portfolio.view',
					'description' => 'Просмотр работы',
				],
				[
					'name' => 'client.company.portfolio.create',
					'description' => 'Создание работы',
				],
				[
					'name' => 'client.company.portfolio.update',
					'description' => 'Редактирование работы',
				],
				[
					'name' => 'client.company.portfolio.delete',
					'description' => 'Удаление работы',
				],
				[
					'name' => 'client.company.portfolio.editable',
					'description' => 'Редактирование поля',
				],
			],
		],
		[
			'name' => 'Client.Company.Editor',
			'description' => '[Client] Компании для модераторов',
			'roles' => ['Editor'],
			'permissions' => [
				
				// Default
				[
					'name' => 'client.company.default.index',
					'description' => 'Список компаний',
				],
				[
					'name' => 'client.company.default.view',
					'description' => 'Просмотр компании',
				],
				[
					'name' => 'client.company.default.create',
					'description' => 'Создание компании',
				],
				[
					'name' => 'client.company.default.update',
					'description' => 'Редактирование компании',
				],
				[
					'name' => 'client.company.default.portfolio',
					'description' => 'Портфолио компании',
				],
				[
					'name' => 'client.company.default.editable',
					'description' => 'Редактирование поля',
				],
				
				// Users
				[
					'name' => 'client.company.user.index',
					'description' => 'Список сотрудников',
				],
				[
					'name' => 'client.company.user.view',
					'description' => 'Просмотр сотрудника',
				],
				[
					'name' => 'client.company.user.create',
					'description' => 'Добавление сотрудника',
				],
				[
					'name' => 'client.company.user.update',
					'description' => 'Редактирование сотрудника',
				],
				[
					'name' => 'client.company.user.delete',
					'description' => 'Удаление сотрудника',
				],
				[
					'name' => 'client.company.user.editable',
					'description' => 'Редактирование поля',
				],
				[
					'name' => 'client.company.user.search',
					'description' => 'Поиск пользователей',
				],
				
				// Address
				[
					'name' => 'client.company.address.index',
					'description' => 'Список адресов',
				],
				[
					'name' => 'client.company.address.view',
					'description' => 'Просмотр адреса',
				],
				[
					'name' => 'client.company.address.create',
					'description' => 'Добавление адреса',
				],
				[
					'name' => 'client.company.address.update',
					'description' => 'Редактирование адреса',
				],
				[
					'name' => 'client.company.address.delete',
					'description' => 'Удаление адреса',
				],
				[
					'name' => 'client.company.address.editable',
					'description' => 'Редактирование поля',
				],
				
				// Discount
				[
					'name' => 'client.company.discount.index',
					'description' => 'Список скидок',
				],
				[
					'name' => 'client.company.discount.view',
					'description' => 'Просмотр скидок',
				],
				[
					'name' => 'client.company.discount.create',
					'description' => 'Добавление скидок',
				],
				[
					'name' => 'client.company.discount.update',
					'description' => 'Редактирование скидок',
				],
				[
					'name' => 'client.company.discount.delete',
					'description' => 'Удаление скидок',
				],
				[
					'name' => 'client.company.discount.editable',
					'description' => 'Редактирование поля',
				],
				
				// Article
				[
					'name' => 'client.company.article.index',
					'description' => 'Статьи',
				],
				[
					'name' => 'client.company.article.view',
					'description' => 'Просмотр статьи',
				],
				[
					'name' => 'client.company.article.create',
					'description' => 'Создание статьи',
				],
				[
					'name' => 'client.company.article.update',
					'description' => 'Редактирование статьи',
				],
				[
					'name' => 'client.company.article.editable',
					'description' => 'Редактирование поля',
				],
				
				// News
				[
					'name' => 'client.company.news.index',
					'description' => 'Новости',
				],
				[
					'name' => 'client.company.news.view',
					'description' => 'Просмотр новости',
				],
				[
					'name' => 'client.company.news.create',
					'description' => 'Создание новости',
				],
				[
					'name' => 'client.company.news.update',
					'description' => 'Редактирование новости',
				],
				[
					'name' => 'client.company.news.editable',
					'description' => 'Редактирование поля',
				],
				
				// Blog
				[
					'name' => 'client.company.blog.index',
					'description' => 'Блоги',
				],
				[
					'name' => 'client.company.blog.view',
					'description' => 'Просмотр блога',
				],
				[
					'name' => 'client.company.blog.create',
					'description' => 'Создание блога',
				],
				[
					'name' => 'client.company.blog.update',
					'description' => 'Редактирование блога',
				],
				[
					'name' => 'client.company.blog.editable',
					'description' => 'Редактирование поля',
				],
				
				// Portfolio
				[
					'name' => 'client.company.portfolio.index',
					'description' => 'Портфолио',
				],
				[
					'name' => 'client.company.portfolio.view',
					'description' => 'Просмотр работы',
				],
				[
					'name' => 'client.company.portfolio.create',
					'description' => 'Создание работы',
				],
				[
					'name' => 'client.company.portfolio.update',
					'description' => 'Редактирование работы',
				],
				[
					'name' => 'client.company.portfolio.editable',
					'description' => 'Редактирование поля',
				],
			],
		],
		
		[
			'name' => 'Client.Company.Company',
			'description' => '[Client] Компании для пользователей',
			'roles' => ['Company'],
			'permissions' => [
				
				// Default
				[
					'name' => 'client.company.default.index',
					'description' => 'Список компаний',
				],
				[
					'name' => 'client.company.default.view',
					'description' => 'Просмотр компании',
				],
				[
					'name' => 'client.company.default.update',
					'description' => 'Редактирование компании',
				],
				[
					'name' => 'client.company.default.portfolio',
					'description' => 'Портфолио компании',
				],
				[
					'name' => 'client.company.default.editable',
					'description' => 'Редактирование поля',
				],
				
				// Address
				[
					'name' => 'client.company.address.index',
					'description' => 'Список адресов',
				],
				[
					'name' => 'client.company.address.view',
					'description' => 'Просмотр адреса',
				],
				[
					'name' => 'client.company.address.create',
					'description' => 'Добавление адреса',
				],
				[
					'name' => 'client.company.address.update',
					'description' => 'Редактирование адреса',
				],
				[
					'name' => 'client.company.address.delete',
					'description' => 'Удаление адреса',
				],
				[
					'name' => 'client.company.address.editable',
					'description' => 'Редактирование поля',
				],
				
				// Article
				[
					'name' => 'client.company.article.index',
					'description' => 'Статьи',
				],
				[
					'name' => 'client.company.article.view',
					'description' => 'Просмотр статьи',
				],
				[
					'name' => 'client.company.article.create',
					'description' => 'Создание статьи',
				],
				[
					'name' => 'client.company.article.update',
					'description' => 'Редактирование статьи',
				],
				[
					'name' => 'client.company.article.editable',
					'description' => 'Редактирование поля',
				],
				
				// News
				[
					'name' => 'client.company.news.index',
					'description' => 'Новости',
				],
				[
					'name' => 'client.company.news.view',
					'description' => 'Просмотр новости',
				],
				[
					'name' => 'client.company.news.create',
					'description' => 'Создание новости',
				],
				[
					'name' => 'client.company.news.update',
					'description' => 'Редактирование новости',
				],
				[
					'name' => 'client.company.news.editable',
					'description' => 'Редактирование поля',
				],
				
				// Blog
				[
					'name' => 'client.company.blog.index',
					'description' => 'Блоги',
				],
				[
					'name' => 'client.company.blog.view',
					'description' => 'Просмотр блога',
				],
				[
					'name' => 'client.company.blog.create',
					'description' => 'Создание блога',
				],
				[
					'name' => 'client.company.blog.update',
					'description' => 'Редактирование блога',
				],
				[
					'name' => 'client.company.blog.editable',
					'description' => 'Редактирование поля',
				],
				
				// Portfolio
				[
					'name' => 'client.company.portfolio.index',
					'description' => 'Портфолио',
				],
				[
					'name' => 'client.company.portfolio.view',
					'description' => 'Просмотр работы',
				],
				[
					'name' => 'client.company.portfolio.create',
					'description' => 'Создание работы',
				],
				[
					'name' => 'client.company.portfolio.update',
					'description' => 'Редактирование работы',
				],
				[
					'name' => 'client.company.portfolio.editable',
					'description' => 'Редактирование поля',
				],
			],
		],
	];
}