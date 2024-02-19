<?php
namespace common\modules\eav\widgets;

use yii\helpers\ArrayHelper;

/**
 * Class DropDownList
 * @package common\modules\eav\widgets
 */
class DropDownList extends AttributeHandler
{
	/**
	 * Value class
	 */
	const VALUE_HANDLER_CLASS = '\common\modules\eav\handlers\OptionValueHandler';
	
	/**
	 * @var int
	 */
	static $order = 24;
	
	/**
	 * @var string
	 */
	static $fieldView = <<<TEMPLATE
		<select>

			<% if (rf.get(Formbuilder.names.INCLUDE_BLANK)) { %>
					<option value=''></option>
			<% } %>

			<% for (i in (rf.get(Formbuilder.names.OPTIONS) || [])) { %>
				<option
					<% if ( rf.get(Formbuilder.names.LOCKED) ) { %><%= Formbuilder.lang('disabled readonly') %><% } %>
					<%= rf.get(Formbuilder.names.OPTIONS)[i].checked && 'selected' %>
				/>
				<%= rf.get(Formbuilder.names.OPTIONS)[i].label %>
				</option>
			<% } %>

		</select>
TEMPLATE;
	
	/**
	 * @var string
	 */
	static $fieldSettings = <<<TEMPLATE
		<%= Formbuilder.templates['edit/field_options']() %>
		<%= Formbuilder.templates['edit/options']({
				includeBlank: true,
				useMultiple: true,
				rf: rf
		}) %>
TEMPLATE;
	
	/**
	 * @var string
	 */
	static $fieldButton = <<<TEMPLATE
		<span class="symbol"><span class="fa fa-caret-down"></span></span> <%= Formbuilder.lang('Dropdown') %>
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
			attrs.field_options.include_blank_option = false;
			return attrs;
		}
TEMPLATE;
	
	/**
	 * @return string|\yii\widgets\ActiveField
	 */
	public function run() {
		$attributeModel = $this->attributeModel;
		$options = $attributeModel->getEavOptions()->asArray()->all();
		
		return $this->owner->activeForm->field($this->owner, $this->getAttributeName(), ['template' => "{input}\n{hint}\n{error}"])->dropDownList(ArrayHelper::map($options, 'id', 'value'));
	}
}