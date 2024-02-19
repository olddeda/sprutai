<?php
namespace common\modules\base\extensions\contentbuilder;

use yii\web\AssetBundle;

class ContentBuilderSimpleLightBoxAsset extends AssetBundle
{
	/**
	 * @var string
	 */
    public $sourcePath = '@common/modules/base/extensions/contentbuilder/vendor/simplelightbox';
	
	/**
	 * @var array
	 */
    public $js = [
        'simplelightbox.js',
    ];
	
	/**
	 * @var array
	 */
    public $css = [
        'simplelightbox.css',
    ];
	
	/**
	 * @var array
	 */
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
