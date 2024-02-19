<?php
namespace common\modules\notification\commands;

use Yii;

use common\modules\rbac\components\RbacController as BaseController;

/**
 * Task runner commands for Notification rbac.
 *
 * @package common\modules\notification\commands
 */
class RbacController extends BaseController
{
	/**
	 * @var array tasks
	 */
	public $tasks = [
		
		// Client.Notification
		[
			'name' => 'Client.Notification',
			'description' => '[Client] Уведомления',
			'roles' => ['Admin'],
			'permissions' => [
				
				// Main
				[
					'name' => 'client.notification.default.index',
					'description' => 'Уведомления',
				],
				[
					'name' => 'client.notification.default.send',
					'description' => 'Отправка уведомления',
				],
			],
		],
	];
}