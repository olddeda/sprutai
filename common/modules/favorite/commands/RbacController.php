<?php
namespace common\modules\favorite\commands;

use Yii;

use common\modules\rbac\components\RbacController as BaseController;

/**
 * Task runner commands for Favorite rbac.
 *
 * @package common\modules\favorite\commands
 */
class RbacController extends BaseController
{
	/**
	 * @var array tasks
	 */
	public $tasks = [

        // Favorite Group
        [
            'name' => 'FavoriteGroup',
            'description' => 'Группы избранного',
            'roles' => ['Admin', 'User'],
            'permissions' => [

                // Item
                [
                    'name' => 'favorite.group.index',
                    'description' => 'Список групп избранного',
                ],
                [
                    'name' => 'favorite.group.create',
                    'description' => 'Создание группы избранного',
                ],
                [
                    'name' => 'favorite.group.update',
                    'description' => 'Редактирование группы избранного',
                ],
                [
                    'name' => 'favorite.group.delete',
                    'description' => 'Удаление группы избранного',
                ],
                [
                    'name' => 'favorite.group.favorite-set',
                    'description' => 'Добавление, удаление из избранного',
                ],
                [
                    'name' => 'favorite.group.favorite-clear',
                    'description' => 'Очистка избранного',
                ],
            ],
        ]
	];
}