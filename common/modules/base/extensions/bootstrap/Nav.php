<?php

namespace common\modules\base\extensions\bootstrap;

use common\modules\base\components\Debug;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * An extended nav menu for Bootstrap 3 - that offers
 * submenu drilldown
 *
 * @author Sergey Safronov <safronov.ser@icloud.com>
 * @since 1.0
 */
class Nav extends \yii\bootstrap\Nav
{
	public $dropdownClass = '\common\modules\base\extensions\bootstrap\Dropdown';
	
	/**
     * @var array the dropdown widget options
     */
    public $dropdownOptions = [];
	
	/**
	 * Label tag
	 * @var string|null
	 */
	public $labelTag = null;
	
	/**
	 * Icon tag
	 * @var string
	 */
	public $iconTag = 'span';
    
    /**
     * @var string the caret indicator to display for dropdowns
     */
    public $dropdownIndicator = ' <span class="caret"></span>';
	
	/**
	 * @var string
	 */
    public $classNormal;
	
	/**
	 * @var string
	 */
    public $classActive;
	
	/**
	 * @var bool
	 */
    public $debug = false;

	/**
	 * Renders a widget's item.
	 * @param string|array $item the item to render.
	 * @return string the rendering result.
	 * @throws InvalidConfigException
	 */
	public function renderItem($item) {
		if (is_string($item)) {
			return $item;
		}

		if (!isset($item['label'])) {
			throw new InvalidConfigException("The 'label' option is required.");
		}

		$encodeLabel = isset($item['encode']) ? $item['encode'] : $this->encodeLabels;
		$label = $encodeLabel ? Html::encode($item['label']) : $item['label'];
		$options = ArrayHelper::getValue($item, 'options', []);
		$items = ArrayHelper::getValue($item, 'items');
		$url = ArrayHelper::getValue($item, 'url', '#');
		$linkOptions = ArrayHelper::getValue($item, 'linkOptions', []);
		
		if ($this->labelTag)
			$label = Html::tag($this->labelTag, $label);

		if (isset($item['icon'])) {
			$label = Html::tag($this->iconTag, '', ['class' => $item['icon']]).$label;
		}

		if (isset($item['active'])) {
			$active = ArrayHelper::remove($item, 'active', false);
		}
		else {
			$active = $this->isItemActive($item);
		}

		if ($items !== null) {
			$linkOptions['data-toggle'] = 'dropdown';
			Html::addCssClass($options, 'dropdown');
			Html::addCssClass($linkOptions, 'dropdown-toggle');
			$label .= $this->dropdownIndicator;
			if (is_array($items)) {
				if ($this->activateItems) {
					$items = $this->isChildActive($items, $active);
				}
				$dropdown = $this->dropdownClass;
				$dropdownOptions = ArrayHelper::merge($this->dropdownOptions, [
					'items' => $items,
					'encodeLabels' => $this->encodeLabels,
					'clientOptions' => false,
					'view' => $this->getView(),
				]);
				$items = $dropdown::widget($dropdownOptions);
			}
		}
		if ($this->activateItems && $active) {
			Html::addCssClass($options, 'active');
		}
		
		if ($this->debug && $active) {
			//Debug::dump($item);
			//die;
		}
		
		if ($this->classNormal && $this->classActive) {
			$classItem = ($active) ? $this->classActive : $this->classNormal;
			Html::addCssClass($linkOptions, $classItem);
		}

		return Html::tag('li', Html::a($label, $url, $linkOptions).$items, $options);
	}

	/**
	 * @inheritdoc
	 */
	protected function isChildActive($items, &$active) {
		foreach ($items as $i => $child) {
			if (ArrayHelper::remove($items[$i], 'active', false) || $this->isItemActive($child)) {
				Html::addCssClass($items[$i]['options'], 'active');
				if ($this->activateParents) {
					$active = true;
				}
			}
			if (isset($items[$i]['items']) && is_array($items[$i]['items'])) {
				$childActive = false;
				$items[$i]['items'] = $this->isChildActive($items[$i]['items'], $childActive);
				if ($childActive) {
					Html::addCssClass($items[$i]['options'], 'active');
					$active = true;
				}
			}
		}
		return $items;
	}

}
