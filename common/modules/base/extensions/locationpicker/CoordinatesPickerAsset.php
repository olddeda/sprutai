<?php
namespace common\modules\base\extensions\locationpicker;

use yii\web\AssetBundle;

/**
 * CoordinatesPickerAsset
 */
class CoordinatesPickerAsset extends AssetBundle {
    public $sourcePath = '@common/modules/base/extensions/locationpicker/assets';
    public $css = [
        'coordinates-picker.css'
    ];
}
