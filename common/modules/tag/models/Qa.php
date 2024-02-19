<?php
namespace common\modules\tag\models;

use common\modules\tag\helpers\enum\Type;

class Qa extends Tag
{
	/**
	 * @return int
	 */
	public static function type() {
		return Type::QA;
	}
}