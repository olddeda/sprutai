<?php
namespace common\modules\payment\models;

use Yii;

use common\modules\base\components\ActiveRecord;

use common\modules\payment\models\query\PaymentTypeModuleQuery;

/**
 * This is the model class for table "{{%payment_type_link}}".
 *
 * @property int $id
 * @property int $module_type
 * @property int $module_id
 * @property int $payment_type_id
 * @property double $price
 * @property bool $price_fixed
 * @property bool $price_free
 * @property int $created_by
 * @property int $updated_by
 * @property int $created_at
 * @property int $updated_at
 *
 * Defined relations:
 * @property \common\modules\payment\models\PaymentType $type
 */
class PaymentTypeModule extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return '{{%payment_type_module}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['module_type', 'module_id', 'payment_type_id'], 'required'],
            [['module_type', 'module_id', 'payment_type_id', 'created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
	        [['price'], 'number'],
	        [['price'], 'default', 'value' => 1.0],
	        [['price_fixed', 'price_free'], 'boolean'],
        ];
    }
	
	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels() {
		return [
			'id' => Yii::t('payment-type-module', 'field_id'),
			'price' => Yii::t('payment-type-module', 'field_price'),
			'price_fixed' => Yii::t('payment-type-module', 'field_price_fixed'),
			'price_free' => Yii::t('payment-type-module', 'field_price_free'),
			'created_by' => Yii::t('payment-type-module', 'field_created_by'),
			'updated_by' => Yii::t('payment-type-module', 'field_updated_by'),
			'created_at' => Yii::t('payment-type-module', 'field_created_at'),
			'updated_at' => Yii::t('payment-type-module', 'field_updated_at'),
		];
	}

    /**
     * {@inheritdoc}
     * @return \common\modules\payment\models\query\PaymentTypeModuleQuery the active query used by this AR class.
     */
    public static function find() {
        return new PaymentTypeModuleQuery(get_called_class());
    }
	
	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getType() {
		return $this->hasOne(PaymentType::class, ['id' => 'payment_type_id']);
	}
}
