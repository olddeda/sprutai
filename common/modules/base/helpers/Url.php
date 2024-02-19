<?php
namespace common\modules\base\helpers;

use yii\helpers\Url as BaseUrl;
use yii\helpers\ArrayHelper;

class Url extends BaseUrl
{
	static public function toRouteParams($route, $params = []) {
		return ArrayHelper::merge($route, $params);
	}
	
	/**
	 * Creates a path info based on the given parameters.
	 * @param array $params list of GET parameters
	 * @param string $equal the separator between name and value
	 * @param string $ampersand the separator between name-value pairs
	 * @param string $key this is used internally.
	 * @return string the created path info
	 */
	static public function createPathInfo($params, $equal, $ampersand, $key = null) {
		$pairs = [];
		foreach($params as $k => $v)  {
			if ($key !== null)
				$k = $key.'['.$k.']';
			if (is_array($v))
				$pairs[] = self::createPathInfo($v, $equal, $ampersand, $k);
			else
				$pairs[] = urlencode($k).$equal.urlencode($v);
		}
		return implode($ampersand, $pairs);
	}
}