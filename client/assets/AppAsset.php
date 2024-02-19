<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace client\assets;

use Yii;

use yii\web\AssetBundle;
use yii\bootstrap\BootstrapAsset;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
	
    public $css = [
    	'sass/bootstrap.scss',
		'sass/app.scss',
		'sass/custom.scss',
		'css/theme.css',
		'css/content.css',
		'css/comments.css',
		'css/qa.css',
	    'css/contentbuilder.css',
	    'css/prism.css',
	    'css/dadataru.css',
	    'css/vote.css',
		'css/timeline.css',
		'css/gridstack.css',
		'css/slim.css',
		//'css/card.css',
		'css/flex/flexboxgrid.css',
    ];
	
    public $js = [
		'js/app.js',
		'js/custom.js',
	    'js/vendor/lodash/lodash.min.js',
		'js/jquery.sparkline.js',
		'js/jquery.redirect.js',
		'js/jquery.isInViewport.js'
    ];
	
    public $depends = [
    	
    	// Yii
        'yii\web\YiiAsset',
		'yii\web\JqueryAsset',
		'yii\bootstrap\BootstrapAsset',
		
		'client\assets\BootstrapAsset',
		'client\assets\FontAwesomeAsset',
		'client\assets\SimpleLineIconsAsset',
		
		'client\assets\StorageApiAsset',
		'client\assets\MatchMediaAsset',
		'client\assets\AnimoAsset',
		'client\assets\SlimScrollAsset',
		'client\assets\ScreenfullAsset',
		'client\assets\MomentAsset',
		'client\assets\ScrollToAsset',
		
		'client\assets\FlotAsset',
		'client\assets\FlotTooltipAsset',
		'client\assets\FlotSplineAsset',
		'client\assets\FlotHiddenGraphs',
		
		'common\modules\base\assets\BootboxAsset',
		
		//'common\modules\base\components\bugsnag\BugsnagAsset',
    ];
}
