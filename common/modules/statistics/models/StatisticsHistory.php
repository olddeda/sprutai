<?php
namespace common\modules\statistics\models;

use Yii;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

use common\modules\base\components\ActiveRecord;

use common\modules\user\models\User;

use common\modules\statistics\Module;
use common\modules\statistics\models\query\StatisticsHistoryQuery;

/**
 * This is the model class for table "{{%statistics_history}}".
 *
 * @property integer $id
 * @property integer $statistics_id
 * @property integer $type
 * @property integer $user_id
 * @property integer $user_ip
 * @property integer $status
 * @property integer $created_by
 * @property integer $updated_by
 * @property integer $created_at
 * @property integer $updated_at
 *
 * Defined relations:
 * @property \common\modules\user\models\User $createdBy
 * @property \common\modules\user\models\User $updatedBy
 * @property \common\modules\user\models\User $user
 */
class StatisticsHistory extends ActiveRecord
{
	/**
	 * @inheritdoc
	 */
	public static function tableName() {
		return '{{%statistics_history}}';
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
			[['statistics_id', 'type', 'user_ip', 'status'], 'required'],
			[['statistics_id', 'type', 'user_id', 'user_ip', 'status', 'created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
		];
	}
	
	/**
	 * @inheritdoc
	 */
	public function attributeLabels() {
		return [
			'id' => Yii::t('statistics-history', 'field_id'),
			'statistics_id' => Yii::t('statistics-history', 'field_statistics_id'),
			'statistics_type' => Yii::t('statistics-history', 'field_statistics_type'),
			'user_id' => Yii::t('statistics-history', 'field_user_id'),
			'user_ip' => Yii::t('statistics-history', 'field_user_ip'),
			'status' => Yii::t('statistics-history', 'field_status'),
			'created_by' => Yii::t('statistics-history', 'field_created_by'),
			'updated_by' => Yii::t('statistics-history', 'field_updated_by'),
			'created_at' => Yii::t('statistics-history', 'field_created_at'),
			'updated_at' => Yii::t('statistics-history', 'field_updated_at'),
		];
	}
	
	/**
	 * @inheritdoc
	 * @return \common\modules\statistics\models\query\StatisticsHistoryQuery the active query used by this AR class.
	 */
	public static function find() {
		return new StatisticsHistoryQuery(get_called_class());
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
	 * Get user model
	 * @return \common\modules\user\models\query\UserQuery
	 */
	public function getUser() {
		return $this->hasOne(User::className(), ['id' => 'user_id']);
	}
	
	/**
	 * Get user ip
	 * @return string
	 */
	public function getUser_ip() {
		return long2ip($this->user_ip);
	}
	
	/**
	 * Set user ip
	 * @param $val
	 */
	public function setUser_ip($val) {
		$this->user_ip = ip2long($val);
	}
	
	/**
	 * Get users
	 * @param $statisticsId
	 *
	 * @return array
	 */
	public static function getUsers($statisticsId) {
		$usersIds = (new Query())
			->select(['user_id'])
			->from(self::tableName())
			->where(['statistics_id' => $statisticsId])
			->groupBy('user_id')
			->column(self::getDb());
		
		if ($usersIds && count($usersIds)) {
			$models = User::find()->andWhere(['in', 'id', $usersIds])->all();
			return ArrayHelper::map($models, 'id', 'fio');
		}
		
		return [];
	}
}