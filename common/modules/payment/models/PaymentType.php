<?php
namespace common\modules\payment\models;

use Yii;

use common\modules\base\components\ActiveRecord;

use common\modules\payment\models\query\PaymentTypeQuery;

/**
 * This is the model class for table "{{%payment_type}}".
 *
 * @property int $id
 * @property int $kind
 * @property string $title
 * @property string $descr
 * @property string $identifier
 * @property number $price
 * @property number $price_tax
 * @property bool $price_fixed
 * @property bool $price_free
 * @property bool $physical
 * @property int $status
 * @property int $created_by
 * @property int $updated_by
 * @property int $created_at
 * @property int $updated_at
 */
class PaymentType extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return '{{%payment_type}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
	        [['kind', 'status', 'created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
            [['title', 'price'], 'required'],
	        [['title', 'identifier'], 'string', 'max' => 255],
            [['descr'], 'string'],
	        [['price', 'tax'], 'number'],
	        [['price'], 'default', 'value' => 1.0],
	        [['price_fixed', 'price_free', 'physical'], 'boolean'],
	        [['identifier'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('payment-type', 'field_id'),
			'kind' => Yii::t('payment-type', 'field_kind'),
            'title' => Yii::t('payment-type', 'field_title'),
            'descr' => Yii::t('payment-type', 'field_descr'),
	        'identifier' => Yii::t('payment-type', 'field_identifier'),
	        'price' => Yii::t('payment-type', 'field_price'),
			'price_tax' => Yii::t('payment-type', 'field_price_tax'),
	        'price_fixed' => Yii::t('payment-type', 'field_price_fixed'),
			'price_free' => Yii::t('payment-type', 'field_price_free'),
	        'physical' => Yii::t('payment-type', 'field_physical'),
            'status' => Yii::t('payment-type', 'field_status'),
            'created_by' => Yii::t('payment-type', 'field_created_by'),
            'updated_by' => Yii::t('payment-type', 'field_updated_by'),
            'created_at' => Yii::t('payment-type', 'field_created_at'),
            'updated_at' => Yii::t('payment-type', 'field_updated_at'),
        ];
    }

    /**
     * {@inheritdoc}
     * @return \common\modules\payment\models\query\PaymentTypeQuery the active query used by this AR class.
     */
    public static function find() {
        return new PaymentTypeQuery(get_called_class());
    }
	
	/**
	 * Find by identifier
	 *
	 * @param string $name
	 * @param bool $except
	 *
	 * @return mixed|null
	 * @throws \Throwable
	 * @throws \yii\web\NotFoundHttpException
	 */
    static public function findByIdentifier(string $name, bool $except = false) {
    	return self::findByColumn('identifier', $name, $except, 'payment-type');
    }
}
