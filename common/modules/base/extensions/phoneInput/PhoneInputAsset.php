<?php

namespace common\modules\base\extensions\phoneInput;

use yii\web\AssetBundle;

/**
 * Asset Bundle of the phone input widget. Registers required CSS and JS files.
 * @package common\modules\base\extensions\phoneInput;
 */
class PhoneInputAsset extends AssetBundle
{
	/**
	 * @inheritdoc
	 **/
    public $sourcePath = '@bower/intl-tel-input';

	/**
	 * @inheritdoc
	 **/
    public $css = ['build/css/intlTelInput.css'];

	/**
	 * @inheritdoc
	 **/
    public $js = [
        'build/js/utils.js',
        'build/js/intlTelInput.min.js',
    ];

	/**
	 * @inheritdoc
	 **/
    public $depends = ['yii\web\JqueryAsset'];
}