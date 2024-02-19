<?php

namespace common\modules\base\commands;

use Yii;

use common\modules\rbac\components\RbacController as BaseController;

/**
 * Task runner commands for Rbac rbac.
 *
 * @package common\modules\base\commands
 */
class RbacController extends BaseController
{
	/**
	 * @var array tasks
	 */
	public $tasks = [
		
		// Client
		[
			'name' => 'Client.Site',
			'description' => '[Client] Site',
			'roles' => ['Admin'],
			'permissions' => [
				
				// Default
				[
					'name' => 'client.site.index',
					'description' => 'Главная страница',
				],
			],
		],
		
		// Client User
		[
			'name' => 'Client.Site.User',
			'description' => '[Client] Site.User',
			'roles' => ['User'],
			'permissions' => [
				
				// Default
				[
					'name' => 'client.site.index',
					'description' => 'Главная страница',
				],
			],
		],
	];
}