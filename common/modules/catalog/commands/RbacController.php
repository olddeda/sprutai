<?php
namespace common\modules\catalog\commands;

use Yii;

use common\modules\rbac\components\RbacController as BaseController;

/**
 * Task runner commands for Catalog rbac.
 *
 * @package common\modules\catalog\commands
 */
class RbacController extends BaseController
{
	/**
	 * @var array tasks
	 */
	public $tasks = [

        // Catalog
        [
            'name' => 'Catalog',
            'description' => 'Каталог',
            'roles' => ['Admin'],
            'permissions' => [

                // Item
                [
                    'name' => 'catalog.item.index',
                    'description' => 'Список товаров',
                ],
                [
                    'name' => 'catalog.item.view',
                    'description' => 'Просмотр товара',
                ],
                [
                    'name' => 'catalog.item.create',
                    'description' => 'Создание товара',
                ],
                [
                    'name' => 'catalog.item.update',
                    'description' => 'Редактирование товара',
                ],
                [
                    'name' => 'catalog.item.delete',
                    'description' => 'Удаление товара',
                ],
                [
                    'name' => 'catalog.item.admin',
                    'description' => 'Управление товарами',
                ],
                [
                    'name' => 'catalog.item.editable',
                    'description' => 'Редактирование поля',
                ],
                [
                    'name' => 'catalog.item.seo',
                    'description' => 'SEO',
                ],
            ],
        ],

        // Catalog.User
        [
            'name' => 'Catalog.User',
            'description' => 'Каталог для пользователя',
            'roles' => ['Admin'],
            'permissions' => [

                // Item
                [
                    'name' => 'catalog.item.index',
                    'description' => 'Список товаров',
                ],
                [
                    'name' => 'catalog.item.view',
                    'description' => 'Просмотр товара',
                ],
            ],
        ],

        // Order
        [
            'name' => 'Catalog.Order',
            'description' => 'Каталог заказы',
            'roles' => ['Admin'],
            'permissions' => [

                // Order
                [
                    'name' => 'catalog.order.index',
                    'description' => 'Список заказов',
                ],
                [
                    'name' => 'catalog.order.view',
                    'description' => 'Просмотр заказа',
                ],
                [
                    'name' => 'catalog.order.create',
                    'description' => 'Создание заказа',
                ],
                [
                    'name' => 'catalog.order.update',
                    'description' => 'Редактирование заказа',
                ],
                [
                    'name' => 'catalog.order.delete',
                    'description' => 'Удаление заказа',
                ],
            ],
        ],

        // Field Group
        [
            'name' => 'Catalog.FieldGroup',
            'description' => 'Группы полей',
            'roles' => ['SuperAdmin', 'Admin'],
            'permissions' => [

                [
                    'name' => 'catalog.field-group.index',
                    'description' => 'Список групп полей',
                ],
                [
                    'name' => 'catalog.field-group.view',
                    'description' => 'Просмотр группы полей',
                ],
                [
                    'name' => 'catalog.field-group.create',
                    'description' => 'Создание группы полей',
                ],
                [
                    'name' => 'catalog.field-group.update',
                    'description' => 'Редактирование группы полей',
                ],
                [
                    'name' => 'catalog.field-group.delete',
                    'description' => 'Удаление группы полей',
                ],
            ],
        ],

        // Field
        [
            'name' => 'Catalog.Field',
            'description' => 'Поля',
            'roles' => ['SuperAdmin', 'Admin'],
            'permissions' => [

                [
                    'name' => 'catalog.field.index',
                    'description' => 'Список полей',
                ],
                [
                    'name' => 'catalog.field.view',
                    'description' => 'Просмотр поля',
                ],
                [
                    'name' => 'catalog.field.create',
                    'description' => 'Создание поля',
                ],
                [
                    'name' => 'catalog.field.update',
                    'description' => 'Редактирование поля',
                ],
                [
                    'name' => 'catalog.field.delete',
                    'description' => 'Удаление поля',
                ],
                [
                    'name' => 'catalog.field.swap',
                    'description' => 'Замена порядковых номеров местами у полей',
                ],
            ],
        ],
	];
}