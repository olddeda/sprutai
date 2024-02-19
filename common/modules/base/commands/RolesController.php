<?php

namespace common\modules\base\commands;

use Yii;
use yii\console\Controller;
use yii\helpers\Console;

class RolesController extends Controller
{
	/** @var \common\modules\rbac\components\DbManager */
	public $auth;

	/** @inheritdoc */
	public $defaultAction = 'add';

	/**
	 * @var array roles
	 */
	public $roles = [
		'SuperAdmin' => 'Супер администратор',
		'Admin' => 'Администратор',
		'User' => 'Пользователь',
		'Editor' => 'Редактор',
		'Guest' => 'Гость',
		'Company' => 'Компания',
	];

	/**
	 * @inheritdoc
	 */
	public function init() {
		parent::init();

		$this->auth = Yii::$app->getAuthManager();
	}

	/**
	 * Add roles
	 */
	public function actionAdd() {
		echo $this->ansiFormat('Create roles: ', Console::FG_YELLOW);

		$isCreated = false;
		foreach ($this->roles as $roleName => $roleDescription) {
			if (!$this->auth->getRole($roleName)) {
				if (!$isCreated) {
					echo PHP_EOL;
					$isCreated = true;
				}

				$role = $this->auth->createRole($roleName);
				$role->description = $roleDescription;
				$this->auth->add($role);

				echo $this->ansiFormat('Create role ', Console::FG_YELLOW);
				echo $this->ansiFormat($role->name.' ['.$role->description.']', Console::FG_GREEN);
				echo PHP_EOL;
			}
		}

		if (!$isCreated) {
			echo $this->ansiFormat('no roles to create', Console::FG_RED);
			echo PHP_EOL;
		}
	}

	/**
	 * Remove roles
	 */
	public function actionRemove() {
		echo $this->ansiFormat('Remove roles: ', Console::FG_YELLOW);

		$isRemoved = false;
		foreach ($this->roles as $roleName => $roleDescription) {
			if ($role = $this->auth->getRole($roleName)) {
				if (!$isRemoved) {
					echo PHP_EOL;
					$isRemoved = true;
				}

				$this->auth->remove($role);

				echo $this->ansiFormat('Remove role ', Console::FG_YELLOW);
				echo $this->ansiFormat($role->name.' ['.$role->description.']', Console::FG_GREEN);
				echo PHP_EOL;
			}
		}

		if (!$isRemoved) {
			echo $this->ansiFormat('no roles to remove', Console::FG_RED);
			echo PHP_EOL;
		}
	}
}