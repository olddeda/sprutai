<?php
namespace common\modules\seo\commands;

use Yii;

use common\modules\rbac\components\RbacController as BaseController;

/**
 * Task runner commands for Store rbac.
 *
 * @package common\modules\store\commands
 */
class RbacController extends BaseController
{
	/**
	 * @var array tasks
	 */
	public $tasks = [
		
		// Client
		[
			'name' => 'Client.Seo',
			'description' => '[Seo] Seo',
			'roles' => [
				'Admin',
			],
			'permissions' => [
				
				
				// Module
				[
					'name' => 'client.seo.module.index',
					'description' => 'SEO модули',
				],
				[
					'name' => 'client.seo.module.update',
					'description' => 'Редактирование SEO модуля',
				],
			],
		],
	];
}