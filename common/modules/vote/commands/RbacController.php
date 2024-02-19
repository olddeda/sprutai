<?php
namespace common\modules\vote\commands;

use Yii;

use common\modules\rbac\components\RbacController as BaseController;

/**
 * Task runner commands for Vote rbac.
 *
 * @package common\modules\vote\commands
 */
class RbacController extends BaseController
{
	/**
	 * @var array tasks
	 */
	public $tasks = [
        [
            'name' => 'Vote',
            'description' => 'Vote',
            'roles' => ['Admin', 'User'],
            'permissions' => [

                // Default
                [
                    'name' => 'vote.default.like',
                    'description' => 'Установка лайка или дизлайка',
                ],
            ],
        ],
	];
}