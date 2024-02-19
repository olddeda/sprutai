<?php

namespace common\modules\base\extensions\editable\bundles;

use yii\web\AssetBundle;

/**
 * Class EditableComboDateAsset
 *
 * @package common\modules\base\extensions\editable\bundles
 */
class EditableComboDateAsset extends AssetBundle
{
    /**
     * @var string
     */
    public $sourcePath = '@common/modules/base/extensions/editable/assets/combodate';

    /**
     * @var array
     */
    public $js = [
        'vendor/moment-with-langs.min.js',
        'vendor/combodate.js',
        'bootstrap-editable-combodate.js',
    ];

    /**
     * @var array
     */
    public $depends = [
        'common\modules\base\extensions\editable\bundles\EditableBootstrapAsset',
    ];
}
