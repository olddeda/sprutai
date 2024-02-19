<?php
namespace common\modules\event\models;

use Yii;
use yii\helpers\Url;

use common\modules\base\components\ActiveRecord;
use common\modules\base\helpers\enum\ModuleType;
use common\modules\base\components\Debug;

use common\modules\user\models\User;

use common\modules\event\models\query\EventQuery;

/**
 * This is the model class for table "{{%event}}".
 *
 * @property int $id
 * @property int $module_type
 * @property int $module_id
 * @property int $user_id
 * @property string $text
 * @property int $status
 * @property int $created_by
 * @property int $updated_by
 * @property int $date_at
 * @property int $created_at
 * @property int $updated_at
 *
 * @property string $date
 * @property string $datetime
 *
 * Defined relations:
 * @property \common\modules\user\models\User $user
 * @property \common\modules\user\models\User $createdBy
 * @property \common\modules\user\models\User $updatedBy
 */
class Event extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return '{{%event}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['module_type', 'module_id', 'user_id', 'status', 'created_by', 'updated_by', 'date_at', 'created_at', 'updated_at'], 'integer'],
            [['module_type', 'module_id', 'user_id', 'text', 'status'], 'required'],
            [['text'], 'string', 'max' => 4000],
            [['date', 'datetime'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('event', 'field_id'),
            'module_type' => Yii::t('event', 'field_module_type'),
            'module_id' => Yii::t('event', 'field_module_id'),
			'user_id' => Yii::t('event', 'field_user_id'),
            'text' => Yii::t('event', 'field_text'),
            'status' => Yii::t('event', 'field_status'),
            'date' => Yii::t('event', 'field_date'),
			'datetime' => Yii::t('event', 'field_datetime'),
            'created_by' => Yii::t('event', 'field_created_by'),
            'updated_by' => Yii::t('event', 'field_updated_by'),
            'date_at' => Yii::t('event', 'field_date_at'),
            'created_at' => Yii::t('event', 'field_created_at'),
            'updated_at' => Yii::t('event', 'field_updated_at'),
        ];
    }

    /**
     * {@inheritdoc}
     * @return \common\modules\event\models\query\EventQuery the active query used by this AR class.
     */
    public static function find() {
        return new EventQuery(get_called_class());
    }
	
	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getModuleContent() {
		return $this->hasOne(Content::class, ['id' => 'module_id'])->alias('moduleContent')->where([]);
	}
	
	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getUser() {
		return $this->hasOne(User::class, ['id' => 'user_id']);
	}
	
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy() {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }
	
	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getToUpdatedBy() {
		return $this->hasOne(User::class, ['id' => 'updated_by']);
	}

    /**
     * Get date formatted
     * @param string $format
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public function getDate($format = 'dd-MM-yyyy') {
        if (!$this->date_at)
            $this->date_at = time();
        return Yii::$app->formatter->asDate($this->date_at, $format);
    }

    /**
     * Set date
     * @param $val
     */
    public function setDate($val) {
        $val .= ' '.date('H:i:s');
        $this->date_at = strtotime($val);
    }

    /**
     * Get datetime formatted
     * @param string $format
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public function getDatetime($format = 'dd-MM-yyyy HH:mm') {
        if (!$this->date_at)
            $this->date_at = time();
        return Yii::$app->formatter->asDatetime($this->date_at, $format);
    }

    /**
     * Set datetime
     * @param $val
     */
    public function setDatetime($val) {
        $val .= ':'.date('s');
        $this->date_at = strtotime($val);
    }
}
