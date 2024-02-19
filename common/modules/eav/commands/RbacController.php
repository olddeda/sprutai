<?php
namespace common\modules\eav\commands;

use common\modules\rbac\components\RbacController as BaseController;

/**
 * Task runner commands for EAV rbac.
 *
 * @package common\modules\eav\commands
 */
class RbacController extends BaseController
{
	
	/**
	 * @var array tasks
	 */
	public $tasks = [
		[
			'name' => 'Client.EAV',
			'description' => '[Client] EAV',
			'roles' => ['Admin'],
			'permissions' => [
				
				// Default
				[
					'name' => 'client.eav.default.index',
					'description' => 'EAV',
				],
				
				// Attribute
				[
					'name' => 'client.eav.attribute.index',
					'description' => 'Список аттрибутов',
				],
				[
					'name' => 'client.eav.attribute.view',
					'description' => 'Просмотр аттрибута',
				],
				[
					'name' => 'client.eav.attribute.create',
					'description' => 'Создание аттрибута',
				],
				[
					'name' => 'client.eav.attribute.update',
					'description' => 'Редактирование аттрибута',
				],
				[
					'name' => 'client.eav.attribute.delete',
					'description' => 'Удаление аттрибута',
				],
				[
					'name' => 'client.eav.attribute.editable',
					'description' => 'Редактирование поля',
				],
				
				// Ajax
				[
					'name' => 'client.eav.ajax.index',
					'description' => 'Список аттрибутов',
				],
				[
					'name' => 'client.eav.ajax.save',
					'description' => 'Сохранение аттрибута',
				],
			],
		],
	];
}