<?php
namespace common\modules\base\extensions\editable;

use common\modules\base\components\Debug;
use yii\base\InvalidConfigException;
use yii\grid\DataColumn;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\Json;
use yii\web\JsExpression;

use common\modules\base\extensions\editable\bundles\EditableAddressAsset;
use common\modules\base\extensions\editable\bundles\EditableBootstrapAsset;
use common\modules\base\extensions\editable\bundles\EditableComboDateAsset;
use common\modules\base\extensions\editable\bundles\EditableDatePickerAsset;
use common\modules\base\extensions\editable\bundles\EditableDateTimePickerAsset;
use common\modules\base\extensions\editable\bundles\EditablePriceAsset;
use common\modules\base\extensions\editable\bundles\EditableSelect2Asset;
use common\modules\base\extensions\editable\bundles\EditableSelectizeAsset;
use common\modules\base\extensions\editable\bundles\EditablePhoneAsset;
use common\modules\base\extensions\editable\bundles\EditableYearAsset;

/**
 * Class EditableColumn
 *
 * @package common\modules\base\extensions\editable
 */
class EditableColumn extends DataColumn
{
	/**
	 * Editable options
	 */
	public $editableOptions = [];
	
	/**
	 * @var array the options for the X-editable.js plugin
	 */
	public $pluginOptions = [];
	
	/**
	 * @var string suffix substituted to a name class of the tag <a>
	 */
	public $classSuffix;
	
	/**
	 * @var string the url to post
	 */
	public $url = ['editable'];
	
	/**
	 * @var string the type of editor
	 */
	public $type = 'text';
	
	/**
	 * @inheritdoc
	 *
	 * @throws \yii\base\InvalidConfigException
	 */
	public function init() {
		if ($this->url === null) {
			throw new InvalidConfigException('Url can not be empty.');
		}
		parent::init();
		
		if (!$this->format) {
			$this->format = 'raw';
		}
		
		$rel = $this->attribute.'_editable'.$this->classSuffix;
		$this->options['pjax'] = '0';
		$this->options['rel'] = $rel;
		
		$this->registerClientScript();
	}
	
	/**
	 * Renders the data cell content.
	 *
	 * @param mixed $model the data model
	 * @param mixed $key the key associated with the data model
	 * @param int $index the zero-based index of the data model among the models array returned by [[GridView::dataProvider]]
	 *
	 * @return string the rendering result
	 */
	protected function renderDataCellContent($model, $key, $index) {
		$value = parent::renderDataCellContent($model, $key, $index);
		$url = (array)$this->url;
		$this->options['data-url'] = Url::to($url);
		$this->options['data-pk'] = $key;
		$this->options['data-name'] = $this->attribute;
		$this->options['data-type'] = $this->type;
		
		if (is_callable($this->editableOptions)) {
			$opts = call_user_func($this->editableOptions, $model, $key, $index);
			
			$source = ArrayHelper::getValue($opts, 'source', []);
			$tmp = [];
			if (count($source)) {
				foreach ($source as $key => $val) {
					$tmp[] = [
						'id' => $key,
						'text' => $val,
					];
				}
			}
			$opts['source'] = $tmp;
			
			foreach ($opts as $prop => $v) {
				$this->options['data-'.$prop] = $v;
			}
		}
		elseif (is_array($this->editableOptions)) {
			foreach ($this->editableOptions as $prop => $v) {
				$this->options['data-'.$prop] = $v;
			}
		}
		
		return Html::a($value, null, $this->options);
	}
	
	/**
	 * Registers required script to the columns work
	 */
	protected function registerClientScript() {
		$view = $this->grid->getView();
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
			case 'select2':
				EditableSelect2Asset::register($view);
				$this->pluginOptions['inputclass'] = 'form-control';
				
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
				$this->pluginOptions['inputclass'] = 'width-400 input-sm';
				break;
		}
		
		$pluginOptions = $this->getPluginOptions();
		
		$rel = $this->options['rel'];
		$selector = "a[rel=\"$rel\"]";
		$js[] = "jQuery('$selector').editable($pluginOptions);";
		$view->registerJs(implode("\n", $js));
	}
	
	/**
	 * Return plugin options in json format
	 *
	 * @return string
	 */
	public function getPluginOptions() {
		$this->pluginOptions['params']['_csrf'] = \Yii::$app->request->csrfToken;
		return Json::encode($this->pluginOptions);
	}
}
