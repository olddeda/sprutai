<?php
namespace common\modules\base\assets;

use Yii;

use yii\web\AssetBundle;

class jQueryi18nAsset extends AssetBundle
{
	public $sourcePath = '@common/modules/base/assets/jquery.i18n';
	
	public $js = [
		'src/jquery.i18n.js',
		'src/jquery.i18n.messagestore.js',
		'src/jquery.i18n.fallbacks.js',
		'src/jquery.i18n.parser.js',
		'src/jquery.i18n.emitter.js',
		'src/jquery.i18n.language.js',
	];
}
