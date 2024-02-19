<?php
namespace common\modules\statistics\models\clickhouse;

use Yii;
use yii\caching\DbDependency;

use common\modules\base\components\clickhouse\ActiveRecord;
use common\modules\base\components\Debug;
use common\modules\base\helpers\enum\Status;

use common\modules\user\models\User;

use common\modules\statistics\Module;
use common\modules\statistics\models\clickhouse\query\StatisticsQuery;
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
 * @property integer $year
 * @property integer $quarter
 * @property integer $month
 * @property integer $day_of_month
 * @property integer $day_of_week
 * @property string $date
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
		return Yii::$app->get('clickhouse');
	}
	
	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[['module_type', 'module_id', 'status'], 'required'],
			[['module_type', 'module_id', 'show', 'visit', 'outgoing', 'status', 'created_by', 'updated_by', 'created_at', 'updated_at', 'year', 'quarter', 'month', 'day_of_month', 'day_of_week'], 'integer'],
			[['module_type', 'module_id', 'status'], 'unique', 'targetAttribute' => ['module_type', 'module_id'], 'message' => 'The combination of Тип модуля and ID модуля has already been taken.'],
			[['date'], 'safe'],
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
			'year' => Yii::t('statistics', 'field_year'),
			'quarter' => Yii::t('statistics', 'field_quarter'),
			'month' => Yii::t('statistics', 'field_month'),
			'day_of_month' => Yii::t('statistics', 'field_date_of_month'),
			'day_of_week' => Yii::t('statistics', 'field_day_of_week'),
			'date' => Yii::t('statistics', 'field_date'),
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
}