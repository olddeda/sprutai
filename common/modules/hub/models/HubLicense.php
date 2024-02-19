<?php
namespace common\modules\hub\models;

use Yii;
use yii\base\Exception;

use common\modules\base\components\ActiveRecord;

use common\modules\hub\models\query\HubLicenseQuery;

use common\modules\user\helpers\Password;

/**
 * This is the model class for table "{{%hub_license}}".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $type
 * @property string $code
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 */
class HubLicense extends ActiveRecord
{
	/**
	 * @inheritdoc
	 */
	public static function tableName() {
		return '{{%hub_license}}';
	}

	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[['type', 'status'], 'required'],
			[['type', 'user_id', 'status', 'created_at', 'updated_at'], 'integer'],
            [['code'], 'string'],
            [['code'], 'unique'],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels() {
		return [
			'id' => Yii::t('hub_license', 'field_id'),
			'type' => Yii::t('hub_license', 'field_type'),
			'user_id' => Yii::t('hub_license', 'field_user_id'),
            'code' => Yii::t('hub_license', 'field_code'),
            'status' => Yii::t('hub_license', 'field_status'),
			'created_at' => Yii::t('hub_license', 'field_created_at'),
			'updated_at' => Yii::t('hub_license', 'field_updated_at'),
		];
	}

    /**
     * @inheritdoc
     * @return HubLicenseQuery the active query used by this AR class.
     */
    public static function find() {
        return new HubLicenseQuery(get_called_class());
    }

    /**
     * @return string
     */
    public static function generateCode() {
        $result = null;
        while (is_null($result)) {
            $code = mb_strtoupper(implode('-', str_split(self::generate(20), 4)));
            if (!self::find()->where(['code' => $code])->exists()) {
                $result = $code;
            }
        }
        return $result;
    }

    /**
     * @param integer $length
     *
     * @return string
     */
    protected static function generate($length) {
        $sets = [
            'ABCDEFGHJKMNPQRSTUVWXYZ',
            '23456789',
        ];
        $all = '';
        $string = '';
        foreach ($sets as $set) {
            $string .= $set[array_rand(str_split($set))];
            $all .= $set;
        }

        $all = str_split($all);
        for ($i = 0; $i < $length - count($sets); $i++) {
            $string .= $all[array_rand($all)];
        }

        $string = str_shuffle($string);

        return $string;
    }

    /**
     * @inheritDoc
     */
    public function beforeSave($insert) {
        if ($insert && is_null($this->code)) {
            $this->code = self::generateCode();
        }
        return parent::beforeSave($insert);
    }
}
