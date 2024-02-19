<?php
namespace common\modules\shortener\commands;

use Yii;

use common\modules\rbac\components\RbacController as BaseController;

/**
 * Task runner commands for Shortener rbac.
 *
 * @package common\modules\shortener\commands
 */
class RbacController extends BaseController
{
	/**
	 * @var array tasks
	 */
	public $tasks = [
		
		// Client.Shortener
		[
			'name' => 'Client.Shortener',
			'description' => '[Client] Короткие ссылки',
			'roles' => ['Admin'],
			'permissions' => [
				
				// Default
				[
					'name' => 'client.shortener.default.index',
					'description' => 'Список коротких ссылок',
				],
				[
					'name' => 'client.shortener.default.view',
					'description' => 'Просмотр коротких ссылок',
				],
				[
					'name' => 'client.shortener.default.create',
					'description' => 'Создание коротких ссылок',
				],
				[
					'name' => 'client.shortener.default.update',
					'description' => 'Редактирование коротких ссылок',
				],
				[
					'name' => 'client.shortener.default.delete',
					'description' => 'Удаление коротких ссылок',
				],
				[
					'name' => 'client.shortener.default.editable',
					'description' => 'Редактирование поля',
				],
                
                // Hit
                [
                    'name' => 'client.shortener.hit.index',
                    'description' => 'Список переходов по коротким ссылкам',
                ],
			],
		],
	];
}