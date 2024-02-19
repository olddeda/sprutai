<?php
namespace common\modules\base\widgets\year;

use yii\web\AssetBundle;

class YearWidgetAsset extends AssetBundle
{
	/**
	 * @var string
	 */
    public $sourcePath = '@common/modules/base/widgets/year/assets';
	
	/**
	 * @var array
	 */
    public $css = [
        'year-widget.css',
    ];
	
	/**
	 * @var array
	 */
	public $depends = [
		'yii\web\JqueryAsset',
	];
}
