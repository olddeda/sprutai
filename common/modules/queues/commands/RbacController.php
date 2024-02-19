<?php
namespace common\modules\queues\commands;

use Yii;

use common\modules\rbac\components\RbacController as BaseController;

/**
 * Task runner commands for Queue rbac.
 *
 * @package common\modules\queque\commands
 */
class RbacController extends BaseController
{
	/**
	 * @var array tasks
	 */
	public $tasks = [
		[
			'name' => 'Client.Queues',
			'description' => '[Client] Очереди',
			'roles' => ['Admin'],
			'permissions' => [
				
				// Default
				[
					'name' => 'client.queues.default.index',
					'description' => 'Задачи',
				],
				[
					'name' => 'client.queues.default.run',
					'description' => 'Запуск задачи',
				],

				// Job
				[
					'name' => 'client.queues.job.index',
					'description' => 'Задачи',
				],
				[
					'name' => 'client.queues.job.view',
					'description' => 'Просмотр задачи',
				],
				[
					'name' => 'client.queues.job.view-details',
					'description' => 'Просмотр детали задачи',
				],
				[
					'name' => 'client.queues.job.view-context',
					'description' => 'Просмотр контекста задачи',
				],
				[
					'name' => 'client.queues.job.view-data',
					'description' => 'Просмотр данных задачи',
				],
				[
					'name' => 'client.queues.job.view-attempts',
					'description' => 'Просмотр попыток задачи',
				],
				[
					'name' => 'client.queues.job.push',
					'description' => 'Запуск повтора задачи',
				],
				[
					'name' => 'client.queues.job.stop',
					'description' => 'Завершение задачи',
				],
				
				// Workers
				[
					'name' => 'client.queues.worker.index',
					'description' => 'Процессы',
				],
				[
					'name' => 'client.queues.worker.stop',
					'description' => 'Остановка процесса',
				],
			],
		],
	];
}