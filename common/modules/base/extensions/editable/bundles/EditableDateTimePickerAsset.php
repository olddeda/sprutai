<?php

namespace common\modules\base\extensions\editable\bundles;

use yii\web\AssetBundle;

/**
 * Class EditableDateTimePickerAsset
 *
 * @package common\modules\base\extensions\editable\bundles
 */
class EditableDateTimePickerAsset extends AssetBundle
{
    /**
     * @var string
     */
    public $sourcePath = '@common/modules/base/extensions/editable/assets/datetimepicker';

    /**
     * @var array
     */
    public $depends = [
        'common\modules\base\extensions\editable\bundles\EditableBootstrapAsset',
    ];

    /**
     * Init object
     */
    public function init()
    {
        $this->css[] = 'vendor/css/bootstrap-datetimepicker.min.css';
        $this->js[] = 'vendor/js/bootstrap-datetimepicker.min.js';
        $this->js[] = 'bootstrap-editable-datetimepicker.js';
        parent::init();
    }
}
