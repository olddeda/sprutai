<?php

namespace common\modules\base\extensions\phoneInput;

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\widgets\InputWidget;
use yii\web\JsExpression;

/**
 * Widget of the phone input
 * @package common\base\modules\extensions\phoneInput
 */
class PhoneInput extends InputWidget
{
    /**
	 * @var string HTML tag type of the widget input ("tel" by default)
	 */
    public $htmlTagType = 'tel';

    /**
	 * @var array Default widget options of the HTML tag
	 */
    public $defaultOptions = ['autocomplete' => "off"];

    /**
	 * @var array Options of the JS-widget
	 */
    public $jsOptions = [];

	/**
	 * @var bool Detect country
	 */
	public $auto = false;

	/**
	 * @inheritdoc
	 *
	 * @throws \yii\base\InvalidConfigException
	 */
    public function init() {
        parent::init();

        PhoneInputAsset::register($this->view);
        PhoneInputCustomAsset::register($this->view);

        $id = ArrayHelper::getValue($this->options, 'id');

		if ($this->auto) {
			$this->jsOptions['autoFormat'] = true;
			$this->jsOptions['defaultCountry'] = 'auto';
			$this->jsOptions['geoIpLookup'] = new JsExpression('function(callback) {
    			$.get("http://ipinfo.io", function() {}, "jsonp").always(function(resp) {
      				var countryCode = (resp && resp.country) ? resp.country : "";
      				callback(countryCode);
    			});
			}');
		}

        $jsOptions = $this->jsOptions ? Json::encode($this->jsOptions) : "";

        $this->view->registerJs("$('#$id').intlTelInput($jsOptions);");
		$this->view->registerJs("initPhoneInputCustom(jQuery('#{$id}'), {$jsOptions});");
    }

    /**
     * @return string
     */
    public function run() {
        $options = ArrayHelper::merge($this->defaultOptions, $this->options);
        if ($this->hasModel()) {
            return Html::activeInput($this->htmlTagType, $this->model, $this->attribute, $options);
        }
        return Html::input($this->htmlTagType, $this->name, $this->value, $options);
    }
}