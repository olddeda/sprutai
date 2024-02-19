<?php
namespace common\modules\telegram\models;

use common\modules\base\behaviors\ArrayFieldBehavior;
use common\modules\base\components\ActiveRecord;
use common\modules\telegram\models\query\TelegramChatUserQuery;
use yii\helpers\ArrayHelper;


/**
 * This is the model class for table "{{%telegram_chat_user}}".
 *
 * @property integer $id
 * @property integer $chat_id
 * @property integer $user_id
 * @property integer $number
 * @property integer $expire_at
 * @property integer $status
 * @property array $params
 */
class TelegramChatUser extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName() {
		return '{{%telegram_chat_user}}';
	}

    /**
     * @inheritdoc
     */
    public function behaviors() {
        return ArrayHelper::merge(parent::behaviors(), [
            [
                'class' => ArrayFieldBehavior::class,
                'attribute' => 'params',
            ],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function rules()  {
        return [
            [['chat_id', 'user_id', 'number', 'expire_at', 'status'], 'integer'],
			[['params'], 'safe'],
        ];
    }
	
	/**
	 * @inheritdoc
	 * @return TelegramChatUserQuery the active query used by this AR class.
	 */
	public static function find() {
		return new TelegramChatUserQuery(get_called_class());
	}
}
