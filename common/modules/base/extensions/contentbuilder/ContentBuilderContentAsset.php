<?php
namespace common\modules\base\extensions\contentbuilder;

use yii\web\AssetBundle;

class ContentBuilderContentAsset extends AssetBundle
{
	/**
	 * @var string
	 */
    public $sourcePath = '@common/modules/base/extensions/contentbuilder/vendor/contentbuilder/assets';
	
	/**
	 * @var array
	 */
    public $css = [
        'contentbuilder-content.css',
    ];
}
