<?php
namespace common\modules\base\widgets\price;

use yii\web\AssetBundle;

class PriceWidgetAsset extends AssetBundle
{
	/**
	 * @var string
	 */
    public $sourcePath = '@common/modules/base/widgets/price/assets';
	
	/**
	 * @var array
	 */
    public $js = [
        'price-widget.js',
    ];
	
	/**
	 * @var array
	 */
    public $css = [
        'price-widget.css',
    ];
	
	/**
	 * @var array
	 */
	public $depends = [
		'yii\web\JqueryAsset',
	];
}
