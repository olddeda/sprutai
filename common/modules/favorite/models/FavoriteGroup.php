<?php
namespace common\modules\favorite\models;

use Yii;

use common\modules\base\components\ActiveRecord;

use common\modules\favorite\models\query\FavoriteGroupQuery;

/**
 * This is the model class for table "{{%favorite_group}}".
 *
 * @property integer $id
 * @property integer $module_type
 * @property integer $module_id
 * @property integer $user_id
 * @property string $title
 * @property integer $sequence
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 *
 * Defined relations:
 * @property Favorite $item
 */
class FavoriteGroup extends ActiveRecord
{
    /**
     * @var integer
     */
    public $module_id;

    /**
     * @var integer
     */
    public $count;

    /**
     * @var integer
     */
    public $count_total;

	/**
	 * @inheritdoc
	 */
	public static function tableName() {
		return '{{%favorite_group}}';
	}

	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[['module_type', 'user_id'], 'required'],
			[['module_type', 'module_id', 'user_id', 'sequence', 'count', 'count_total', 'created_at', 'updated_at'], 'integer'],
            [['title'], 'string', 'max' => 255],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels() {
		return [
			'id' => Yii::t('favorite_group', 'field_id'),
			'module_type' => Yii::t('favorite_group', 'field_module_type'),
            'module_id' => Yii::t('favorite_group', 'field_module_id'),
			'user_id' => Yii::t('favorite_group', 'field_user_id'),
            'title' => Yii::t('favorite_group', 'field_title'),
			'created_at' => Yii::t('favorite_group', 'field_created_at'),
			'updated_at' => Yii::t('favorite_group', 'field_updated_at'),
		];
	}

    /**
     * @inheritdoc
     * @return FavoriteGroupQuery the active query used by this AR class.
     */
    public static function find() {
        return new FavoriteGroupQuery(get_called_class());
    }
}
