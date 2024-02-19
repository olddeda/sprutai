<?php
namespace common\modules\company\models;

use Yii;
use yii\helpers\Url;

use common\modules\base\components\ActiveRecord;
use common\modules\base\components\Debug;

use common\modules\company\models\query\CompanyAddressQuery;

/**
 * CompanyAddress Active Record model.
 *
 * @property integer $id
 * @property integer $company_id
 * @property boolean $is_primary
 * @property string $address
 * @property string $postal_code
 * @property string $country
 * @property string $region
 * @property string $city
 * @property string $street
 * @property string $house
 * @property string $block
 * @property string $flat
 * @property string $metro
 * @property integer $created_at
 * @property integer $updated_at
 *
 * Defined relations:
 * @property \common\modules\company\models\Company $company
 *
 */
class CompanyAddress extends ActiveRecord
{
	/**
	 * @inheritdoc
	 */
	public static function tableName() {
		return '{{%company_address}}';
	}
	
	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[['company_id', 'address', 'country', 'city', 'street', 'house'], 'required'],
			[['is_primary'], 'boolean'],
			[['postal_code'], 'string', 'max' => 10],
			[['house', 'block', 'flat'], 'string', 'max' => 20],
			[['country', 'region', 'city', 'street', 'metro'], 'string', 'max' => 100],
			[['address'], 'string', 'max' => 1000],
			[['address', 'postal_code', 'country', 'region', 'city', 'street', 'block', 'flat'], 'filter', 'filter' => '\yii\helpers\HtmlPurifier::process'],
			[['address', 'postal_code', 'country', 'region', 'city', 'street', 'block', 'flat'], 'filter', 'filter' => '\yii\helpers\Html::encode'],
		];
	}
	
	/**
	 * @inheritdoc
	 */
	public function attributeLabels() {
		return [
			'id' => Yii::t('company-address', 'field_id'),
			'company_id' => Yii::t('company-address', 'field_company_id'),
			'is_primary' => Yii::t('company-address', 'field_is_primary'),
			'address' => Yii::t('company-address', 'field_address'),
			'postal_code' => Yii::t('company-address', 'field_postal_code'),
			'country' => Yii::t('company-address', 'field_country'),
			'region' => Yii::t('company-address', 'field_region'),
			'city' => Yii::t('company-address', 'field_city'),
			'street' => Yii::t('company-address', 'field_street'),
			'house' => Yii::t('company-address', 'field_house'),
			'block' => Yii::t('company-address', 'field_block'),
			'flat' => Yii::t('company-address', 'field_flat'),
			'metro' => Yii::t('company-address', 'field_metro'),
			'created_at' => Yii::t('company-address', 'field_created_at'),
			'updated_at' => Yii::t('company-address', 'field_updated_at'),
		];
	}
	
	/**
	 * {@inheritdoc}
	 * @return \common\modules\user\models\query\CompanyAddressQuery the active query used by this AR class.
	 */
	public static function find() {
		return new CompanyAddressQuery(get_called_class());
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getCompany() {
		return $this->hasOne(Company::class, ['id' => 'company_id']);
	}
	
	/**
	 * Set model as primary
	 */
	public function primary() {
		self::updateAll([
			'is_primary' => false,
		], 'company_id = :company_id', [
			':company_id' => $this->company_id,
		]);
		$this->is_primary = true;
		$this->save();
	}
	
	/**
	 * @param bool $useStatus
	 *
	 * @return false|int|void
	 */
	public function delete($useStatus = true) {
		parent::delete(false);
		
		if (!self::find()->where('company_id = :company_id AND is_primary = :is_primary', [
			':company_id' => $this->company_id,
			':is_primary' => true,
		])->count()) {
			$model = self::find()->where('company_id = :company_id', [
				':company_id' => $this->company_id,
			])->one();
			if ($model) {
				$model->is_primary = true;
				$model->save();
			}
		}
	}
	
	
	/**
	 * @param $insert
	 *
	 * @return bool
	 */
	public function beforeSave($insert) {
		$tmp = [
			$this->postal_code,
			$this->country,
		];
		
		if ($this->region != $this->city)
			$tmp[] = $this->region;
		$tmp[] = $this->city;
		$tmp[] = $this->street;
		$tmp[] = $this->house;
		$tmp[] = $this->block;
		$tmp[] = $this->flat;
		
		$a = [];
		foreach ($tmp as $f) {
			if ($f)
				$a[] = $f;
		}
		$this->address = implode(', ', $a);
		
		return parent::beforeSave($insert);
	}
}
