<?php
namespace common\modules\eav\widgets;

/**
 * Class TextInput
 * @package common\modules\eav\widgets
 */
class TextInput extends AttributeHandler
{
	/**
	 * @var int
	 */
	static $order = 0;
	
	/**
	 * @var string
	 */
	static $fieldView = <<<TEMPLATE
		<input type='text'
			class='form-control input-sm rf-size-<%= rf.get(Formbuilder.names.SIZE) %>'
			<% if ( rf.get(Formbuilder.names.LOCKED) ) { %>disabled readonly<% } %>
		/>
TEMPLATE;
	
	/**
	 * @var string
	 */
	static $fieldSettings = <<<TEMPLATE
		<%= Formbuilder.templates['edit/field_options']() %>
		<%= Formbuilder.templates['edit/size']() %>
		<%= Formbuilder.templates['edit/min_max_length']() %>
TEMPLATE;
	
	/**
	 * @var string
	 */
	static $fieldButton = <<<TEMPLATE
		<span class='symbol'><span class='fa fa-font'></span></span> <%= Formbuilder.lang('Input textfield') %>
TEMPLATE;
	
	/**
	 * @var string
	 */
	static $defaultAttributes = <<<TEMPLATE
		function (attrs) {
			attrs.field_options.size = 'small';
			return attrs;
		}
TEMPLATE;
	
	/**
	 * @return string|\yii\widgets\ActiveField
	 */
	public function run() {
		return $this->owner->activeForm->field($this->owner, $this->getAttributeName(), ['template' => "{input}\n{hint}\n{error}"])->textInput($this->options);
	}
}