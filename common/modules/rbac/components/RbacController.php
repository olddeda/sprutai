<?php
namespace common\modules\rbac\components;

use Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\ArrayHelper;
use yii\helpers\Console;
use yii\helpers\FileHelper;

class RbacController extends Controller
{
	/** @var \common\modules\rbac\components\DbManager */
	public $auth;

	/** @inheritdoc */
	public $defaultAction = 'add';

	/** @var array task  */
	public $task = [];

	/** @var array tasks  */
	public $tasks = [];

	/** @var array permission */
	public $permissions = [];

	/**
	 * @inheritdoc
	 */
	public function init() {
		parent::init();
		$this->auth = Yii::$app->getAuthManager();
		
		// Find local task and merge
		$fileLocal = dirname((new \ReflectionClass(get_called_class()))->getFileName()).DIRECTORY_SEPARATOR.'rbac-local.php';
		if (file_exists($fileLocal)) {
			//$this->tasks = ArrayHelper::merge($this->tasks, require($fileLocal));
			//var_dump($this->tasks);
			//die;
		}
	}

	/**
	 * Add RBAC.
	 */
	public function actionAdd() {

		if (count($this->tasks)) {
			echo PHP_EOL;
			foreach ($this->tasks as $task) {
				if (count($task) && isset($task['permissions'])) {
					$this->_taskAdd($task);
				}
			}
		}
		
		Yii::$app->cache->flush();
		Yii::$app->cacheFile->flush();

		return ExitCode::OK;
	}

	/**
	 * Remove RBAC.
	 */
	public function actionRemove() {

		if (count($this->tasks)) {
			echo PHP_EOL;
			foreach ($this->tasks as $task) {
				if (count($task) && isset($task['permissions'])) {
					$this->_taskRemove($task);
					echo PHP_EOL;
				}
			}
		}

		return ExitCode::OK;
	}

	/**
	 * Task add
	 *
	 * @param $task
	 *
	 * @throws \yii\base\Exception
	 */
	private function _taskAdd($task) {

		// Get superadmin
		$admin = $this->auth->getRole('SuperAdmin');

		// Check and create or update task
		$taskItem = $this->_createOrUpdateItem($task, 'task');

		// Get permissions
		$permissions = (isset($task['permissions'])) ? $task['permissions'] : array();

		// Check and create or update routes
		foreach ($permissions as $item) {
			$permission = $this->_createOrUpdateItem($item, 'permission');
			if (!$this->auth->hasChild($taskItem, $permission))
				$this->auth->addChild($taskItem, $permission);
		}

		// Assign task to role
		if (!$this->auth->hasChild($admin, $taskItem)) {
			$this->auth->addChild($admin, $taskItem);

			echo $this->ansiFormat('Assign task to role ', Console::FG_YELLOW);
			echo $this->ansiFormat($admin->name, Console::FG_GREEN);
			echo PHP_EOL;
		}

		// Add roles
		if (isset($task['roles'])) {
			foreach ($task['roles'] as $val) {
				if ($this->auth->getRole($val)) {
					$role = $this->auth->getRole($val);

					if (!$this->auth->hasChild($role, $taskItem)) {
						$this->auth->addChild($role, $taskItem);

						echo $this->ansiFormat('Assign task to role ', Console::FG_YELLOW);
						echo $this->ansiFormat($role->name, Console::FG_GREEN);
						echo PHP_EOL;
					}
				}
			}
		}
	}

	/**
	 * Task remove
	 * @param $task
	 */
	private function _taskRemove($task) {

		// Get superadmin
		$admin = $this->auth->getRole('SuperAdmin');

		// Check and create or update task
		$taskItem = $this->auth->getTask($task['name']);

		// Get permissions
		$permissions = (isset($task['permissions'])) ? $task['permissions'] : array();

		// Check and create or update routes
		foreach ($permissions as $item) {
			$permission = $this->auth->getPermission($item['name']);
			if ($this->auth->hasChild($taskItem, $permission)) {
				$this->auth->removeChild($taskItem, $permission);

				echo $this->ansiFormat('Remove permission: ', Console::FG_YELLOW);
				echo $this->ansiFormat($item['name'], Console::FG_GREEN);
				if (isset($item['description']))
					echo $this->ansiFormat(' ('.$item['description'].')', Console::FG_GREEN);
				echo PHP_EOL;
			}
		}

		// Remove from roles
		if (isset($task['roles'])) {
			foreach ($task['roles'] as $val) {
				if ($this->auth->getRole($val)) {
					$role = $this->auth->getRole($val);

					if ($this->auth->hasChild($role, $taskItem)) {
						$this->auth->removeChild($role, $taskItem);

						echo $this->ansiFormat('Revoke task ', Console::FG_YELLOW);
						echo $this->ansiFormat($task['name'], Console::FG_GREEN);
						if (isset($data['description']))
							echo $this->ansiFormat(' ('.$task['description'].')', Console::FG_GREEN);
						echo $this->ansiFormat(' from role ', Console::FG_YELLOW);
						echo $this->ansiFormat($role->name, Console::FG_GREEN);
						echo PHP_EOL;
					}
				}
			}
		}

		if ($taskItem !== null) {
			$this->auth->remove($taskItem);

			echo $this->ansiFormat('Remove task: ', Console::FG_YELLOW);
			echo $this->ansiFormat($task['name'], Console::FG_GREEN);
			if (isset($task['description']))
				echo $this->ansiFormat(' ('.$task['description'].')', Console::FG_GREEN);
			echo PHP_EOL;
		}

		return;
	}

	/**
	 * Create or update permission
	 *
	 * @param $data
	 * @param string $type
	 *
	 * @return Task|mixed|\yii\rbac\Item|\yii\rbac\Permission|null
	 * @throws \Exception
	 */
	private function _createOrUpdateItem($data, $type = 'task') {

		$isCreated = false;

		$item = null;
		if ($type == 'task')
			$item = $this->_getTask($data, $isCreated);
		else if ($type == 'permission')
			$item = $this->_getPermisson($data, $isCreated);

		if ($item !== null) {
			if (isset($data['description']))
				$item->description = $data['description'];
			if (isset($data['rule']))
				$item->ruleName = $data['rule'];

			if ($isCreated)
				$this->auth->add($item);
			else
				$this->auth->update($data['name'], $item);

			echo $this->ansiFormat(($isCreated ? 'Created' : 'Updated').' '.$type.': ', Console::FG_YELLOW);
			echo $this->ansiFormat($data['name'], Console::FG_GREEN);
			if (isset($data['description']))
				echo $this->ansiFormat(' ('.$data['description'].')', Console::FG_GREEN);
			echo PHP_EOL;
		}

		return $item;
	}

	/**
	 * Get task
	 * @param $data
	 * @param $isCreated
	 *
	 * @return Task|mixed|null|\yii\rbac\Item
	 */
	private function _getTask($data, &$isCreated) {
		$item = $this->auth->getTask($data['name']);
		if ($item === null) {
			$item = $this->auth->createTask($data['name']);
			$isCreated = true;
		}
		return $item;
	}

	/**
	 * Get permission
	 * @param $data
	 * @param $isCreated
	 *
	 * @return null|\yii\rbac\Item|\yii\rbac\Permission
	 */
	private function _getPermisson(&$data, &$isCreated) {
		$item = $this->auth->getPermission($data['name']);
		if ($item === null) {
			$item = $this->auth->createPermission($data['name']);
			$isCreated = true;
		}
		return $item;
	}
}