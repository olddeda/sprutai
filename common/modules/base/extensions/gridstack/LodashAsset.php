<?php
namespace common\modules\base\extensions\gridstack;

use yii\web\AssetBundle;

/**
 * Asset for the lodash library
 */
class LodashAsset extends AssetBundle 
{
    public $sourcePath = '@common/modules/base/extensions/gridstack/assets';

    public $js = [
        'lodash.js',
    ];
}
