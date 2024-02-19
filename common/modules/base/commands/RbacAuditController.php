<?php

namespace common\modules\base\commands;

use Yii;

use common\modules\rbac\components\RbacController as BaseController;

/**
 * Task runner commands for Rbac rbac.
 *
 * @package common\modules\base\commands
 */
class RbacAuditController extends BaseController
{
	/**
	 * @var array tasks
	 */
	public $tasks = [
		
		// Client
		[
			'name' => 'Client.Audit',
			'description' => '[Client] Audit',
			'roles' => ['Admin'],
			'permissions' => [
				
				// Main
				[
					'name' => 'client.audit.default.index',
					'description' => 'Аудит',
				],
				
				// Entries
				[
					'name' => 'client.audit.entry.index',
					'description' => 'Список записей',
				],
				[
					'name' => 'client.audit.entry.view',
					'description' => 'Просмотр записи',
				],
				
				// Trails
				[
					'name' => 'client.audit.trail.index',
					'description' => 'Список изменений',
				],
				[
					'name' => 'client.audit.trail.view',
					'description' => 'Просмотр изменения',
				],
				
				// Errors
				[
					'name' => 'client.audit.error.index',
					'description' => 'Список ошибок',
				],
				[
					'name' => 'client.audit.error.view',
					'description' => 'Просмотр ошибки',
				],
				
				// Javascript
				[
					'name' => 'client.audit.javascript.index',
					'description' => 'Список ошибок javascript',
				],
				[
					'name' => 'client.audit.javascript.view',
					'description' => 'Просмотр ошибки javascript',
				],
				
				// Mails
				[
					'name' => 'client.audit.mail.index',
					'description' => 'Список писем',
				],
				[
					'name' => 'client.audit.mail.view',
					'description' => 'Просмотр письма',
				],
			],
		],
	];
}