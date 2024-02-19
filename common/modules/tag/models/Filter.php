<?php
namespace common\modules\tag\models;

use common\modules\tag\helpers\enum\Type;

class Filter extends Tag
{
	/**
	 * @return int
	 */
	public static function type() {
		return Type::FILTER;
	}
}