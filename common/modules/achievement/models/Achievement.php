<?php
namespace common\modules\achievement\models;

use Yii;

use common\modules\base\components\ActiveRecord;

use common\modules\achievement\models\query\AchievementQuery;
use common\modules\achievement\helpers\enum\Type;

/**
 * This is the model class for table "{{%achievement}}".
 *
 * @property integer $id
 * @property integer $type
 * @property integer $title
 * @property integer $level
 * @property integer $sequence
 * @property integer status
 * @property integer $created_at
 * @property integer $updated_at
 */
class Achievement extends ActiveRecord
{
	/**
	 * @inheritdoc
	 */
	public static function tableName() {
		return '{{%achievement}}';
	}

	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[['type', 'title', 'level', 'sequence'], 'required'],
			[['type', 'title', 'level', 'sequence', 'created_at', 'updated_at'], 'integer'],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels() {
		return [
			'id' => Yii::t('achievement_group', 'field_id'),
			'type' => Yii::t('achievement_group', 'field_type'),
			'title' => Yii::t('achievement_group', 'field_title'),
            'level' => Yii::t('achievement_group', 'field_level'),
            'sequence' => Yii::t('achievement_group', 'field_sequence'),
			'status' => Yii::t('achievement_group', 'field_status'),
			'created_at' => Yii::t('achievement_group', 'field_created_at'),
			'updated_at' => Yii::t('achievement_group', 'field_updated_at'),
		];
	}

    /**
     * @inheritdoc
     * @return AchievementQuery the active query used by this AR class.
     */
    public static function find() {
        return new AchievementQuery(get_called_class());
    }

    /**
     * @return string
     */
    public function getTypeName() {
        return str_replace('type_', '', Type::getItem($this->type));
    }
}
