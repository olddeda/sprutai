<?php
namespace common\modules\payment\commands;

use Yii;

use common\modules\rbac\components\RbacController as BaseController;

/**
 * Task runner commands for Payment rbac.
 *
 * @package common\modules\payment\commands
 */
class RbacController extends BaseController
{
	/**
	 * @var array tasks
	 */
	public $tasks = [
		
		// Client.Payment
		[
			'name' => 'Client.Payment',
			'description' => '[Client] Платежи',
			'roles' => ['Admin'],
			'permissions' => [
				
				// Default
				[
					'name' => 'client.payment.default.index',
					'description' => 'Платежи',
				],
				[
					'name' => 'client.payment.default.view',
					'description' => 'Просмотр платежа',
				],
				[
					'name' => 'client.payment.default.create',
					'description' => 'Создание платежа',
				],
				[
					'name' => 'client.payment.default.update',
					'description' => 'Редактирование платежа',
				],
				[
					'name' => 'client.payment.default.delete',
					'description' => 'Удаление платежа',
				],
				[
					'name' => 'client.payment.default.editable',
					'description' => 'Редактирование поля',
				],
				
				// Withdrawals
				[
					'name' => 'client.payment.withdrawal.index',
					'description' => 'Списания',
				],
				[
					'name' => 'client.payment.withdrawal.view',
					'description' => 'Просмотр списания',
				],
				[
					'name' => 'client.payment.withdrawal.create',
					'description' => 'Создание списания',
				],
				[
					'name' => 'client.payment.withdrawal.update',
					'description' => 'Редактирование списания',
				],
				[
					'name' => 'client.payment.withdrawal.delete',
					'description' => 'Удаление списания',
				],
				[
					'name' => 'client.payment.withdrawal.editable',
					'description' => 'Редактирование поля',
				],
				
				// Type
				[
					'name' => 'client.payment.type.index',
					'description' => 'Типы платежей',
				],
				[
					'name' => 'client.payment.type.view',
					'description' => 'Просмотр типа платежа',
				],
				[
					'name' => 'client.payment.type.create',
					'description' => 'Создание типа платежа',
				],
				[
					'name' => 'client.payment.type.update',
					'description' => 'Редактирование типа платежа',
				],
				[
					'name' => 'client.payment.type.delete',
					'description' => 'Удаление типа платежа',
				],
				[
					'name' => 'client.payment.type.editable',
					'description' => 'Редактирование поля',
				],
			],
		],
	];
}