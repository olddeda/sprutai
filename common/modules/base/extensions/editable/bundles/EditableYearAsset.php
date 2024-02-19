<?php
namespace common\modules\base\extensions\editable\bundles;

use yii\web\AssetBundle;

/**
 * Class EditableYearAsset
 *
 * @package common\modules\base\extensions\editable\bundles
 */
class EditableYearAsset extends AssetBundle
{
    /**
     * @var string
     */
    public $sourcePath = '@common/modules/base/extensions/editable/assets/year';

    /**
     * @var array
     */
    public $css = [
        'bootstrap-editable-year.css',
    ];

    /**
     * @var array
     */
    public $js = [
        'bootstrap-editable-year.js',
    ];

    /**
     * @var array
     */
    public $depends = [
        'common\modules\base\extensions\editable\bundles\EditableBootstrapAsset',
    ];
}
