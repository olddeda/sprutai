<?php

namespace common\modules\eav\widgets;

/**
 * Class Textarea
 * @package common\modules\eav\widgets
 */
class Textarea extends AttributeHandler
{
	/**
	 * @var int
	 */
	static $order = 1;
	
	/**
	 * @var string
	 */
	static $fieldView = <<<TEMPLATE
		<textarea
			class='form-control input-sm' type='text'
			rows=<%= rf.get(Formbuilder.names.AREA_ROWS) %>
			cols=<%= rf.get(Formbuilder.names.AREA_COLS) %>
			<% if ( rf.get(Formbuilder.names.LOCKED) ) { %>disabled readonly<% } %>
		/>
		</textarea>
TEMPLATE;
	
	/**
	 * @var string
	 */
	static $fieldSettings = <<<TEMPLATE
		<%= Formbuilder.templates['edit/field_options']() %>
		<%= Formbuilder.templates['edit/text_area']() %>
TEMPLATE;
	
	/**
	 * @var string
	 */
	static $fieldButton = <<<TEMPLATE
		<span class='symbol'><span class='fa fa-font'></span></span> <%= Formbuilder.lang('Input textarea') %>
TEMPLATE;
	
	/**
	 * @var string
	 */
	static $defaultAttributes = <<<TEMPLATE
		function (attrs) {
			attrs.field_options.size = 'large';
			return attrs;
		}
TEMPLATE;
	
	/**
	 * @return string|\yii\widgets\ActiveField
	 */
	public function run() {
		return $this->owner->activeForm->field($this->owner, $this->getAttributeName(), ['template' => "{input}\n{hint}\n{error}"])->textArea($this->options);
	}
}