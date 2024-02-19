<?php

namespace common\modules\base\extensions\editable\bundles;

use yii\web\AssetBundle;

/**
 * Class EditableDatePickerAsset
 *
 * @package common\modules\base\extensions\editable\bundles
 */
class EditableDatePickerAsset extends AssetBundle
{
    /**
     * @var string
     */
    public $sourcePath = '@common/modules/base/extensions/editable/assets/datepicker';

    /**
     * @var array
     */
    public $css = [
        'vendor/css/datepicker3.css',
    ];

    /**
     * @var array
     */
    public $js = [
        'vendor/js/bootstrap-datepicker.js',
        'bootstrap-editable-datepicker.js',
    ];

    /**
     * @var array
     */
    public $depends = [
        'common\modules\base\extensions\editable\bundles\EditableBootstrapAsset',
    ];
}
