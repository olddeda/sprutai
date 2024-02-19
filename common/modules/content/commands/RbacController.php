<?php
namespace common\modules\content\commands;

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

        // Content
        [
            'name' => 'Content',
            'description' => 'Контент',
            'roles' => ['Admin'],
            'permissions' => [

                // Default
                [
                    'name' => 'content.default.index',
                    'description' => 'Список материалов',
                ],
                [
                    'name' => 'content.default.view',
                    'description' => 'Просмотр материала',
                ],
                [
                    'name' => 'content.default.create',
                    'description' => 'Создание материала',
                ],
                [
                    'name' => 'content.default.update',
                    'description' => 'Редактирование материала',
                ],
                [
                    'name' => 'content.default.delete',
                    'description' => 'Удаление материала',
                ],
                [
                    'name' => 'content.default.seo',
                    'description' => 'SEO',
                ],
                [
                    'name' => 'content.default.admin',
                    'description' => 'Администрирование',
                ],
                [
                    'name' => 'content.default.content-link',
                    'description' => 'Поиск контент линков',
                ],

                // History
                [
                    'name' => 'content.history.index',
                    'description' => 'Список истории материала',
                ],
                [
                    'name' => 'content.history.create',
                    'description' => 'Добавление истории материала',
                ],

                // Utils
                [
                    'name' => 'content.utils.youtube-info',
                    'description' => 'Получение информации youtube ролика',
                ],


                // Page
                [
                    'name' => 'content.page.index',
                    'description' => 'Список страниц',
                ],
                [
                    'name' => 'content.page.view',
                    'description' => 'Просмотр страницы',
                ],
                [
                    'name' => 'content.page.create',
                    'description' => 'Создание страницы',
                ],
                [
                    'name' => 'content.page.update',
                    'description' => 'Редактирование страницы',
                ],
                [
                    'name' => 'content.page.delete',
                    'description' => 'Удаление страницы',
                ],
                [
                    'name' => 'content.page.editable',
                    'description' => 'Редактирование поля',
                ],
                [
                    'name' => 'content.page.backup',
                    'description' => 'Бекап текста',
                ],

                // Article
                [
                    'name' => 'content.article.index',
                    'description' => 'Статьи',
                ],
                [
                    'name' => 'content.article.view',
                    'description' => 'Просмотр статьи',
                ],
                [
                    'name' => 'content.article.create',
                    'description' => 'Создание статьи',
                ],
                [
                    'name' => 'content.article.update',
                    'description' => 'Редактирование статьи',
                ],
                [
                    'name' => 'content.article.delete',
                    'description' => 'Удаление статьи',
                ],
                [
                    'name' => 'content.article.editable',
                    'description' => 'Редактирование поля',
                ],
                [
                    'name' => 'content.article.backup',
                    'description' => 'Бекап текста',
                ],

                // News
                [
                    'name' => 'content.news.index',
                    'description' => 'Новости',
                ],
                [
                    'name' => 'content.news.view',
                    'description' => 'Просмотр новости',
                ],
                [
                    'name' => 'content.news.create',
                    'description' => 'Создание новости',
                ],
                [
                    'name' => 'content.news.update',
                    'description' => 'Редактирование новости',
                ],
                [
                    'name' => 'content.news.delete',
                    'description' => 'Удаление новости',
                ],
                [
                    'name' => 'content.news.editable',
                    'description' => 'Редактирование поля',
                ],
                [
                    'name' => 'content.article.backup',
                    'description' => 'Бекап текста',
                ],

                // Blog
                [
                    'name' => 'content.blog.index',
                    'description' => 'Блоги',
                ],
                [
                    'name' => 'content.blog.view',
                    'description' => 'Просмотр блога',
                ],
                [
                    'name' => 'content.blog.create',
                    'description' => 'Создание блога',
                ],
                [
                    'name' => 'content.blog.update',
                    'description' => 'Редактирование блога',
                ],
                [
                    'name' => 'content.blog.delete',
                    'description' => 'Удаление блога',
                ],
                [
                    'name' => 'content.blog.editable',
                    'description' => 'Редактирование поля',
                ],
                [
                    'name' => 'content.blog.backup',
                    'description' => 'Бекап текста',
                ],

                // Shortcut
                [
                    'name' => 'content.shortcut.index',
                    'description' => 'Shortcuts',
                ],
                [
                    'name' => 'content.shortcut.view',
                    'description' => 'Просмотр Shortcut',
                ],
                [
                    'name' => 'content.shortcut.create',
                    'description' => 'Создание Shortcut',
                ],
                [
                    'name' => 'content.shortcut.update',
                    'description' => 'Редактирование Shortcut',
                ],
                [
                    'name' => 'content.shortcut.delete',
                    'description' => 'Удаление Shortcut',
                ],
                [
                    'name' => 'content.shortcut.editable',
                    'description' => 'Редактирование поля',
                ],
                [
                    'name' => 'content.shortcut.backup',
                    'description' => 'Бекап текста',
                ],

                // Contest
                [
                    'name' => 'content.contest.index',
                    'description' => 'Конкурсы',
                ],
            ],
        ],

        // Content.User
        [
            'name' => 'Content.User',
            'description' => 'Контент для пользователя',
            'roles' => ['User'],
            'permissions' => [

                // Main
                [
                    'name' => 'content.default.index',
                    'description' => 'Контент',
                ],
                [
                    'name' => 'content.default.content-link',
                    'description' => 'Поиск контент линков',
                ],

                // History
                [
                    'name' => 'content.history.index',
                    'description' => 'Список истории материала',
                ],
                [
                    'name' => 'content.history.create',
                    'description' => 'Добавление истории материала',
                ],

                // Article
                [
                    'name' => 'content.article.index',
                    'description' => 'Статьи',
                ],
                [
                    'name' => 'content.article.view',
                    'description' => 'Просмотр статьи',
                ],
                [
                    'name' => 'content.article.create',
                    'description' => 'Создание статьи',
                ],
                [
                    'name' => 'content.article.update',
                    'description' => 'Редактирование статьи',
                ],
                [
                    'name' => 'content.article.editable',
                    'description' => 'Редактирование поля',
                ],
                [
                    'name' => 'content.article.backup',
                    'description' => 'Бекап текста',
                ],

                // Blog
                [
                    'name' => 'content.blog.index',
                    'description' => 'Блоги',
                ],
                [
                    'name' => 'content.blog.view',
                    'description' => 'Просмотр блога',
                ],
                [
                    'name' => 'content.blog.create',
                    'description' => 'Создание блога',
                ],
                [
                    'name' => 'content.blog.update',
                    'description' => 'Редактирование блога',
                ],
                [
                    'name' => 'content.blog.editable',
                    'description' => 'Редактирование поля',
                ],
                [
                    'name' => 'content.blog.backup',
                    'description' => 'Бекап текста',
                ],

                // Shortcut
                [
                    'name' => 'content.shortcut.index',
                    'description' => 'Shortcuts',
                ],
                [
                    'name' => 'content.shortcut.view',
                    'description' => 'Просмотр Shortcut',
                ],
                [
                    'name' => 'content.shortcut.create',
                    'description' => 'Создание Shortcut',
                ],
                [
                    'name' => 'content.shortcut.update',
                    'description' => 'Редактирование Shortcut',
                ],
                [
                    'name' => 'content.shortcut.editable',
                    'description' => 'Редактирование поля',
                ],
                [
                    'name' => 'content.shortcut.backup',
                    'description' => 'Бекап текста',
                ],

                // News
                [
                    'name' => 'content.news.index',
                    'description' => 'Новости',
                ],
                [
                    'name' => 'content.news.view',
                    'description' => 'Просмотр новости',
                ],
                [
                    'name' => 'content.news.create',
                    'description' => 'Создание новости',
                ],
                [
                    'name' => 'content.news.update',
                    'description' => 'Редактирование новости',
                ],
                [
                    'name' => 'content.news.editable',
                    'description' => 'Редактирование поля',
                ],
                [
                    'name' => 'content.news.backup',
                    'description' => 'Бекап текста',
                ],
            ],
        ],

        // Content.Editor
        [
            'name' => 'Content.Editor',
            'description' => 'Контент для редактора',
            'roles' => ['Editor'],
            'permissions' => [

                // Main
                [
                    'name' => 'content.default.index',
                    'description' => 'Контент',
                ],
                [
                    'name' => 'content.default.content-link',
                    'description' => 'Поиск контент линков',
                ],

                // Article
                [
                    'name' => 'content.article.index',
                    'description' => 'Статьи',
                ],
                [
                    'name' => 'content.article.view',
                    'description' => 'Просмотр статьи',
                ],
                [
                    'name' => 'content.article.create',
                    'description' => 'Создание статьи',
                ],
                [
                    'name' => 'content.article.update',
                    'description' => 'Редактирование статьи',
                ],
                [
                    'name' => 'content.article.editable',
                    'description' => 'Редактирование поля',
                ],
                [
                    'name' => 'content.article.backup',
                    'description' => 'Бекап текста',
                ],

                // News
                [
                    'name' => 'content.news.index',
                    'description' => 'Новости',
                ],
                [
                    'name' => 'content.news.view',
                    'description' => 'Просмотр новости',
                ],
                [
                    'name' => 'content.news.create',
                    'description' => 'Создание новости',
                ],
                [
                    'name' => 'content.news.update',
                    'description' => 'Редактирование новости',
                ],
                [
                    'name' => 'content.news.editable',
                    'description' => 'Редактирование поля',
                ],
                [
                    'name' => 'content.news.backup',
                    'description' => 'Бекап текста',
                ],

                // Blog
                [
                    'name' => 'content.blog.index',
                    'description' => 'Блоги',
                ],
                [
                    'name' => 'content.blog.view',
                    'description' => 'Просмотр блога',
                ],
                [
                    'name' => 'content.blog.create',
                    'description' => 'Создание блога',
                ],
                [
                    'name' => 'content.blog.update',
                    'description' => 'Редактирование блога',
                ],
                [
                    'name' => 'content.blog.editable',
                    'description' => 'Редактирование поля',
                ],
                [
                    'name' => 'content.blog.backup',
                    'description' => 'Бекап текста',
                ],

                // Shortcut
                [
                    'name' => 'content.shortcut.index',
                    'description' => 'Shortcuts',
                ],
                [
                    'name' => 'content.shortcut.view',
                    'description' => 'Просмотр Shortcut',
                ],
                [
                    'name' => 'content.shortcut.create',
                    'description' => 'Создание Shortcut',
                ],
                [
                    'name' => 'content.shortcut.update',
                    'description' => 'Редактирование Shortcut',
                ],
                [
                    'name' => 'content.shortcut.editable',
                    'description' => 'Редактирование поля',
                ],
                [
                    'name' => 'content.shortcut.backup',
                    'description' => 'Бекап текста',
                ],

                // Contest
                [
                    'name' => 'content.contest.index',
                    'description' => 'Конкурсы',
                ],
            ],
        ],
		
		// Client.Content
		[
			'name' => 'Client.Content',
			'description' => '[Client] Контент',
			'roles' => ['Admin'],
			'permissions' => [
				
				// Main
				[
					'name' => 'client.content.default.index',
					'description' => 'Контент',
				],
                [
                    'name' => 'client.content.default.content-link',
                    'description' => 'Поиск контент линков',
                ],

				// Page
				[
					'name' => 'client.content.page.index',
					'description' => 'Список страниц',
				],
				[
					'name' => 'client.content.page.view',
					'description' => 'Просмотр страницы',
				],
				[
					'name' => 'client.content.page.create',
					'description' => 'Создание страницы',
				],
				[
					'name' => 'client.content.page.update',
					'description' => 'Редактирование страницы',
				],
				[
					'name' => 'client.content.page.delete',
					'description' => 'Удаление страницы',
				],
				[
					'name' => 'client.content.page.editable',
					'description' => 'Редактирование поля',
				],
                [
                    'name' => 'client.content.page.backup',
                    'description' => 'Бекап текста',
                ],
				
				// Article
				[
					'name' => 'client.content.article.index',
					'description' => 'Статьи',
				],
				[
					'name' => 'client.content.article.view',
					'description' => 'Просмотр статьи',
				],
				[
					'name' => 'client.content.article.create',
					'description' => 'Создание статьи',
				],
				[
					'name' => 'client.content.article.update',
					'description' => 'Редактирование статьи',
				],
				[
					'name' => 'client.content.article.delete',
					'description' => 'Удаление статьи',
				],
				[
					'name' => 'client.content.article.editable',
					'description' => 'Редактирование поля',
				],
                [
                    'name' => 'client.content.article.backup',
                    'description' => 'Бекап текста',
                ],
				
				// News
				[
					'name' => 'client.content.news.index',
					'description' => 'Новости',
				],
				[
					'name' => 'client.content.news.view',
					'description' => 'Просмотр новости',
				],
				[
					'name' => 'client.content.news.create',
					'description' => 'Создание новости',
				],
				[
					'name' => 'client.content.news.update',
					'description' => 'Редактирование новости',
				],
				[
					'name' => 'client.content.news.delete',
					'description' => 'Удаление новости',
				],
				[
					'name' => 'client.content.news.editable',
					'description' => 'Редактирование поля',
				],
                [
                    'name' => 'client.content.news.backup',
                    'description' => 'Бекап текста',
                ],
				
				// Blog
				[
					'name' => 'client.content.blog.index',
					'description' => 'Блоги',
				],
				[
					'name' => 'client.content.blog.view',
					'description' => 'Просмотр блога',
				],
				[
					'name' => 'client.content.blog.create',
					'description' => 'Создание блога',
				],
				[
					'name' => 'client.content.blog.update',
					'description' => 'Редактирование блога',
				],
				[
					'name' => 'client.content.blog.delete',
					'description' => 'Удаление блога',
				],
				[
					'name' => 'client.content.blog.editable',
					'description' => 'Редактирование поля',
				],
                [
                    'name' => 'client.content.blog.backup',
                    'description' => 'Бекап текста',
                ],
				
				// Shortcut
				[
					'name' => 'client.content.shortcut.index',
					'description' => 'Shortcuts',
				],
				[
					'name' => 'client.content.shortcut.view',
					'description' => 'Просмотр Shortcut',
				],
				[
					'name' => 'client.content.shortcut.create',
					'description' => 'Создание Shortcut',
				],
				[
					'name' => 'client.content.shortcut.update',
					'description' => 'Редактирование Shortcut',
				],
				[
					'name' => 'client.content.shortcut.delete',
					'description' => 'Удаление Shortcut',
				],
				[
					'name' => 'client.content.shortcut.editable',
					'description' => 'Редактирование поля',
				],
                [
                    'name' => 'client.content.shortcut.backup',
                    'description' => 'Бекап текста',
                ],
				
				// Contest
				[
					'name' => 'client.content.contest.index',
					'description' => 'Конкурсы',
				],
			],
		],
		
		// Client.Content.User
		[
			'name' => 'Client.Content.User',
			'description' => '[Client] Контент для пользователя',
			'roles' => ['User'],
			'permissions' => [
				
				// Main
				[
					'name' => 'client.content.default.index',
					'description' => 'Контент',
				],
                [
                    'name' => 'client.content.default.content-link',
                    'description' => 'Поиск контент линков',
                ],
				
				// Article
				[
					'name' => 'client.content.article.index',
					'description' => 'Статьи',
				],
				[
					'name' => 'client.content.article.view',
					'description' => 'Просмотр статьи',
				],
				[
					'name' => 'client.content.article.create',
					'description' => 'Создание статьи',
				],
				[
					'name' => 'client.content.article.update',
					'description' => 'Редактирование статьи',
				],
				[
					'name' => 'client.content.article.editable',
					'description' => 'Редактирование поля',
				],
                [
                    'name' => 'client.content.article.backup',
                    'description' => 'Бекап текста',
                ],
				
				// Blog
				[
					'name' => 'client.content.blog.index',
					'description' => 'Блоги',
				],
				[
					'name' => 'client.content.blog.view',
					'description' => 'Просмотр блога',
				],
				[
					'name' => 'client.content.blog.create',
					'description' => 'Создание блога',
				],
				[
					'name' => 'client.content.blog.update',
					'description' => 'Редактирование блога',
				],
				[
					'name' => 'client.content.blog.editable',
					'description' => 'Редактирование поля',
				],
                [
                    'name' => 'client.content.blog.backup',
                    'description' => 'Бекап текста',
                ],
				
				// Shortcut
				[
					'name' => 'client.content.shortcut.index',
					'description' => 'Shortcuts',
				],
				[
					'name' => 'client.content.shortcut.view',
					'description' => 'Просмотр Shortcut',
				],
				[
					'name' => 'client.content.shortcut.create',
					'description' => 'Создание Shortcut',
				],
				[
					'name' => 'client.content.shortcut.update',
					'description' => 'Редактирование Shortcut',
				],
				[
					'name' => 'client.content.shortcut.editable',
					'description' => 'Редактирование поля',
				],
                [
                    'name' => 'client.content.shortcut.backup',
                    'description' => 'Бекап текста',
                ],
				
				// News
				[
					'name' => 'client.content.news.index',
					'description' => 'Новости',
				],
				[
					'name' => 'client.content.news.view',
					'description' => 'Просмотр новости',
				],
				[
					'name' => 'client.content.news.create',
					'description' => 'Создание новости',
				],
				[
					'name' => 'client.content.news.update',
					'description' => 'Редактирование новости',
				],
				[
					'name' => 'client.content.news.editable',
					'description' => 'Редактирование поля',
				],
                [
                    'name' => 'client.content.news.backup',
                    'description' => 'Бекап текста',
                ],
			],
		],
		
		// Client.Content.Editor
		[
			'name' => 'Client.Content.Editor',
			'description' => '[Client] Контент для редактора',
			'roles' => ['Editor'],
			'permissions' => [
				
				// Main
				[
					'name' => 'client.content.default.index',
					'description' => 'Контент',
				],
                [
                    'name' => 'client.content.default.content-link',
                    'description' => 'Поиск контент линков',
                ],
				
				// Article
				[
					'name' => 'client.content.article.index',
					'description' => 'Статьи',
				],
				[
					'name' => 'client.content.article.view',
					'description' => 'Просмотр статьи',
				],
				[
					'name' => 'client.content.article.create',
					'description' => 'Создание статьи',
				],
				[
					'name' => 'client.content.article.update',
					'description' => 'Редактирование статьи',
				],
				[
					'name' => 'client.content.article.editable',
					'description' => 'Редактирование поля',
				],
                [
                    'name' => 'client.content.article.backup',
                    'description' => 'Бекап текста',
                ],
				
				// News
				[
					'name' => 'client.content.news.index',
					'description' => 'Новости',
				],
				[
					'name' => 'client.content.news.view',
					'description' => 'Просмотр новости',
				],
				[
					'name' => 'client.content.news.create',
					'description' => 'Создание новости',
				],
				[
					'name' => 'client.content.news.update',
					'description' => 'Редактирование новости',
				],
				[
					'name' => 'client.content.news.editable',
					'description' => 'Редактирование поля',
				],
                [
                    'name' => 'client.content.news.backup',
                    'description' => 'Бекап текста',
                ],
				
				// Blog
				[
					'name' => 'client.content.blog.index',
					'description' => 'Блоги',
				],
				[
					'name' => 'client.content.blog.view',
					'description' => 'Просмотр блога',
				],
				[
					'name' => 'client.content.blog.create',
					'description' => 'Создание блога',
				],
				[
					'name' => 'client.content.blog.update',
					'description' => 'Редактирование блога',
				],
				[
					'name' => 'client.content.blog.editable',
					'description' => 'Редактирование поля',
				],
                [
                    'name' => 'client.content.blog.backup',
                    'description' => 'Бекап текста',
                ],
				
				// Shortcut
				[
					'name' => 'client.content.shortcut.index',
					'description' => 'Shortcuts',
				],
				[
					'name' => 'client.content.shortcut.view',
					'description' => 'Просмотр Shortcut',
				],
				[
					'name' => 'client.content.shortcut.create',
					'description' => 'Создание Shortcut',
				],
				[
					'name' => 'client.content.shortcut.update',
					'description' => 'Редактирование Shortcut',
				],
				[
					'name' => 'client.content.shortcut.editable',
					'description' => 'Редактирование поля',
				],
                [
                    'name' => 'client.content.shortcut.backup',
                    'description' => 'Бекап текста',
                ],
				
				// Contest
				[
					'name' => 'client.content.contest.index',
					'description' => 'Конкурсы',
				],
			],
		],

        // Content.Video
        [
            'name' => 'Content.Video',
            'description' => 'Контент видео',
            'roles' => ['User'],
            'permissions' => [
                [
                    'name' => 'content.default.create',
                    'description' => 'Создание материала',
                ],
                [
                    'name' => 'content.default.update',
                    'description' => 'Редактирование материала',
                ],
                [
                    'name' => 'content.default.admin',
                    'description' => 'Администрирование',
                ],

                // Utils
                [
                    'name' => 'content.utils.youtube-info',
                    'description' => 'Получение информации для youtube ролика',
                ],
            ],
        ],
	];
}