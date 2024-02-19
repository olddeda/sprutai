<?php

namespace common\modules\base\helpers;

class Arr extends \yii\base\Component {
	
	/**
	 * Max value in array by key
	 * @param $array
	 * @param $keyToSearch
	 *
	 * @return int
	 */
	static public function minByKey($array, $keyToSearch) {
		$val = PHP_INT_MAX;
		foreach ($array as $arr) {
			foreach ($arr as $key => $value) {
				if ($key == $keyToSearch && ($value <= $val)) {
					$val = $value;
				}
			}
		}
		return $val;
	}
	
	/**
	 * Max value in array by key
	 * @param $array
	 * @param $keyToSearch
	 *
	 * @return int
	 */
	static public function maxByKey($array, $keyToSearch) {
		$val = 0;
		foreach ($array as $arr) {
			foreach ($arr as $key => $value) {
				if ($key == $keyToSearch && ($value >= $val)) {
					$val = $value;
				}
			}
		}
		return $val;
	}
}