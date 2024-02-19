<?php
namespace common\modules\statistics\components;

use common\modules\base\components\Debug;
use Yii;
use yii\base\Component;
use yii\caching\DbDependency;

use common\modules\base\helpers\enum\Status;

use common\modules\statistics\Module;
use common\modules\statistics\helpers\enum\Type;
use yii\helpers\ArrayHelper;

/**
 * Class StatisticsComponent
 * @package common\modules\statistics\components
 */
class StatisticsComponent extends Component
{
	/**
	 * @var string
	 */
	public $driver = 'mysql';
	
	/**
	 * @var array
	 */
	private $_models = [
		'mysql' => [
			'statistics' => 'common\modules\statistics\models\Statistics',
			'history' => 'common\modules\statistics\models\StatisticsHistory',
		],
		'clickhouse' => [
			'statistics' => 'common\modules\statistics\models\Statistics',
			'history' => 'common\modules\statistics\models\clickhouse\StatisticsHistory',
		],
	];
	
	/**
	 * @var null
	 */
	private $_items	= null;
	
	/**
	 * @var null
	 */
	private $_itemsIds = null;
	
	/**
	 * @var null
	 */
	private $_itemsModuleIds	= null;
	
	/**
	 * Get statistics
	 * @param integer $type
	 * @param integer $moduleType
	 * @param array $moduleId
	 * @return integer
	 */
	public function get($type, $moduleType, $moduleId) {
		if ($moduleType && $moduleId) {
			
			$this->_loadItems();
			
			if ($this->_items && count($this->_items) && isset($this->_items[$moduleType]) && isset($this->_items[$moduleType][$moduleId]) && isset($this->_items[$moduleType][$moduleId][$type]))
				return $this->_items[$moduleType][$moduleId][$type];
			
			$model = $this->statisticsModel()::find()->where([
				'module_type' => $moduleType,
				'module_id' => $moduleId,
			])->one();
			if ($model) {
				if ($type == Type::SHOW)
					return $model->show;
				if ($type == Type::VISIT)
					return $model->visit;
				if ($type == Type::OUTGOING)
					return $model->outgoing;
			}
		}
		return 0;
	}
	
	
	/**
	 * Set statistics
	 * @param integer $type
	 * @param integer $moduleType
	 * @param array $moduleId
	 */
	public function set($type, $moduleType, $moduleId) {
		if (in_array($type, Type::getValues()) && $moduleType && $moduleId) {
			$moduleIds = (!is_array($moduleId)) ? [intval($moduleId)] : array_map('intval', explode(',', $moduleId));
			
			// Check rows
			$this->_checkAndCreate($moduleType, $moduleIds);
			
			// Get ids
			$ids = $this->_getIds($moduleType, $moduleIds);
			
			$userId = (Yii::$app->user->isGuest) ? 0 : Yii::$app->user->id;
			
			$historyIdsFound = $this->historyModel()::find()
				->select('statistics_id')
				->from($this->historyModel()::tableName())
				->where('type = :type AND user_id = :user_id AND user_ip = :user_ip AND status = :status AND created_at + :interval > :time', [
					':type' => $type,
					':user_id' => $userId,
					':user_ip' => ip2long(Yii::$app->request->getUserIP()),
					':status' => Status::ENABLED,
					':interval' => Module::getInstance()->timeInterval,
					':time' => time(),
				])
				->andWhere(['in', 'statistics_id', $ids])
				->createCommand()
				->queryColumn();
			
			$historyIdsNotFound = array_diff($ids, $historyIdsFound);
			if (count($historyIdsNotFound)) {
				$userId = $userId;
				$time = time();
				
				$fields = ['statistics_id', 'type', 'user_id', 'user_ip', 'status', 'created_by', 'updated_by', 'created_at', 'updated_at'];
				if ($this->driver == 'clickhouse') {
					$fields = ArrayHelper::merge($fields, ['year', 'quarter', 'month', 'day_of_month', 'day_of_week', 'date']);
				}
				
				$insert = [];
				foreach ($historyIdsNotFound as $id) {
					$tmp = [$id, $type, $userId, ip2long(Yii::$app->request->getUserIP()), Status::ENABLED, $userId, $userId, $time, $time];
					
					if ($this->driver == 'clickhouse') {
						$tmp = ArrayHelper::merge($tmp, [
							intval(date('Y', $time)),
							intval(ceil(date('n', $time) / 3)),
							intval(date('n', $time)),
							intval(date('j', $time)),
							intval(date('w', $time)),
							date('Y-m-d'),
						]);
					}
					
					$insert[] = $tmp;
				}
				$this->historyModel()::getDb()->createCommand()->batchInsert($this->historyModel()::tableName(), $fields, $insert)->execute();
				
				$this->statisticsModel()::updateAllCounters([
					Type::getItem($type) => 1,
				], ['in', 'id', $historyIdsNotFound]);
			}
		}
	}
	
	/**
	 * Check and create rows
	 * @param $moduleType
	 * @param $moduleIds
	 */
	private function _checkAndCreate($moduleType, $moduleIds) {
		$idsFound = $this->_getModuleIds($moduleType);
		$idsNotFound = array_diff($moduleIds, $idsFound);
		
		if (count($idsNotFound)) {
			$userId = (Yii::$app->user->isGuest) ? 0 : Yii::$app->user->id;
			$time = time();
			
			$insert = [];
			foreach ($idsNotFound as $id)
				$insert[] = [$moduleType, $id, 0, 0, 0, Status::ENABLED, $userId, $userId, $time, $time];
			
			$this->statisticsModel()::getDb()->createCommand()->batchInsert($this->statisticsModel()::tableName(), ['module_type', 'module_id', 'show', 'visit', 'outgoing', 'status', 'created_by', 'updated_by', 'created_at', 'updated_at'], $insert)->execute();
			
			$this->_loadItems(true);
		}
	}
	
	/**
	 * Cache all items
	 */
	private function _loadItems($force = false) {
		if (!$this->_itemsIds || !$this->_itemsModuleIds || $force) {
			$this->_items = [];
			$this->_itemsIds = [];
			$this->_itemsModuleIds = [];
			
			$statisticsClass = $this->statisticsModel();
			
			$dependency = new DbDependency([
				'sql' => 'SELECT MAX(updated_at) FROM '.$statisticsClass::tableName(),
			]);
			
			$rows = $this->statisticsModel()::getDb()->cache(function ($db) use ($statisticsClass) {
				return $db->createCommand('SELECT `id`, `module_type`, `module_id`, `visit`, `show`, `outgoing`, `updated_at` FROM '.$statisticsClass::tableName())->queryAll();
			}, Yii::$app->params['cache.duration'], $dependency);
			
			if ($rows) {
				foreach ($rows as $m) {
					$this->_items[$m['module_type']][$m['module_id']]['visit'] = intval($m['visit']);
					$this->_items[$m['module_type']][$m['module_id']]['show'] = intval($m['show']);
					$this->_items[$m['module_type']][$m['module_id']]['outgoing'] = intval($m['outgoing']);
					$this->_itemsIds[$m['module_type']][$m['module_id']] = intval($m['id']);
					$this->_itemsModuleIds[$m['module_type']][] = $m['module_id'];
				}
			}
		}
	}
	
	/**
	 * Get ids by module type and module id
	 * @param $moduleType
	 * @param $moduleIds
	 *
	 * @return array
	 */
	private function _getIds($moduleType, $moduleIds) {
		if (!$this->_itemsIds)
			$this->_loadItems();
		$ids = [];
		if (isset($this->_itemsIds[$moduleType])) {
			foreach ($this->_itemsIds[$moduleType] as $moduleId => $id) {
				if (in_array($moduleId, $moduleIds))
					$ids[] = $id;
			}
		}
		return $ids;
	}
	
	/**
	 * Get modules ids by module type
	 * @param $moduleType
	 *
	 * @return array
	 */
	private function _getModuleIds($moduleType) {
		if (!$this->_itemsModuleIds)
			$this->_loadItems();
		return (isset($this->_itemsModuleIds[$moduleType])) ? $this->_itemsModuleIds[$moduleType] : [];
	}
	
	private function statisticsModel() {
		return $this->_models[$this->driver]['statistics'];
	}
	
	private function historyModel() {
		return $this->_models[$this->driver]['history'];
	}
}