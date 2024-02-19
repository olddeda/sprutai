<?php
namespace common\modules\lookup\models;

class City extends Lookup
{
	/**
	 * @return int
	 */
	public static function type() {
		return self::TYPE_CITY;
	}
}