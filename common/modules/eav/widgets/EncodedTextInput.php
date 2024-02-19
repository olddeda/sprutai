<?php

namespace common\modules\eav\widgets;

/**
 * Class EncodedTextInput
 * @package common\modules\eav\widgets
 */
class EncodedTextInput extends TextInput
{
	/**
	 * Value class
	 */
	const VALUE_HANDLER_CLASS = '\common\modules\eav\handlers\ArrayValueHandler';
	
	/**
	 * @var int
	 */
	static $order = 3;
	
	/**
	 * @var string
	 */
	static $fieldView = <<<TEMPLATE
		<textarea
			class='form-control input-sm' type='text'
			rows=<%= rf.get(Formbuilder.names.AREA_ROWS) %>
			cols=<%= rf.get(Formbuilder.names.AREA_COLS) %>
			<% if ( rf.get(Formbuilder.names.LOCKED) ) { %><%= Formbuilder.lang('disabled readonly') %><% } %>
		/>
		</textarea>
TEMPLATE;
	
	/**
	 * @var string
	 */
	static $fieldSettings = <<<TEMPLATE
		<%= Formbuilder.templates['edit/field_options']() %>
		<%= Formbuilder.templates['edit/text_area']({ hideSizeOptions: true }) %>
TEMPLATE;
	
	/**
	 * @var string
	 */
	static $fieldButton = <<<TEMPLATE
		<span class='symbol'><span class='fa fa-paragraph'></span></span> <%= Formbuilder.lang('Json textarea') %>
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

}