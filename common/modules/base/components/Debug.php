<?php
namespace common\modules\base\components;

use Yii;
use yii\helpers\VarDumper;

class Debug extends VarDumper
{
	public static function dump($var, $depth = 10, $highlight = false) {
		echo parent::dump($var, $depth, true);
	}
}

