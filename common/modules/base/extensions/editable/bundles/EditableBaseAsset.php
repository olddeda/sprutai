<?php
namespace common\modules\base\extensions\editable\bundles;

use yii\web\AssetBundle;

/**
 * Class EditableBaseAsset
 *
 * @package common\modules\base\extensions\editable\bundles
 */
class EditableBaseAsset extends AssetBundle
{
    /**
     * @var string
     */
    public $sourcePath = '@common/modules/base/extensions/editable/assets/base';

    /**
     * @var array
     */
    public $css = [
        'bootstrap-editable-base.css',
    ];

    /**
     * @var array
     */
    public $js = [
        'bootstrap-editable-base.js',
    ];
}
