<?php
namespace common\modules\base\extensions\dadataru;

use yii\web\AssetBundle;

class DaDataRuAssetIE10 extends AssetBundle
{
	/**
	 * @var array
	 */
    public $js = [
	    '//cdnjs.cloudflare.com/ajax/libs/jquery-ajaxtransport-xdomainrequest/1.0.1/jquery.xdomainrequest.min.js',
    ];
    
    public $jsOptions = [
    	'condition' => 'IE 10'
    ];
	
	/**
	 * @var array
	 */
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
