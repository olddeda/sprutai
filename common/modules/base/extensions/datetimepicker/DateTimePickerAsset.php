<?php
namespace common\modules\base\extensions\datetimepicker;

use yii\web\AssetBundle;

/**
 * DateTimePickerAsset
 * @author Antonio Ramirez <amigo.cobos@gmail.com>
 * @link http://www.ramirezcobos.com/
 * @link http://www.2amigos.us/
 * @package dosamigos\datetimepicker
 */
class DateTimePickerAsset extends AssetBundle
{
    public $sourcePath = '@bower/smalot-bootstrap-datetimepicker';

    public $css = [
        'css/bootstrap-datetimepicker.css'
    ];

    public $js = [
        'js/bootstrap-datetimepicker.js'
    ];

    public $depends = [
        'yii\bootstrap\BootstrapPluginAsset'
    ];
}
