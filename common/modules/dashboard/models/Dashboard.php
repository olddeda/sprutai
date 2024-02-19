<?php
namespace common\modules\dashboard\models;

use Yii;
use yii\helpers\ArrayHelper;

use common\modules\base\components\ActiveRecord;
use common\modules\base\behaviors\ArrayFieldBehavior;

use common\modules\dashboard\models\query\DashboardQuery;

/**
 * This is the model class for table "{{%dashboard}}".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $name
 * @property integer $width
 * @property integer $height
 * @property integer $x
 * @property integer $y
 * @property array $params
 * @property integer $status
 * @property integer $created_by
 * @property integer $updated_by
 * @property integer $created_at
 * @property integer $updated_at
 */
class Dashboard extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName() {
    	return '{{%dashboard}}';
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
    public function rules() {
        return [
            [['user_id', 'name', 'width', 'height', 'x', 'y'], 'required'],
            [['user_id', 'width', 'height', 'x', 'y', 'status', 'created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
            [['name'], 'string', 'max' => 255],
			[['params'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('dashboard', 'field_id'),
            'user_id' => Yii::t('dashboard', 'field_user_id'),
            'name' => Yii::t('dashboard', 'field_name'),
            'width' => Yii::t('dashboard', 'field_width'),
            'height' => Yii::t('dashboard', 'field_height'),
            'x' => Yii::t('dashboard', 'field_x'),
            'y' => Yii::t('dashboard', 'field_y'),
            'status' => Yii::t('dashboard', 'field_status'),
            'created_by' => Yii::t('dashboard', 'field_created_by'),
            'updated_by' => Yii::t('dashboard', 'field_updated_by'),
            'created_at' => Yii::t('dashboard', 'field_created_at'),
            'updated_at' => Yii::t('dashboard', 'field_updated_at'),
        ];
    }

    /**
     * @inheritdoc
     * @return \common\modules\dashboard\models\query\DashboardQuery the active query used by this AR class.
     */
    public static function find() {
        return new DashboardQuery(get_called_class());
    }
}
