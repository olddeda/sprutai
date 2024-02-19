<?php
namespace common\modules\notification\models;

use Yii;

use common\modules\base\components\ActiveRecord;

/**
 * This is the model class for table "notification_status".
 *
 * @property integer $id
 * @property string $provider
 * @property string $event
 * @property string $params
 * @property string $status
 * @property string $created_at
 * @property string $updated_at
 */
class NotificationStatus extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%notification_status}}';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
			[['created_at', 'updated_at'], 'integer'],
            [['params'], 'string'],
            [['provider', 'event'], 'string', 'max' => 255],
            [['status'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
		return [
			'id' => Yii::t('notification-status', 'field_id'),
			'provider' => Yii::t('notification-status', 'field_provider'),
			'event' => Yii::t('notification-status', 'field_event'),
			'params' => Yii::t('notification-status', 'field_params'),
			'status' => Yii::t('notification-status', 'field_status'),
			'created_at' => Yii::t('notification-status', 'field_created_at'),
			'updated_at' => Yii::t('notification-status', 'field_updated_at'),
		];
    }
}