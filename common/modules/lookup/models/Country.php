<?php
namespace common\modules\lookup\models;

class Country extends Lookup
{
	/**
	 * @return int
	 */
	public static function type() {
		return self::TYPE_COUNTRY;
	}
	
	/**
	 * @return \common\modules\lookup\models\query\LookupQuery
	 */
	public function getCities() {
		return $this->hasMany(City::className(), ['id' => 'parent_id']);
	}
}