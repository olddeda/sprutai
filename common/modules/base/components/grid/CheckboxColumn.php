<?php
namespace common\modules\base\components\grid;

use yii\grid\CheckboxColumn as BaseCheckboxColumn;

use yii\helpers\Html;
use yii\helpers\Json;

class CheckboxColumn extends BaseCheckboxColumn
{
	
	/**
	 * Renders the header cell content.
	 * The default implementation simply renders [[header]].
	 * This method may be overridden to customize the rendering of the header cell.
	 * @return string the rendering result
	 */
	protected function renderHeaderCellContent() {
		if ($this->header !== null || !$this->multiple) {
			return parent::renderHeaderCellContent();
		}
		else {
			$html = Html::beginTag('div', ['class' => 'checkbox-default checkbox-primary']);
			$html.= Html::checkbox($this->getHeaderCheckBoxName(), false, ['class' => 'select-on-check-all', 'id' => 'select-on-check-all']);
			$html.= Html::tag('label', '', ['for' => 'select-on-check-all']);
			$html.= Html::endTag('div');
			return $html;
		}
	}
	
	/**
	 * @inheritdoc
	 */
	protected function renderDataCellContent($model, $key, $index) {
		if ($this->checkboxOptions instanceof Closure) {
			$options = call_user_func($this->checkboxOptions, $model, $key, $index, $this);
		}
		else {
			$options = $this->checkboxOptions;
		}
		
		if (!isset($options['value'])) {
			$options['value'] = is_array($key) ? Json::encode($key) : $key;
		}
		
		if ($this->cssClass !== null) {
			Html::addCssClass($options, $this->cssClass);
		}
		
		$options['id'] = str_replace('[]', '', $this->name).'_'.$model->id;
		
		$html = Html::beginTag('div', ['class' => 'checkbox-default checkbox-primary']);
		$html.= Html::checkbox($this->name, !empty($options['checked']), $options);
		$html.= Html::tag('label', '', ['for' => $options['id']]);
		$html.= Html::endTag('div');
		
		return $html;
	}
}