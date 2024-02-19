<?php
namespace common\modules\base\extensions\editable\bundles;

use yii\web\AssetBundle;

/**
 * Class EditableAddressAsset
 *
 * @package common\modules\base\extensions\editable\bundles
 */
class EditableAddressAsset extends AssetBundle
{
    /**
     * @var string
     */
    public $sourcePath = '@common/modules/base/extensions/editable/assets/address';

    /**
     * @var array
     */
    public $css = [
        'bootstrap-editable-address.css',
    ];

    /**
     * @var array
     */
    public $js = [
        'bootstrap-editable-address.js',
    ];

    /**
     * @var array
     */
    public $depends = [
        'common\modules\base\extensions\editable\bundles\EditableBootstrapAsset',
    ];
}
