<?php
namespace common\modules\base\helpers;

use yii\helpers\Url;

class Html extends \yii\helpers\Html
{
	public static function a($text, $url = null, $options = []) {
		if ($url !== null) {
			$options['href'] = Url::to($url);
		}
		if (isset($options['icon'])) {
			$text = Html::tag('span', '', ['class' => $options['icon']]).' '.$text;
		}
		return static::tag('a', $text, $options);
	}
}

