<?php

namespace common\modules\rbac\components;

use common\modules\base\components\Debug;
use yii\db\Query;
use yii\caching\Cache;
use yii\helpers\ArrayHelper;

use yii\rbac\DbManager as BaseDbManager;

/**
 * This Auth manager changes visibility and signature of some methods from \yii\rbac\DbManager.
 */
class DbManager extends BaseDbManager implements ManagerInterface
{
	/**
	 * @var string the ID of the cache application component that is used to cache rbac.
	 * Defaults to 'cache' which refers to the primary cache application component.
	 */
	public $cache = 'cache';
	/**
	 * @var integer Lifetime of cached data in seconds
	 */
	public $cacheDuration = 3600;
	/**
	 * @var string cache key name
	 */
	public $cacheKeyName = 'rbac';
	/**
	 * @var array php cache
	 */
	protected $cachedData = [];
	
	/**
	 * @inheritdoc
	 */
	public function getRole($name) {
		$cacheKey = 'role:'.$name;
		$cached = $this->getCache($cacheKey);
		
		if (empty($cached)) {
			$cached = parent::getRole($name);
			$this->setCache($cacheKey, $cached);
		}
		
		return $cached;
	}
	
	/**
	 * @inheritdoc
	 */
	public function getRolesByUser($userId) {
		$cacheKey = 'rolesByUser:'.$userId;
		$cached = $this->getCache($cacheKey);
		
		if (empty($cached)) {
			$cached = parent::getRolesByUser($userId);
			$this->setCache($cacheKey, $cached);
		}
		
		return $cached;
	}

	/**
	 * @inheritdoc
	 */
	public function checkAccess($userId, $permissionName, $params = []) {
		if (!empty($params))
			return parent::checkAccess($userId, $permissionName, $params);

		$cacheKey = 'checkAccess:'.$userId.':'.$permissionName;
		$cached = $this->getCache($cacheKey);
		if (empty($cached)) {
			$cached = parent::checkAccess($userId, $permissionName);
			$this->setCache($cacheKey, $cached);
		}

		return $cached;
	}

	/**
	 * @inheritdoc
	 */
	protected function checkAccessRecursive($user, $itemName, $params, $assignments) {
		$cacheKey = 'checkAccessRecursive:'.$user.':'.$itemName;
		if (!empty($params))
			$cacheKey .= ':' . current($params)->primaryKey;
		$cached = $this->getCache($cacheKey);
		if (empty($cached)) {
			$cached = parent::checkAccessRecursive($user, $itemName, $params, $assignments);
			$this->setCache($cacheKey, $cached);
		}

		return $cached;
	}

	/**
	 * Set a value in cache
	 *
	 * @param $key
	 * @param $value
	 *
	 * @return mixed
	 */
	protected function setCache($key, $value) {
		$this->cachedData = $this->resolveCacheComponent()->get($this->cacheKeyName);
		if (empty($this->cachedData))
			$this->cachedData = [];
		$this->cachedData[$key] = $value;

		return $this->resolveCacheComponent()->set($this->cacheKeyName, $this->cachedData, $this->cacheDuration);
	}

	/**
	 * Get cached value
	 *
	 * @param $key
	 *
	 * @return mixed
	 */
	protected function getCache($key) {
		$cached = ArrayHelper::getValue($this->cachedData, $key);
		if (!isset($cached)) {
			$cacheData = $this->resolveCacheComponent()->get($this->cacheKeyName);
			$cached = $this->cachedData[$key] = ArrayHelper::getValue($cacheData, $key);
		}

		return $cached;
	}

	/**
	 * Get cached value
	 *
	 * @param $key
	 *
	 * @return mixed
	 */
	public function deleteAllCache() {
		return $this->resolveCacheComponent()->delete($this->cacheKeyName);
	}

	/**
	 * Returns cache component configured as in cacheId
	 * @return Cache
	 */
	protected function resolveCacheComponent() {
		if ($this->cache instanceof Cache)
			return $this->cache;
		return Yii::$app->get($this->cache);
	}

	/**
	 * @inheritdoc
	 */
	public function getItem($name) {
		$cacheKey = 'Item:' . $name;
		$cached = $this->getCache($cacheKey);
		if (empty($cached)) {
			$cached = parent::getItem($name);
			$this->setCache($cacheKey, $cached);
		}

		return $cached;
	}

	/**
	 * @inheritdoc
	 */
	public function getAssignments($userId) {
		if (empty($userId))
			return parent::getAssignments($userId);

		$cacheKey = 'Assignments:' . $userId;
		$cached = $this->getCache($cacheKey);
		if (empty($cached)) {
			$cached = parent::getAssignments($userId);
			$this->setCache($cacheKey, $cached);
		}

		return $cached;
	}

    /**
     * @param int|null $type If null will return all auth items.
     * @param array $excludeItems Items that should be excluded from result array.
     * @return array
     */
    public function getItems($type = null, $excludeItems = []) {

		// Create query
        $query = (new Query())->from($this->itemTable);

		// Add parent
		$query->leftJoin($this->itemChildTable, $this->itemChildTable.'.child = '.$this->itemTable.'.name');

        if ($type !== null)
            $query->where(['type' => $type]);
		else
            $query->orderBy('type');

		// Exclude items
		if ($excludeItems) {
			$query->andWhere(['not in', 'name', $excludeItems]);
		}
        //foreach ($excludeItems as $name)
        //    $query->andWhere('name != :item', ['item' => $name]);

        $items = [];
        foreach ($query->all($this->db) as $row)
            $items[$row['name']] = $this->populateItem($row);

        return $items;
    }

    /**
     * Returns both roles and permissions assigned to user.
     *
     * @param integer $userId
     * @return array
     */
    public function getItemsByUser($userId) {
        if (empty($userId))
            return [];

        $query = (new Query)->select('b.*')->from([
			'a' => $this->assignmentTable,
			'b' => $this->itemTable
		])->where('{{a}}.[[item_name]]={{b}}.[[name]]')->andWhere([
			'a.user_id' => (string)$userId
		]);

        $roles = [];
        foreach ($query->all($this->db) as $row)
            $roles[$row['name']] = $this->populateItem($row);
        return $roles;
    }

	/**
	 * @inheritdoc
	 */
	public function createTask($name) {
		$role = new Task;
		$role->name = $name;
		return $role;
	}

	/**
	 * @inheritdoc
	 */
	public function getTask($name) {
		return $this->getItem($name);
	}

	/**
	 * @inheritdoc
	 */
	public function getTasks() {
		return $this->getItems(Item::TYPE_TASK);
	}

	/**
	 * Populates an auth item with the data fetched from database
	 * @param array $row the data from the auth item table
	 * @return Item the populated auth item instance (either Role or Permission)
	 */
	protected function populateItem($row) {

		// Get class
		$class = Permission::class;
		if ($row['type'] == Item::TYPE_ROLE)
			$class = Role::class;
		else if ($row['type'] == Item::TYPE_TASK)
			$class = Task::class;

		// Add data
		if (!isset($row['data']) || ($data = @unserialize($row['data'])) === false) {
			$data = null;
		}

		// Add parent
		if (!isset($row['parent']))
			$row['parent'] = null;

		return new $class([
			'parent' => $row['parent'],
			'name' => $row['name'],
			'type' => $row['type'],
			'description' => $row['description'],
			'ruleName' => $row['rule_name'],
			'data' => $data,
			'createdAt' => $row['created_at'],
			'updatedAt' => $row['updated_at'],
		]);
	}
}