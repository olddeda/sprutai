<?php
namespace common\modules\statistics\models;

use Yii;
use yii\caching\DbDependency;

use common\modules\base\components\ActiveRecord;
use common\modules\base\components\Debug;
use common\modules\base\helpers\enum\Status;

use common\modules\user\models\User;

use common\modules\statistics\Module;
use common\modules\statistics\models\query\StatisticsQuery;
use common\modules\statistics\helpers\enum\Type;

/**
 * This is the model class for table "{{%statistics}}".
 *
 * @property integer $id
 * @property integer $module_type
 * @property integer $module_id
 * @property integer $show
 * @property integer $visit
 * @property integer $outgoing
 * @property integer $status
 * @property integer $created_by
 * @property integer $updated_by
 * @property integer $created_at
 * @property integer $updated_at
 *
 * Defined relations:
 * @property \common\modules\user\models\User $createdBy
 * @property \common\modules\user\models\User $updatedBy
 */
class Statistics extends ActiveRecord
{
	/** @var integer|array */
	public $module_ids;
	
	private static $_items			= null;
	private static $_itemsIds		= null;
	private static $_itemsModuleIds	= null;
	
	/**
	 * @inheritdoc
	 */
	public static function tableName() {
		return '{{%statistics}}';
	}
	
	/**
	 * @return \yii\db\Connection
	 */
	public static function getDb() {
		return Module::getInstance()->getDb();
	}
	
	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[['module_type', 'module_id', 'status'], 'required'],
			[['module_type', 'module_id', 'show', 'visit', 'outgoing', 'status', 'created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
			[['module_type', 'module_id', 'status'], 'unique', 'targetAttribute' => ['module_type', 'module_id'], 'message' => 'The combination of Тип модуля and ID модуля has already been taken.'],
		];
	}
	
	/**
	 * @inheritdoc
	 */
	public function attributeLabels() {
		return [
			'id' => Yii::t('statistics', 'field_id'),
			'module_type' => Yii::t('statistics', 'field_module_type'),
			'module_id' => Yii::t('statistics', 'field_module_id'),
			'show' => Yii::t('statistics', 'field_show'),
			'visit' => Yii::t('statistics', 'field_visit'),
			'outgoing' => Yii::t('statistics', 'field_outgoing'),
			'status' => Yii::t('statistics', 'field_status'),
			'created_by' => Yii::t('statistics', 'field_created_by'),
			'updated_by' => Yii::t('statistics', 'field_updated_by'),
			'created_at' => Yii::t('statistics', 'field_created_at'),
			'updated_at' => Yii::t('statistics', 'field_updated_at'),
		];
	}
	
	/**
	 * @inheritdoc
	 * @return \common\modules\statistics\models\query\StatisticsQuery the active query used by this AR class.
	 */
	public static function find() {
		return new StatisticsQuery(get_called_class());
	}
	
	/**
	 * Get created user model
	 * @return \common\modules\user\models\query\UserQuery
	 */
	public function getCreatedBy() {
		return $this->hasOne(User::className(), ['id' => 'created_by']);
	}
	
	/**
	 * Get updated user model
	 * @return \common\modules\user\models\query\UserQuery
	 */
	public function getUpdatedBy() {
		return $this->hasOne(User::className(), ['id' => 'updated_by']);
	}
	
	/**
	 * Get statistics
	 * @param integer $type
	 * @param integer $moduleType
	 * @param array $moduleId
	 * @return integer
	 */
	static public function get($type, $moduleType, $moduleId) {
		if ($moduleType && $moduleId) {
			
			self::_loadItems();
			
			if (self::$_items && count(self::$_items) && isset(self::$_items[$moduleType]) && isset(self::$_items[$moduleType][$moduleId]) && isset(self::$_items[$moduleType][$moduleId][$type]))
				return self::$_items[$moduleType][$moduleId][$type];
			
			$model = self::find()->where([
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
	static public function set($type, $moduleType, $moduleId) {
		if (in_array($type, Type::getValues()) && $moduleType && $moduleId) {
			$moduleIds = (!is_array($moduleId)) ? [$moduleId] : $moduleId;
			
			// Check rows
			self::_checkAndCreate($moduleType, $moduleIds);
			
			// Get ids
			$ids = self::_getIds($moduleType, $moduleIds);
			
			$userId = (Yii::$app->user->isGuest) ? 0 : Yii::$app->user->id;
			
			$historyIdsFound = StatisticsHistory::find()
				->select('statistics_id')
				->from(StatisticsHistory::tableName())
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
				
				$insert = [];
				foreach ($historyIdsNotFound as $id)
					$insert[] = [$id, $type, $userId, ip2long(Yii::$app->request->getUserIP()), Status::ENABLED, $userId, $userId, $time, $time];
				self::getDb()->createCommand()->batchInsert(StatisticsHistory::tableName(), ['statistics_id', 'type', 'user_id', 'user_ip', 'status', 'created_by', 'updated_by', 'created_at', 'updated_at'], $insert)->execute();
				
				self::updateAllCounters([
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
	private static function _checkAndCreate($moduleType, $moduleIds) {
		$idsFound = self::_getModuleIds($moduleType);
		$idsNotFound = array_diff($moduleIds, $idsFound);
		
		
		
		if (count($idsNotFound)) {
			$userId = (Yii::$app->user->isGuest) ? 0 : Yii::$app->user->id;
			$time = time();
			
			$insert = [];
			foreach ($idsNotFound as $id)
				$insert[] = [$moduleType, $id, 0, 0, 0, Status::ENABLED, $userId, $userId, $time, $time];
			
			self::getDb()->createCommand()->batchInsert(self::tableName(), ['module_type', 'module_id', 'show', 'visit', 'outgoing', 'status', 'created_by', 'updated_by', 'created_at', 'updated_at'], $insert)->execute();
			
			self::_loadItems(true);
		}
	}
	
	/**
	 * Cache all items
	 */
	private static function _loadItems($force = false) {
		
		if (!self::$_itemsIds || !self::$_itemsModuleIds || $force) {
			self::$_items = [];
			self::$_itemsIds = [];
			self::$_itemsModuleIds = [];
			
			$dependency = new DbDependency([
				'sql' => 'SELECT MAX(updated_at) FROM '.self::tableName(),
			]);
			
			$rows = self::getDb()->cache(function ($db) {
				return $db->createCommand('SELECT `id`, `module_type`, `module_id`, `visit`, `show`, `outgoing`, `updated_at` FROM '.self::tableName())->queryAll();
			}, Yii::$app->params['cache.duration'], $dependency);
			
			if ($rows) {
				foreach ($rows as $m) {
					self::$_items[$m['module_type']][$m['module_id']]['visit'] = $m['visit'];
					self::$_items[$m['module_type']][$m['module_id']]['show'] = $m['show'];
					self::$_items[$m['module_type']][$m['module_id']]['outgoing'] = $m['outgoing'];
					self::$_itemsIds[$m['module_type']][$m['module_id']] = $m['id'];
					self::$_itemsModuleIds[$m['module_type']][] = $m['module_id'];
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
	private static function _getIds($moduleType, $moduleIds) {
		if (!self::$_itemsIds)
			self::_loadItems();
		$ids = [];
		if (isset(self::$_itemsIds[$moduleType])) {
			foreach (self::$_itemsIds[$moduleType] as $moduleId => $id) {
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
	private static function _getModuleIds($moduleType) {
		if (!self::$_itemsModuleIds)
			self::_loadItems();
		return (isset(self::$_itemsModuleIds[$moduleType])) ? self::$_itemsModuleIds[$moduleType] : [];
	}
}