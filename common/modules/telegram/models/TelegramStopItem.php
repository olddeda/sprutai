<?php
namespace common\modules\telegram\models;

use Yii;
use common\modules\base\components\ActiveRecord;

use common\modules\telegram\models\query\TelegramStopQuery;
use common\modules\telegram\models\query\TelegramStopItemQuery;

/**
 * This is the model class for table "{{%telegram_stop_item}}".
 *
 * @property integer $id
 * @property integer $telegram_stop_id
 * @property integer $telegram_chat_id
 * @property integer $telegram_user_id
 * @property string $text
 * @property integer $created_at
 * @property integer $updated_at
 *
 * Defined relations:
 * @property TelegramStop $telegramStop
 */
class TelegramStopItem extends ActiveRecord
{
	
    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%telegram_stop_item}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()  {
        return [
            [['telegram_stop_id', 'telegram_chat_id', 'telegram_user_id', 'created_at', 'updated_at'], 'integer'],
            [['telegram_stop_id', 'telegram_chat_id', 'telegram_user_id', 'text'], 'required'],
            [['text'], 'string', 'max' => 10000],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('telegram-stop-item', 'field_id'),
            'telegram_stop_id' => Yii::t('telegram-stop-item', 'field_telegram_stop_id'),
            'telegram_chat_id' => Yii::t('telegram-stop-item', 'field_telegram_chat_id'),
            'telegram_chat_id' => Yii::t('telegram-stop-item', 'field_telegram_chat_id'),
            'text' => Yii::t('telegram-stop-item', 'field_text'),
            'created_at' => Yii::t('telegram-stop-item', 'field_created_at'),
            'updated_at' => Yii::t('telegram-stop-item', 'field_updated_at'),
        ];
    }

    /**
     * @return TelegramStopItemQuery
     */
	public static function find() {
		return new TelegramStopItemQuery(get_called_class());
	}

    /**
     * @return TelegramStopQuery
     */
    public function getTelegramStop() {
        return $this->hasOne(TelegramStop::class, ['id' => 'telegram_stop_id']);
    }
}
