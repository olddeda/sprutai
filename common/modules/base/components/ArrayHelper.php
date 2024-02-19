<?php
namespace common\modules\base\components;

use yii\helpers\ArrayHelper as BaseArrayHelper;

class ArrayHelper extends BaseArrayHelper
{
	public static function inArrayKeyValue($array, $key, $keyValue) {
		$withinArray = false;
		foreach ($array as $k => $v) {
			if (is_array($v)) {
				$withinArray = self::inArray($v, $key, $keyValue);
				if ($withinArray == true) {
					break;
				}
			}
			else {
				if ($v == $keyValue && $k == $key) {
					$withinArray = true;
					break;
				}
			}
		}
		return $withinArray;
	}
	
	public static function inArray($needle, $haystack) {
		$found = false;
		foreach ($haystack as $item) {
			if ($item === $needle) {
				$found = true;
				break;
			} elseif (is_array($item)) {
				$found = in_array_r($needle, $item);
				if($found) {
					break;
				}
			}
		}
		return $found;
	}
	
	public static function search($array, $key, $value, $first = false) {
		$results = [];
		
		if (is_array($array)) {
			if (isset($array[$key]) && $array[$key] == $value) {
				$results[] = $array;
			}
			
			foreach ($array as $subarray) {
				$results = array_merge($results, self::search($subarray, $key, $value));
			}
		}
		
		if (is_object($array)) {
			if (isset($array->{$key}) && $array->{$key} == $value) {
				$results[] = $array;
			}
			
			foreach ($array as $subarray) {
				$results = array_merge($results, self::search($subarray, $key, $value));
			}
		}
		
		return ($first) ? current($results) : $results;
	}
	
}
