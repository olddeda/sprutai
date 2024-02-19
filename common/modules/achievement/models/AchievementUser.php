<?php
namespace common\modules\achievement\models;

use common\modules\achievement\helpers\enum\Type;
use common\modules\catalog\models\CatalogItem;
use common\modules\notification\components\Notification;
use Yii;

use common\modules\base\components\ActiveRecord;

use common\modules\user\models\query\UserQuery;
use common\modules\user\models\User;

use common\modules\achievement\models\query\AchievementQuery;
use common\modules\achievement\models\query\AchievementUserQuery;

/**
 * This is the model class for table "{{%achievement_user}}".
 *
 * @property integer $id
 * @property integer $achievement_id
 * @property integer $user_id
 * @property integer $count
 * @property integer $created_at
 * @property integer $updated_at
 *
 * Defined relations:
 * @property Achievement $achievement
 * @property User $user
 */
class AchievementUser extends ActiveRecord
{
    /** @var integer */
    public $count;

	/**
	 * @inheritdoc
	 */
	public static function tableName() {
		return '{{%achievement_user}}';
	}

	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[['achievement_id'], 'required'],
			[['achievement_id', 'user_id', 'count'], 'integer'],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels() {
		return [
			'id' => Yii::t('achievement_user', 'field_id'),
			'achievement_id' => Yii::t('achievement_user', 'field_achievement_id'),
			'user_id' => Yii::t('achievement_user', 'field_user_id'),
			'created_at' => Yii::t('achievement_user', 'field_created_at'),
			'updated_at' => Yii::t('achievement_user', 'field_updated_at'),
		];
	}

    /**
     * @inheritdoc
     * @return AchievementUserQuery the active query used by this AR class.
     */
    public static function find() {
        return new AchievementUserQuery(get_called_class());
    }

    /**
     * @return AchievementQuery
     */
    public function getAchievement() {
        return $this->hasOne(Achievement::class, ['id' => 'achievement_id']);
    }

    /**
     * @return UserQuery
     */
    public function getUser() {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * @inheritDoc
     */
    public function afterSave($insert, $changedAttributes) {
        parent::afterSave($insert, $changedAttributes);

        if ($insert) {
            if ($this->achievement->level < 10) {
                return;
            }

            $message = Yii::t('notification', 'achievement_new_level', [
                'user_url' => 'http://v2.sprut.ai/user/'.$this->user->username,
                'user_name' => $this->user->getAuthorName(true),
                'title' => $this->achievement->title,
            ]);

            /** @var Notification $notification */
            $notification = Yii::$app->get('notification');

            $chatId = -1001082506583; // General
            //$chatId = -1001260099741; // Moderators;

            $notification->queueTelegramIds([$chatId], $message, [
                'bot' => 'telegramAchievement',
            ]);
        }
    }
}
