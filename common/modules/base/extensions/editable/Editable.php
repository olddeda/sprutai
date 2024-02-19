<?php
namespace common\modules\base\extensions\editable;

use common\modules\base\components\Debug;
use yii\base\InvalidConfigException;
use yii\db\ActiveRecordInterface;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\widgets\InputWidget;

use common\modules\base\extensions\editable\bundles\EditableBootstrapAsset;
use common\modules\base\extensions\editable\bundles\EditableBaseAsset;
use common\modules\base\extensions\editable\bundles\EditableAddressAsset;
use common\modules\base\extensions\editable\bundles\EditableComboDateAsset;
use common\modules\base\extensions\editable\bundles\EditableDatePickerAsset;
use common\modules\base\extensions\editable\bundles\EditableDateTimePickerAsset;
use common\modules\base\extensions\editable\bundles\EditablePriceAsset;
use common\modules\base\extensions\editable\bundles\EditableGoogleMapAsset;
use common\modules\base\extensions\editable\bundles\EditableSelect2Asset;
use common\modules\base\extensions\editable\bundles\EditableSelectizeAsset;
use common\modules\base\extensions\editable\bundles\EditablePhoneAsset;
use common\modules\base\extensions\editable\bundles\EditableYearAsset;

/**
 * Class Editable
 *
 * @package common\modules\base\extensions\editable
 */
class Editable extends InputWidget
{
	/**
	 * @var string the type of input. Type of input
	 */
	public $type = 'text';
	
	/**
	 * @var string the Mode of editable, can be popup or inline
	 */
	public $mode = 'inline';
	
	/**
	 * @var string|array Url for submit, e.g. '/post'
	 */
	public $url;
	
	/**
	 * @var array the options for the X-editable.js plugin
	 */
	public $pluginOptions = [];
	
	/**
	 * @var array the event handlers for the X-editable.js plugin
	 */
	public $clientEvents = [];
	
	/** @var boolean show as button */
	public $button;
	
	/** @var string button class */
	public $buttonClass = 'btn btn-default';
	
	/** @var string button text */
	public $buttonText;
	
	/** @var array button options */
	public $buttonOptions = [];
	
	/**
	 * Initializes the widget.
	 */
	public function init() {
		parent::init();
		
		if ($this->url === null) {
			throw new InvalidConfigException("You must setup the 'Url' property.");
		}
		
		if (!isset($this->options['id'])) {
			$this->options['id'] = $this->hasModel() ? Html::getInputId($this->model, $this->attribute) : $this->getId();
		}
	}
	
	/**
	 * Executes the widget.
	 */
	public function run() {
		$linkText = $this->getLinkText();
		
		if ($this->button) {
			if (!isset($this->buttonOptions['pjaxId']))
				$this->buttonOptions['pjaxId'] = '#p0';
			
			echo Html::beginTag('div', ['class' => 'editable-button']);
			echo Html::a($linkText, null, ArrayHelper::merge($this->options, ['class' => 'editable-button-input']));
			echo Html::button($this->buttonText, [
				'id' => $this->options['id'].'-button',
				'class' => $this->buttonClass.' editable-button-toggle',
				'disabled' => true,
			]);
			echo Html::endTag('div');
		}
		else {
			echo Html::a($linkText, null, $this->options);
		}
		
		$this->registerClientScript();
	}
	
	/**
	 * Register client script
	 */
	protected function registerClientScript() {
		$view = $this->getView();
		
		switch ($this->type) {
			case 'address':
				EditableAddressAsset::register($view);
				break;
			case 'combodate':
				EditableComboDateAsset::register($view);
				break;
			case 'date':
				EditableDatePickerAsset::register($view);
				break;
			case 'datetime':
				EditableDateTimePickerAsset::register($view);
				break;
			case 'price':
				EditablePriceAsset::register($view);
				break;
			case 'googlemap':
				EditableGoogleMapAsset::register($view);
				break;
			case 'select2':
				EditableSelect2Asset::register($view);
				
				// Hide search support
				if (isset($this->pluginOptions['select2']) && isset($this->pluginOptions['select2']['hideSearch']) && $this->pluginOptions['select2']['hideSearch']) {
					$this->pluginOptions['select2']['minimumResultsForSearch'] = new JsExpression('Infinity');
					unset($this->pluginOptions['select2']['hideSearch']);
				}
				
				break;
			case 'selectize':
				EditableSelectizeAsset::register($view);
				break;
			case 'phone':
				EditablePhoneAsset::register($view);
				break;
			case 'year':
				EditableYearAsset::register($view);
				break;
			default:
				EditableBootstrapAsset::register($view);
				break;
		}
		
		EditableBaseAsset::register($view);
		
		$id = ArrayHelper::remove($this->pluginOptions, 'selector', '#'.$this->options['id']);
		$id = preg_replace('/([.])/', '\\\\\\\$1', $id);
		
		if ($this->hasActiveRecord() && $this->model->isNewRecord) {
			$this->pluginOptions['send'] = 'always'; // send to server without pk
		}
		
		$pluginOptions = $this->getPluginOptions();
		
		$js = "jQuery('$id').editable($pluginOptions);";
		$view->registerJs($js);
		
		if ($this->button) {
			$buttonOptions = Json::encode($this->buttonOptions);
			$view->registerJs("initEditableButton('{$id}', {$buttonOptions});");
		}
		
		if (!empty($this->clientEvents)) {
			$this->registerClientEvents($id);
		}
	}
	
	/**
	 * Return plugin options in json format
	 *
	 * @return string
	 */
	public function getPluginOptions() {
		$pk = ArrayHelper::getValue($this->pluginOptions, 'pk', $this->hasActiveRecord() ? $this->model->getPrimaryKey() : null);
		$this->pluginOptions['pk'] = $pk;
		$this->pluginOptions['url'] = $this->url instanceof JsExpression ? $this->url : Url::toRoute($this->url);
		$this->pluginOptions['type'] = $this->type;
		$this->pluginOptions['mode'] = $this->mode;
		$this->pluginOptions['name'] = $this->attribute ?: $this->name;
		$this->pluginOptions['_csrf'] = \Yii::$app->request->csrfToken;
		
		if ($this->button) {
			$this->pluginOptions['toggle'] = 'manual';
		}
		
		return Json::encode($this->pluginOptions);
	}
	
	/**
	 * Register client events
	 *
	 * @param $id
	 */
	public function registerClientEvents($id) {
		$view = $this->getView();
		$js = [];
		foreach ($this->clientEvents as $event => $handler) {
			$js[] = "jQuery('$id').on('$event', $handler);";
		}
		$view->registerJs(implode("\n", $js));
	}
	
	/**
	 * Return link text
	 *
	 * @return mixed|string
	 */
	protected function getLinkText() {
		$value = $this->value;
		if ($this->hasModel()) {
			$model = $this->model;
			if ($value !== null) {
				if (is_string($value)) {
					$linkText = ArrayHelper::getValue($model, $value);
				}
				else {
					$linkText = call_user_func($value, $model);
				}
			}
			else {
				$linkText = ArrayHelper::getValue($model, $this->attribute);
			}
		}
		else {
			$linkText = $value;
		}
		
		return $linkText;
	}
	
	/**
	 * To ensure that `getPrimaryKey()` and `getIsNewRecord()` methods are implemented in model.
	 *
	 * @return bool
	 */
	protected function hasActiveRecord() {
		return $this->hasModel() && $this->model instanceof ActiveRecordInterface;
	}
}
