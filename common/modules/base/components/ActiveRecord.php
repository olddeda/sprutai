<?php
namespace common\modules\base\components;

use Yii;
use yii\caching\DbDependency;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use yii\db\ActiveRecord as BaseActiveRecord;

use common\modules\base\behaviors\ModifiedByBehavior;
use common\modules\base\behaviors\TimestampBehavior;
use common\modules\base\helpers\enum\ModuleType;
use common\modules\base\helpers\enum\Status;
use common\modules\base\extensions\base\ModuleTrait;

use common\modules\statistics\models\Statistics;
use common\modules\statistics\helpers\enum\Type as TypeStatistics;

use common\modules\seo\models\SeoUri;

/**
 * Class ActiveRecord
 * @package common\modules\base\components
 *
 * @property int $id
 * @property int $created_by
 * @property int $updated_by
 * @property int $created_at
 * @property int $updated_at
 *
 * Defined relations:
 * @property \common\modules\statistics\models\Statistics $statistics
 */
class ActiveRecord extends BaseActiveRecord
{
	use ModuleTrait;
	
	// Scenarios
	const SCENARIO_TEMP	    = 'temp';
	const SCENARIO_CREATE	= 'create';
	const SCENARIO_UPDATE	= 'update';
	
	const SEQUENCE_STEP		= 10;

    /**
     * @var array
     */
	public $files;
	
	/**
     * Returns a list of behaviors that this component should behave as.
     *
     * @return array the behavior configurations.
     */
	public function behaviors() {
		$behaviors = [
			'timestamp' => [
				'class' => TimestampBehavior::class,
				'model' => $this,
			],
			'modifiedby' => [
				'class' => ModifiedByBehavior::class,
				'model' => $this,
			],
		];
		return $behaviors;
	}
	
	/**
	 * {@inheritdoc}
	 * @return ActiveQuery the newly created [[ActiveQuery]] instance.
	 */
	public static function find() {
		return Yii::createObject(ActiveQuery::className(), [get_called_class()]);
	}
	
	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getStatistics() {
		$class = get_called_class();
		return $this->hasOne(Statistics::class, ['module_id' => 'id'])->onCondition([
			Statistics::tableName().'.module_type' => $class::moduleType(),
		])->where([]);
	}
	
	
	/**
	 * Find one by id
	 *
	 * @param integer $id
	 * @param bool|false $except
	 * @param string $messageCategory
	 * @param array $relations
	 * @param bool|false $own
	 * @param array $skipFields
	 *
	 * @return array|null|\yii\db\ActiveRecord
	 */
	static public function findBy($id, $except = false, $messageCategory = 'base', $relations = [], $cache = false, $own = false, $conditions = null, $skipFields = [], $callback = null) {
		$class = get_called_class();
		return $class::findByColumn('id', $id, $except, $messageCategory, $relations, $cache, $own, $conditions, $skipFields, $callback);
	}
	
	/**
	 * Find one by column
	 * @param $column
	 * @param $value
	 * @param bool $except
	 * @param string $messageCategory
	 * @param array $relations
	 * @param bool $cache
	 * @param bool $own
	 * @param null $conditions
	 * @param array $skipFields
	 *
	 * @return mixed|null
	 * @throws NotFoundHttpException
	 * @throws \Throwable
	 */
	static public function findByColumn($column, $value, $except = false, $messageCategory = 'base', $relations = [], $cache = false, $own = false, $conditions = null, $skipFields = [], $callback = null) {
		$class = get_called_class();
		$model = new $class;
		$query = $class::find();
		
		$class::prepareQuery($query);

		if (!is_null($callback)) {
		    $query = call_user_func($callback, $query);
        }
		
		$query->andWhere($class::tableName().'.'.$column.' = :'.$column, [
			':'.$column => $value,
		]);
		
		if (is_array($relations) && count($relations))
			$query->joinWith($relations);
		
		// Add owner user condition
		if ($own) {
			if (!Yii::$app->user->isSuperAdmin && !Yii::$app->user->isAdmin && !Yii::$app->user->isEditor) {
				$userColumn = (in_array('user_id', $model->attributes())) ? 'user_id' : 'created_by';
				if (in_array($userColumn, $model->attributes())) {
					$query->andWhere($class::tableName().'.'.$userColumn.' = :'.$userColumn, [
						':'.$userColumn => Yii::$app->user->id,
					]);
				}
			}
		}
		
		if (!is_null($conditions)) {
            $query->andWhere($conditions);
        }
		
		$model = null;
		if ($cache) {
			$dependency = new DbDependency();
			$dependency->sql = 'SELECT MAX(updated_at) FROM '.$class::tableName();
			$model = self::getDb()->cache(function ($db) use($query) {
				return $query->one();
			}, Yii::$app->params['cache.duration'], $dependency);
		}
		else
			$model = $query->one();
		
		if ($model === null && $except)
			throw new NotFoundHttpException(Yii::t($messageCategory, 'error_not_exists'));
		
		return $model;
	}
	
	/**
	 * Find all by column
	 *
	 * @param $column
	 * @param $value
	 * @param array $relations
	 * @param bool $cache
	 * @param bool $own
	 *
	 * @return mixed|null
	 * @throws \Throwable
	 */
	static public function findAllByColumn($column, $value, $relations = [], $cache = false, $own = false) {
		$class = get_called_class();
		$model = new $class;
		$query = $class::find();
		
		$class::prepareQuery($query);
		
		if (is_array($value)) {
			$query->andWhere(['in', $class::tableName().'.'.$column, $value]);
		}
		else {
			$query->andWhere($class::tableName().'.'.$column.' = :'.$column, [
				':'.$column => $value,
			]);
		}
		
		if (count($relations))
			$query->joinWith($relations);
		
		// Add owner user condition
		if ($own) {
			if (!Yii::$app->user->isSuperAdmin && !Yii::$app->user->isAdmin) {
				$userColumn = (in_array('user_id', $model->attributes())) ? 'user_id' : 'created_by';
				$query->andWhere($class::tableName().'.'.$userColumn.' = :'.$userColumn, [
					':'.$userColumn => Yii::$app->user->id,
				]);
			}
		}
		
		$result = null;
		if ($cache) {
			$dependency = new DbDependency();
			$dependency->sql = 'SELECT MAX(updated_at) FROM '.$class::tableName();
			$result = self::getDb()->cache(function ($db) use($query) {
				return $query->all();
			}, Yii::$app->params['cache.duration'], $dependency);
		}
		else
			$result = $query->all();
		
		return $result;
	}
	
	/**
	 * Find own model
	 *
	 * @param integer $id
	 * @param bool|false $except
	 * @param string $messageCategory
	 * @param array $relations
	 *
	 * @return array|null|BaseActiveRecord
	 */
	static public function findOwn($id, $except = false, $messageCategory = 'base', $relations = [], $cache = true) {
		return self::findBy($id, $except, $messageCategory, $relations, $cache, true);
	}
	
	/**
	 * Find model by id
	 *
	 * @param integer $id
	 * @param bool|false $except
	 * @param string $messageCategory
	 * @param array $relations
	 *
	 * @return mixed
	 */
	static public function findById($id, $except = false, $messageCategory = 'base', $relations = [], $cache = false, $conditions = null) {
		$class = get_called_class();
		return $class::findBy($id, $except, $messageCategory, $relations, $cache, false, $conditions);
	}
	
	/**
	 * Find model by hash
	 * @param string $hash
	 *
	 * @return array|Media|null
	 */
	public static function findByHash($hash, $allowRoles = []) {
		
		// Create query
		$query = self::find()->where('MD5(CONCAT(id, created_at)) = :hash', [
			':hash' => $hash
		]);
		
		// Add owner condition
		//if (!Yii::$app->user->isSuperAdmin && !Yii::$app->user->isAdmin && !Yii::$app->user->hasRole($allowRoles)) {
		//	$query->andWhere('created_by = :created_by', [
		//		':created_by' => Yii::$app->user->id,
		//	]);
		//}
		
		return $query->one();
	}
	
	/**
	 * @param $query
	 */
	public static function prepareQuery($query) {}

	/**
	 * Get module type
	 * @return int
	 */
	public static function moduleType() {
		return ModuleType::NONE;
	}

	/**
	 * Get module type
	 * @return int
	 */
	public function getModuleType() {
		return static::moduleType();
	}

	/**
	 * Get module name
	 * @return int
	 */
	public function getModuleName() {
		return ModuleType::getItem($this->moduleType);
	}
	
	/**
	 * Get module class
	 *
	 * @return string
	 * @throws \ReflectionException
	 */
	public function getModuleClass() {
		return (new \ReflectionClass(get_called_class()))->getShortName();
	}
	
	/**
	 * Get hash
	 *
	 * @return string
	 */
	public function getHash() {
		return md5($this->id.$this->created_at);
	}
	
	/**
	 * Get url
	 *
	 * @return string
	 * @throws \ReflectionException
	 */
	public function getUrl($scheme = false) {
		$class = strtolower($this->getModuleClass());
		$model = Inflector::camel2id(get_called_class());
		return Url::toRoute(['/'.$class.'/view', 'id' => $this->id], $scheme);
	}
	
	/**
	 * Get uri
	 *
	 * @return string
	 * @throws \ReflectionException
	 */
	public function getUri() {
		$slugify = $this->seo->slugify;
		
		$route = $this->getUriModuleName() ?: Inflector::camel2id($this->getModuleClass());
		
		$uri = SeoUri::uriForModule($route, 0, 'index');
		if ($uri)
			return $uri.'/'.$slugify;
		return $slugify;
	}
	
	/**
	 * Get uri params
	 * @return array
	 */
	public function getUriParams() {
		return array(
			'id' => $this->id,
		);
	}
	
	/**
	 * Get unique model id
	 * @return string
	 */
	public function getUnique_id() {
		return $this->getModuleType().'-'.$this->id;
	}
	
	/**
	 * @param $name
	 *
	 * @return mixed|null
	 */
	public function getParamsValue($name) {
		if (isset($this->params[$name]))
			return $this->params[$name];
		return null;
	}
	
	/**
	 * @param $name
	 * @param $value
	 */
	public function setParamsValue($name, $value) {
		$params = $this->params;
		$params[$name] = $value;
		$this->params = $params;
	}
	
	/**
	 * Root uri params
	 * @return array
	 */
	static public function rootUriParams() {
		return [];
	}
	
	/**
	 * @return string|null
	 */
	public function getUriModuleName() {
		return null;
	}
	
	/**
	 * @param string $action
	 * @param bool $scheme
	 *
	 * @return string
	 */
	public function getUriRoute($action = 'view', $scheme = false) {
		return Url::to(['/'.$this->getUriModuleName().'/'.$action, 'id' => $this->id], $scheme);
	}
	
	/**
	 * List data key => val
     *
	 * @param string $key
	 * @param string $val
	 * @param string $orderBy
	 * @param array $condition
	 * @param array $excludeIds
     * @param array $compare
	 *
	 * @return mixed
	 * @throws \Exception
	 */
	static public function listData($key = 'id', $val = 'title', $orderBy = 'title', $condition = [], $excludeIds = [], $compare = []) {
		$class = get_called_class();

		// Create deependency by updated_at
		//$dependency = new DbDependency();
		//$dependency->db = $class::getDb();
		//$dependency->sql = 'SELECT MAX(updated_at) FROM '.$class::tableName();

		//return $class::getDb()->cache(function ($db) use($class, $key, $val, $orderBy, $condition, $excludeIds) {

			/** @var \yii\db\ActiveQuery $query */
			$query = $class::find();
			$query->andWhere(['not in', 'status', Status::TEMP]);
			if ($condition)
				$query->andWhere($condition);
			if ($excludeIds && count($excludeIds))
				$query->andWhere(['not in', 'id', $excludeIds]);
			if ($compare) {
			    foreach ($compare as $c) {
			        $query->andFilterCompare($c[0], $c[1], $c[2]);
                }
            }
			if ($orderBy)
				$query->orderBy($orderBy);

			return ArrayHelper::map($query->all(), $key, $val);
		//}, Yii::$app->params['cache.duration'], $dependency);
	}
	
	/**
	 * List data
	 * @param array $key
	 * @param array $val
	 * @param string $orderBy
	 * @param array $relations
	 * @param array $excludeIds
	 *
	 * @return mixed
	 * @throws \Exception
	 */
	static public function listDataKeysValues($key = ['from' => 'id', 'to' => 'id'], $val = ['from' => 'title', 'to' => 'title'], $orderBy = 'title', $relations = [], $excludeIds = []) {
		$data = self::listData($key['from'], $val['from'], $orderBy, $relations, $excludeIds);
		$result = [];
		if ($data) {
			foreach ($data as $dataKey => $dataVal) {
				$result[] = [
					$key['to'] => $dataKey,
					$val['to'] => $dataVal,
				];
			}
		}
		return $result;
	}
	
	/**
	 * Get last sequence
	 * @param mixed $condition
	 * @param mixed $joinWith
	 *
	 * @return int
	 * @throws \yii\base\InvalidConfigException
	 */
	static public function lastSequence($condition = null, $joinWith = null) {
		$class = get_called_class();
		$model = Yii::createObject($class);
		if (in_array('sequence', array_keys($model->getAttributes()))) {
			$query = $class::find();
			if ($condition)
				$query->andWhere($condition);
			if ($joinWith)
				$query->joinWith($joinWith);
			return $query->max('sequence') + self::SEQUENCE_STEP;
		}
		
		return 0;
	}

	/**
	 * Enable object
	 * 
	 * Set status enabled and save
	 */
	public function enable($useStatus = true) {
		if ($useStatus && isset($this->attributes['status'])) {
			$this->status = Status::ENABLED;
			$this->save(false);
		}
	}

	/**
	 * Delete object
	 *
	 * Set status deleted and save
	 */
	public function delete($useStatus = true) {
		if ($useStatus && isset($this->attributes['status'])) {
			$this->status = Status::DELETED;
			$this->save(false);
		}
		else
			parent::delete();
	}
	
	/**
	 * @return string
	 */
	public static function calledClass() {
		return get_called_class();
	}
	
	/**
	 * @param $userId
	 *
	 * @return mixed
	 */
	static public function hasByUser($userId) {
		$class = self::calledClass();
		return $class::find()->andWhere(['user_id' => $userId])->exists();
	}
	
	/**
	 * Check can access user action
	 * @param string $action
	 *
	 * @return bool|int|string
	 */
	public function can($action) {
		if (Yii::$app->user->getIsAdmin() || Yii::$app->user->getIsSuperAdmin())
			return true;
		
		$attributes = array_keys($this->getAttributes());
		if (in_array('user_id', $attributes))
			return $this->getAttribute('user_id') == Yii::$app->user->id;
		if (in_array('created_at', $attributes))
			return $this->getAttribute('created_at') == Yii::$app->user->id;
		
		return false;
	}
	
	/**
	 * Check can user update
	 * @return boolean
	 */
	public function canUpdate() {
		return $this->can('update');
	}
	
	/**
	 * Check can user delete
	 * @return boolean
	 */
	public function canDelete() {
		return $this->can('delete');
	}
	
	/**
	 * Set stat
	 * @param int $type
	 *
	 * @throws \yii\db\Exception
	 */
	public function setStat($type = TypeStatistics::VISIT) {
		$class = get_called_class();
		if ($class::moduleType()) {
			Yii::$app->statistics->set($type, $class::moduleType(), $this->id);
		}
	}
	
	/**
	 * Get stat
	 * @param int $type
	 *
	 * @throws \yii\db\Exception
	 */
	public function getStatisticsVal($type = TypeStatistics::VISIT) {
		if ($this->statistics) {
			if ($type == TypeStatistics::SHOW)
				return $this->statistics->show;
			if ($type == TypeStatistics::VISIT)
				return $this->statistics->visit;
			if ($type == TypeStatistics::OUTGOING)
				return $this->statistics->outgoing;
		}
		return 0;
	}
	
	/**
	 * Get class name
	 *
	 * @return string
	 * @throws \ReflectionException
	 */
	public function getClassName() {
		return (new \ReflectionClass($this))->getShortName();
	}
	
	/**
	 * @inheritdoc
	 */
	public function beforeSave($insert) {
		return parent::beforeSave($insert);
	}
}