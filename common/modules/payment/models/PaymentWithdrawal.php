<?php
namespace common\modules\payment\models;

use Yii;
use yii\helpers\Url;

use common\modules\base\components\ActiveRecord;

use common\modules\payment\models\query\PaymentWithdrawalQuery;

use common\modules\content\models\Content;


/**
 * This is the model class for table "{{%payment_withdrawal}}".
 *
 * @property int $id
 * @property int $payment_id
 * @property int $payment_source_id
 * @property int $created_by
 * @property int $updated_by
 * @property int $created_at
 * @property int $updated_at
 *
 * Defined relations:
 * @property \common\modules\payment\models\Payment $payment
 * @property \common\modules\payment\models\Payment $paymentSource
 */
class PaymentWithdrawal extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return '{{%payment_withdrawal}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['payment_id', 'payment_source_id', 'created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
            [['payment_id', 'payment_source_id'], 'required'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('payment_withdrawal', 'field_id'),
			'payment_id' => Yii::t('payment_withdrawal', 'field_payment_id'),
			'payment_source_id' => Yii::t('payment_withdrawal', 'field_payment_source_id'),
            'created_by' => Yii::t('payment_withdrawal', 'field_created_by'),
            'updated_by' => Yii::t('payment_withdrawal', 'field_updated_by'),
            'created_at' => Yii::t('payment_withdrawal', 'field_created_at'),
            'updated_at' => Yii::t('payment_withdrawal', 'field_updated_at'),
        ];
    }

    /**
     * {@inheritdoc}
     * @return \common\modules\payment\models\query\PaymentWithdrawalQuery the active query used by this AR class.
     */
    public static function find() {
        return new PaymentWithdrawalQuery(get_called_class());
    }
	
	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getPayment() {
		return $this->hasOne(Payment::class, ['id' => 'payment_id']);
	}
	
	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getPaymentSource() {
		return $this->hasOne(Payment::class, ['id' => 'payment_source_id']);
	}
}
