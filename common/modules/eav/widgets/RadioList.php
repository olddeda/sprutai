<?php

namespace common\modules\eav\widgets;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * Class RadioList
 * @package common\modules\eav\widgets
 */
class RadioList extends AttributeHandler
{
	/**
	 * Value class
	 */
	const VALUE_HANDLER_CLASS = '\common\modules\eav\handlers\OptionValueHandler';
	
	/**
	 * @var int
	 */
	static $order = 15;
	
	/**
	 * @var string
	 */
	static $fieldView = <<<TEMPLATE
		<% for (i in (rf.get(Formbuilder.names.OPTIONS) || [])) { %>
		<div>
		<label class='fb-option'>
		<input type='radio'
		 <%= rf.get(Formbuilder.names.OPTIONS)[i].checked && 'checked' %>
		 <% if ( rf.get(Formbuilder.names.LOCKED) ) { %>disabled readonly<% } %>
			onclick="javascript: return false;"
		/>
		<%= rf.get(Formbuilder.names.OPTIONS)[i].label %>
		</label>
		</div>
		<% } %>
TEMPLATE;
	
	/**
	 * @var string
	 */
	static $fieldSettings = <<<TEMPLATE
		<%= Formbuilder.templates['edit/field_options']() %>
		<%= Formbuilder.templates['edit/options']({
			rf: rf
		}) %>
TEMPLATE;
	
	/**
	 * @var string
	 */
	static $fieldButton = <<<TEMPLATE
		<span class="symbol"><span class="fa fa-circle-o"></span></span> <%= Formbuilder.lang('Radio') %>
TEMPLATE;
	
	/**
	 * @var string
	 */
	static $defaultAttributes = <<<TEMPLATE
		function (attrs) {
			attrs.field_options.options = [
				{
						label: "",
						checked: false
				}
			];
			return attrs;
		}
TEMPLATE;
	
	/**
	 * @return string|\yii\widgets\ActiveField
	 */
	public function run() {
		$options = $this->attributeModel->getEavOptions()->asArray()->all();
		
		return $this->owner->activeForm->field($this->owner, $this->getAttributeName(), ['template' => "{input}\n{hint}\n{error}"])->radioList(ArrayHelper::map($options, 'id', 'value'));
	}
}