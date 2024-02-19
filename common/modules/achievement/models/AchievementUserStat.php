<?php
namespace common\modules\achievement\models;

use Yii;

use common\modules\base\components\ActiveRecord;

use common\modules\user\models\User;
use common\modules\user\models\query\UserQuery;

use common\modules\achievement\models\query\AchievementUserStatQuery;

/**
 * This is the model class for table "{{%achievement_user_stat}}".
 *
 * @property integer $id
 * @property integer $type
 * @property integer $user_id
 * @property integer $count
 * @property integer $created_at
 * @property integer $updated_at
 *
 * Defined relations:
 * @property User $user
 */
class AchievementUserStat extends ActiveRecord
{
	/**
	 * @inheritdoc
	 */
	public static function tableName() {
		return '{{%achievement_user_stat}}';
	}

	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[['type', 'user_id'], 'required'],
			[['type', 'user_id', 'count', 'created_at', 'updated_at'], 'integer'],
            [['count'], 'default', 'value' => 0],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels() {
		return [
			'id' => Yii::t('achievement_user_stat', 'field_id'),
			'type' => Yii::t('achievement_user_stat', 'field_type'),
			'user_id' => Yii::t('achievement_user_stat', 'field_user_id'),
            'count' => Yii::t('achievement_user_stat', 'field_count'),
			'created_at' => Yii::t('achievement_user_stat', 'field_created_at'),
			'updated_at' => Yii::t('achievement_user_stat', 'field_updated_at'),
		];
	}

    /**
     * @inheritdoc
     * @return AchievementUserStatQuery the active query used by this AR class.
     */
    public static function find() {
        return new AchievementUserStatQuery(get_called_class());
    }

    /**
     * @return UserQuery
     */
    public function getUser() {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}
