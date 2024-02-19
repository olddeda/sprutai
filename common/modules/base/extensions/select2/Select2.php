<?php
namespace common\modules\base\extensions\select2;

use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\JsExpression;
use yii\widgets\InputWidget;

use yii\helpers\ArrayHelper;

class Select2 extends InputWidget
{
	/**
	 * @var array
	 */
    public $items = [];

    /**
     * @var array
     * @see https://select2.github.io/options.html
     */
    public $clientOptions = [];
	
	/**
	 * @var array
	 */
    public $clientEvents = [];
    
    public $hideSearch = false;
	
	/**
	 * @inheritdoc
	 */
    public function run() {
    	$this->initPlaceholder();
        $this->registerPlugin('select2');
        Html::addCssClass($this->options, 'form-control');

        if ($this->hasModel()) {
            return Html::activeDropDownList($this->model, $this->attribute, $this->items, $this->options);
        } else {
            return Html::dropDownList($this->name, $this->value, $this->items, $this->options);
        }
    }
	
	/**
	 * @param $name
	 */
    protected function registerPlugin($name) {
        $view = $this->getView();
        Select2Asset::register($view);
		Select2CustomAsset::register($view);
        $id = $this->options['id'];
        if ($this->clientOptions !== false) {
        	
        	if (is_array($this->clientOptions)) {
        		
        		if (isset($this->clientOptions['allowClear']) && $this->clientOptions['allowClear']) {
        			$this->clientOptions['inputclass'] = 'test';
				}
        		
        		// Hide search support
				if (isset($this->clientOptions['hideSearch']) && $this->clientOptions['hideSearch']) {
					$this->clientOptions['minimumResultsForSearch'] = new JsExpression('Infinity');
					unset($this->clientOptions['hideSearch']);
				}
			}
        	
            $options = empty($this->clientOptions) ? Json::encode([]) : Json::encode($this->clientOptions);
            $js = "jQuery('#$id').$name($options);";
            $view->registerJs($js);
			$view->registerJs("initS2Custom(jQuery('#{$id}'), {$options});");
        }
        $this->registerClientEvents();
    }
	
	/**
	 * @inheritdoc
	 */
    protected function registerClientEvents() {
        if (!empty($this->clientEvents)) {
            $id = $this->options['id'];
            $js = [];
            foreach ($this->clientEvents as $event => $handler) {
                $js[] = "jQuery('#$id').on('$event', $handler);";
            }
            $this->getView()->registerJs(implode("\n", $js));
        }
    }
	
	/**
	 * Initializes the placeholder for Select2
	 */
	protected function initPlaceholder() {
		$isMultiple = ArrayHelper::getValue($this->options, 'multiple', false);
		if (isset($this->options['prompt']) && !isset($this->clientOptions['placeholder'])) {
			$this->clientOptions['placeholder'] = $this->options['prompt'];
			if ($isMultiple) {
				unset($this->options['prompt']);
			}
			return;
		}
		if (isset($this->options['placeholder'])) {
			$this->clientOptions['placeholder'] = $this->options['placeholder'];
			unset($this->options['placeholder']);
		}
		if (isset($this->clientOptions['placeholder']) && is_string($this->clientOptions['placeholder']) && !$isMultiple) {
			$this->options['prompt'] = $this->clientOptions['placeholder'];
		}
	}
}
