<?php
namespace common\modules\telegram\models;

use Yii;
use yii\db\Query;
use yii\helpers\ArrayHelper;

use common\modules\base\components\ActiveRecord;

use common\modules\telegram\models\query\TelegramStopQuery;

/**
 * This is the model class for table "{{%telegram_stop}}".
 *
 * @property integer $id
 * @property string $keyword
 * @property boolean $kick
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 */
class TelegramStop extends ActiveRecord
{
	static public $keywords;

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%telegram_stop}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()  {
        return [
            [['status', 'created_at', 'updated_at'], 'integer'],
            [['keyword', 'status'], 'required'],
            [['keyword'], 'string', 'max' => 255],
            [['kick'], 'boolean'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('telegram-stop', 'field_id'),
            'keyword' => Yii::t('telegram-stop', 'field_keyword'),
            'kick' => Yii::t('telegram-stop', 'field_kick'),
            'status' => Yii::t('telegram-stop', 'field_status'),
            'created_at' => Yii::t('telegram-stop', 'field_created_at'),
            'updated_at' => Yii::t('telegram-stop', 'field_updated_at'),
        ];
    }

    /**
     * @return TelegramStopQuery
     */
	public static function find() {
		return new TelegramStopQuery(get_called_class());
	}

    /**
     * @return array
     */
	static public function keywords() {
	    if (is_null(self::$keywords)) {
	        self::$keywords = (new Query())
                ->select('id, keyword, kick')
                ->from(self::tableName())
                ->where('status = 1')
                ->all();
        }
	    return self::$keywords;
    }
}
