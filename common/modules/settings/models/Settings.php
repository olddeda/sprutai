<?php

namespace common\modules\settings\models;

use Yii;
use yii\helpers\ArrayHelper;

use common\modules\base\components\ActiveRecord;
use common\modules\base\helpers\enum\Status;

use common\modules\settings\models\query\SettingsQuery;
use common\modules\settings\helpers\enum\Type;

/**
 * This is the model class for table "Settings".
 *
 * @property integer $id
 * @property string $type
 * @property string $section
 * @property string $key
 * @property string $value
 * @property string $descr
 * @property boolean $status
 * @property integer $created_by
 * @property integer $updated_by
 * @property integer $created_at
 * @property integer $updated_at
 *
 */
class Settings extends ActiveRecord
{
    /**
     * Declares the name of the database table associated with this AR class.
     * @return string the table name
     */
    public static function tableName() {
        return '{{%settings}}';
    }

    /**
     * Returns the validation rules for attributes.
     * @return array validation rules
     */
    public function rules() {
        return [
            [['section', 'key', 'value'], 'required'],
            [['section', 'key'], 'unique', 'targetAttribute' => ['section', 'key']],
            [['value', 'type'], 'string'],
            [['section', 'key', 'descr'], 'string', 'max' => 255],
            [['status'], 'integer'],
            ['status', 'default', 'value' => Status::ENABLED],
            [['type', 'descr'], 'safe'],
        ];
    }

    /**
     * Returns the attribute labels.
     * @return array attribute labels (name => label)
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('settings', 'field_id'),
            'type' => Yii::t('settings', 'field_type'),
            'section' => Yii::t('settings', 'field_section'),
            'key' => Yii::t('settings', 'field_key'),
            'value' => Yii::t('settings', 'field_value'),
			'descr' => Yii::t('settings', 'field_descr'),
            'status' => Yii::t('settings', 'field_status'),
			'created_by' => Yii::t('settings', 'field_created_by'),
			'updated_by' => Yii::t('settings', 'field_updated_by'),
            'created_at' => Yii::t('settings', 'field_created_at'),
            'updated_at' => Yii::t('settings', 'field_updated_at'),
        ];
    }

    /**
     * Returns a list of behaviors that this component should behave as.
     * @return array
     */
    public function behaviors() {
        return array_merge(parent::behaviors(), [
        ]);
    }

    /**
     * Creates an [[ActiveQueryInterface]] instance for query purpose.
     * @return common/modules/settings/models/query/SettingsQuery
     */
    public static function find() {
        return new SettingsQuery(get_called_class());
    }

    /**
     * This method is invoked after deleting a record.
     */
    public function afterDelete() {
        Yii::$app->settings->invalidateCache();
        parent::afterDelete();
    }

    /**
     * Return array of settings
     */
    public function getSettings() {
        $result = [];
        $settings = static::find()->select(['type', 'section', 'key', 'value'])->enabled()->asArray()->all();
        foreach ($settings as $setting) {
            $section = $setting['section'];
            $key = $setting['key'];
            $settingOptions = [
				'type' => $setting['type'],
				'value' => $setting['value']
			];

            if (isset($result[$section][$key])) {
                ArrayHelper::merge($result[$section][$key], $settingOptions);
            }
			else {
                $result[$section][$key] = $settingOptions;
            }
        }
        return $result;
    }

    /**
     * Set
     * @param $section
     * @param $key
     * @param $value
     * @param null $type
     * @return bool
     */
    public function set($section, $key, $value, $type = null) {

		// Get types
        $types = Type::getConstantsByValue();

		// Find model
        $model = self::findOne([
			'section' => $section,
			'key' => $key
		]);

		// If not exists mode, create
        if ($model === null)
            $model = new self();

		// Set params
        $model->section = $section;
        $model->key = $key;
        $model->value = strval($value);
		$model->type = ($type !== null && ArrayHelper::keyExists($type, $types)) ? $type : gettype($value);

		// Save and return result
        return $model->save();
    }

    /**
     * Remove
     * @param $section
     * @param $key
     * @return bool|int|null
     * @throws \Exception
     */
    public function remove($section, $key) {
        $model = self::findOne([
			'section' => $section,
			'key' => $key
		]);
        if ($model !== null)
            return $model->delete();
        return false;
    }
	
	/**
	 * This method is called at the end of inserting or updating a record.
	 *
	 * @param bool $insert
	 * @param array $changedAttributes
	 */
	public function afterSave($insert, $changedAttributes) {
		Yii::$app->settings->invalidateCache();
		parent::afterSave($insert, $changedAttributes);
	}
}
