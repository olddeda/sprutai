<?php
namespace common\modules\user\models;

use common\modules\user\helpers\enum\WalletType;
use Yii;

use yii\helpers\ArrayHelper;

use common\modules\base\components\ActiveRecord;
use common\modules\base\extensions\phoneInput\PhoneInputValidator;
use common\modules\base\extensions\phoneInput\PhoneInputBehavior;

/**
 * This is the model class for table "profile".
 *
 * @property integer $user_id
 * @property string $first_name
 * @property string $middle_name
 * @property string $last_name
 * @property string $phone
 * @property integer $wallet_type
 * @property string $wallet_number
 * @property integer $created_by
 * @property integer $updated_by
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property User $user
 */
class UserProfile extends ActiveRecord
{
    /**
	 * @var \common\modules\user\Module
	 */
    protected $module;

    /**
	 * @inheritdoc
	 */
    public function init() {
        $this->module = Yii::$app->getModule('user');
    }

    /**
	 * @inheritdoc
	 */
    public static function tableName() {
        return '{{%user_profile}}';
    }

	/**
	 * @inheritdoc
	 */
	public function behaviors() {
		return ArrayHelper::merge(parent::behaviors(), [
			[
				'class' => PhoneInputBehavior::className(),
			],
		]);
	}

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
			[['first_name', 'last_name'], 'required'],
			[['wallet_type', 'created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
			[['first_name', 'last_name', 'middle_name', 'phone', 'wallet_number'], 'string', 'max' => 50],
			[['fio'], 'safe'],
			[['first_name', 'last_name', 'middle_name'], 'filter', 'filter' => '\yii\helpers\HtmlPurifier::process'],
			[['first_name', 'last_name', 'middle_name'], 'filter', 'filter' => '\yii\helpers\Html::encode'],
			[['phone'], PhoneInputValidator::class, 'skipOnEmpty' => true],
        ];
    }

    /**
	 * @inheritdoc
	 */
    public function attributeLabels() {
        return [
            'first_name' => Yii::t('user-profile', 'field_first_name'),
            'last_name' => Yii::t('user-profile', 'field_last_name'),
            'middle_name' => Yii::t('user-profile', 'field_middle_name'),
			'phone' => Yii::t('user-profile', 'field_phone'),
			'fio' => Yii::t('user-profile', 'field_fio'),
			'phone' => Yii::t('user-profile', 'field_phone'),
            'wallet_type' => Yii::t('user-profile', 'field_wallet_type'),
            'wallet_number' => Yii::t('user-profile', 'field_wallet_number'),
        ];
    }

    /**
     * @return \yii\db\ActiveQueryInterface
     */
    public function getUser() {
        return $this->hasOne($this->module->modelMap['User'], ['id' => 'user_id']);
    }

	/**
	 * Get fullname
	 * @return string
	 */
	public function getFio() {
		$tmp = [];
		if ($this->last_name)
			$tmp[] = $this->last_name;
		if ($this->first_name)
			$tmp[] = $this->first_name;
		if ($this->middle_name)
			$tmp[] = $this->middle_name;
		return implode(' ', $tmp);
	}

    /**
     * @return string|null
     */
	public function getWallet_link() {
	    $link = null;
	    if ($this->wallet_type && !is_null($this->wallet_number)) {
            switch ($this->wallet_type) {
                case WalletType::YANDEX:
                    $link = "https://money.yandex.ru/to/".$this->wallet_number;
                    break;
                case WalletType::PAYPAL:
                    $link = "https://paypal.me/".$this->wallet_number;
                    break;
            }
        }
        return $link;
    }

	/**
	 * @inheritdoc
	 */
	public function beforeSave($insert) {
		if (parent::beforeSave($insert)) {
			return true;
		}
		return false;
	}
}
