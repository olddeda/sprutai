<?php
namespace common\modules\base\components;

use yii\i18n\Formatter as BaseFormatter;

class Formatter extends BaseFormatter {

	/**
	 * Format as bytes
	 * @param $bytes
	 * @param int $precision
	 *
	 * @return string
	 */
	public function asBytes($bytes, $precision = 2) {
		$units = array('b', 'kb', 'mb', 'gb', 'tb');

		$bytes = max($bytes, 0);
		$pow = floor(($bytes ? log($bytes) : 0) / log(1024));
		$pow = min($pow, count($units) - 1);

		// Uncomment one of the following alternatives
		//$bytes /= pow(1024, $pow);
		$bytes /= (1 << (10 * $pow));

		return round($bytes, $precision) . ' ' . $units[$pow];
	}
	
	/**
	 * @inheritDoc
	 */
	public function asPercent($value, $decimals = null, $options = [], $textOptions = []) {
		$result = parent::asPercent($value, $decimals, $options, $textOptions);
		return (int)$result.'%';
	}
}