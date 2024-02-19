<?php
namespace common\modules\dashboard\commands;

use Yii;

use common\modules\rbac\components\RbacController as BaseController;

/**
 * Task runner commands for Dashboard rbac.
 *
 * @package common\modules\dashboard\commands
 */
class RbacController extends BaseController
{
	/**
	 * @var array tasks
	 */
	public $tasks = [
        [
            'name' => 'Dashboard',
            'description' => 'Дашбоард',
            'roles' => ['Admin'],
            'permissions' => [

                // Main
                [
                    'name' => 'dashboard.default.index',
                    'description' => 'Дашбоард',
                ],
                [
                    'name' => 'dashboard.default.save',
                    'description' => 'Сохранение параметров',
                ],
            ],
        ],

		[
			'name' => 'Module.Dashboard',
			'description' => '[Module] Дашбоард',
			'roles' => ['Admin', 'Editor'],
			'permissions' => [
				
				// Main
				[
					'name' => 'client.dashboard.default.index',
					'description' => 'Дашбоард',
				],
				[
					'name' => 'client.dashboard.default.save',
					'description' => 'Сохранение параметров',
				],
			],
		],
	];
}