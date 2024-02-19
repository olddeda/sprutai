<?php
namespace common\modules\eav\widgets;

use yii\helpers\ArrayHelper;

/**
 * Class CheckBoxList
 * @package common\modules\eav\widgets
 */
class CheckBoxList extends AttributeHandler
{
	
	/**
	 * Value class
	 */
	const VALUE_HANDLER_CLASS = '\common\modules\eav\handlers\MultipleOptionsValueHandler';
	
	/**
	 * @var int
	 */
	static $order = 10;
	
	/**
	 * @var string
	 */
	static $fieldView = <<<TEMPLATE
		<% for (i in (rf.get(Formbuilder.names.OPTIONS) || [])) { %>
		<div>
		<label class='fb-option'>
		<input type='checkbox'
			<%= rf.get(Formbuilder.names.OPTIONS)[i].checked && 'checked' %>
			<% if ( rf.get(Formbuilder.names.LOCKED) ) { %><%= Formbuilder.lang('disabled readonly') %><% } %>
				onclick="javascript: return false;"
		/>
		<%= rf.get(Formbuilder.names.OPTIONS)[i].label %>
		</label>
		</div>
		<% } %>
		<% if (rf.get(Formbuilder.names.INCLUDE_OTHER)) { %>
		<div class='other-option'>
		<label class='fb-option'><input type='checkbox' /> <%= Formbuilder.lang('Other') %></label>
		<input type='text' />
		</div>
		<% } %>
TEMPLATE;
	
	/**
	 * @var string
	 */
	static $fieldSettings = <<<TEMPLATE
		<%= Formbuilder.templates['edit/field_options']() %>
		<%= Formbuilder.templates['edit/options']({
			showCheckBox: true,
			rf: rf
		}) %>
TEMPLATE;
	
	/**
	 * @var string
	 */
	static $fieldButton = <<<TEMPLATE
		<span class="symbol"><span class="fa fa-square-o"></span></span> <%= Formbuilder.lang('Checkboxes') %>
TEMPLATE;
	
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
		return $this->owner->activeForm->field($this->owner, $this->getAttributeName(), ['template' => "{input}\n{hint}\n{error}"])->checkboxList(ArrayHelper::map($options, 'id', 'value'));
	}
}