<?php

namespace common\modules\base\extensions\widget\fileinput;

use Yii;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;

use common\modules\base\extensions\widget\base\InputWidget;
use common\modules\base\extensions\widget\base\TranslationTrait;

/**
 * FileInput widget styled for Bootstrap 3.0 with ability to multiple file
 * selection and preview, format button styles and inputs. Runs on all modern
 * browsers supporting HTML5 File Inputs and File Processing API. For browser
 * versions IE9 and below, this widget will gracefully degrade to normal HTML
 * file input.
 *
 * Wrapper for the Bootstrap FileInput JQuery Plugin by Krajee
 *
 * @see http://plugins.krajee.com/bootstrap-fileinput
 * @see https://github.com/kartik-v/bootstrap-fileinput
 *
 * @since 1.0
 */
class FileInput extends InputWidget
{
	use TranslationTrait;

	/**
	 * @var bool whether to show 'plugin unsupported' message for IE browser versions 9 & below
	 */
	public $showMessage = true;

	/*
	 * @var array HTML attributes for the container for the warning
	 * message for browsers running IE9 and below.
	 */
	public $messageOptions = [
		'class' => 'alert alert-warning'
	];

	/**
	 * @var array the internalization configuration for this widget
	 */
	public $i18n = [];

	/**
	 * @var array initialize the FileInput widget
	 */
	public function init() {
		parent::init();

		$this->_messageCategory = 'fileinput';

		$this->initI18N(__DIR__);
		$this->initLanguage();

		$this->registerAssets();

		if ($this->pluginLoading) {
			Html::addCssClass($this->options, 'file-loading');
		}

		$input = $this->getInput('fileInput');
		$script = 'document.getElementById("'.$this->options['id'].'").className.replace(/\bfile-loading\b/,"");';

		if ($this->showMessage) {
			$validation = ArrayHelper::getValue($this->pluginOptions, 'showPreview', true) ? 'file preview and multiple file upload' : 'multiple file upload';
			$message = '<strong>'.Yii::t('fileinput', 'Note:').'</strong> '.Yii::t('fileinput', 'Your browser does not support {validation}. Try an alternative or more recent browser to access these features.', ['validation' => $validation]);
			$content = Html::tag('div', $message, $this->messageOptions)."<script>{$script};</script>";
			$input .= "\n".$this->validateIE($content);
		}
		echo $input;
	}

	/**
	 * Validates and returns content based on IE browser version validation
	 *
	 * @param string $content
	 * @param string $validation
	 *
	 * @return string
	 */
	protected function validateIE($content, $validation = 'lt IE 10') {
		return "<!--[if {$validation}]><br>{$content}<![endif]-->";
	}

	/**
	 * Registers the asset bundle and locale
	 */
	public function registerAssetBundle() {
		$view = $this->getView();
		FileInputAsset::register($view)->addLanguage($this->language, 'fileinput_locale_');
	}

	/**
	 * Registers the needed assets
	 */
	public function registerAssets() {
		$this->registerAssetBundle();
		$this->registerPlugin('fileinput');
	}
}