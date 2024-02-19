/**
 Phone editable input.
 Internally value stored as {country: "RU", locality: "Moscow", address: "Isakovskogo str. 4, Moscow, Russia, 123181", lat: 55.70897496906675, lng: 37.668968869369564}
 
 @class selectize
 @extends abstractinput
 @final
 @example
 <a href="#" id="selectize" data-type="selectize" data-pk="1">awesome</a>
 <script>
 $(function(){
    $('#selectize').selectize({
        url: '/post',
        title: 'Phone',
        value: {
        
        }
    });
});
 </script>
 **/
(function ($) {
	"use strict";
	
	var Constructor = function (options) {
		this.init('selectize', options, Constructor.defaults);
		
		options.selectize = options.selectize || {};
		
		// overriding objects in config (as by default jQuery extend() is not recursive)
		this.options.selectize = $.extend({}, Constructor.defaults.selectize, options.selectize);
		
		// detect whether it is multi-valued
		this.isMultiple = this.options.selectize.multiple;
		this.isRemote = ('ajax' in this.options.selectize);
	};
	
	//inherit from Abstract input
	$.fn.editableutils.inherit(Constructor, $.fn.editabletypes.abstractinput);
	
	$.extend(Constructor.prototype, {
		
		/**
		 Renders input from tpl
		 
		 @method render()
		 **/
		render: function () {
			this.$input = this.$tpl.find('select');
			
			if (!this.$input.data('selectize')) {
				this.$input.selectize(this.options.selectize);
				this.$selectize = this.$input[0].selectize;
			}
		},
		
		/**
		 Converts string to value. Used for reading value from 'data-value' attribute.
		 
		 @method str2value(str)
		 */
		str2value: function (str) {
			return str;
		},
		
		/**
		 Gets value from element's html
		 
		 @method html2value(html)
		 **/
		html2value: function (html) {
			return html;
		},
		
		/**
		 * Used to update the text in the link based on the selected value
		 */
		value2html: function (value, element) {
			if (!this.$input)
				return;
			var values = this.$selectize.getValue();
			var tmp = [];
			for (var v = 0; v < values.length; v++) {
				var obj = this.$selectize.options[values[v]];
				tmp.push(obj.title);
			}
			value = tmp.join(this.options.selectize.delimiter);
			$(element)[this.options.escape ? 'text' : 'html']($.trim(value));
		},
		
		/**
		 Sets value of input.
		 
		 @method value2input(value)
		 @param {mixed} value
		 **/
		value2input: function (value) {
			
			// The value for a multiple select can be passed in as a single string
			// This will convert it from a string to an array of data values
			if (value && !$.isArray(value) && this.isMultiple) {
				value = this.str2value(value);
			}
			
			if (!value) {
				return;
			}
			
			// Branch off based on whether or not it's a multiple select
			// Either way, we are adding `<option>` tags for selected values that
			// don't already exist, so they can be selected correctly.
			if ($.isArray(value)) {
				for (var v = 0; v < value.length; v++) {
					var obj = this.$selectize.options[value[v]];
					if (typeof obj === 'object') {
						this.$selectize.addOption({
							value: obj.id,
							text: obj.title,
						});
						this.$selectize.addItem(obj.id);
					}
				}
				this.$selectize.refreshItems();
			} else {
				var $filtered = this.$input.find('option').filter(function (i, elem) {
					return elem.value == value.toString()
				});
				
				if ($filtered.length === 0) {
					var $el = $(this.options.scope);
					var text;
					if (!$el.data('editable').isEmpty) {
						text = $el.text();
					} else {
						text = value;
					}
					this.$selectize.addOption({
						value: value,
						text: text,
					});
					this.$selectize.addItem(value);
				}
			}
			
			// After setting the value we must trigger the change event for Select2
			this.$input.val(value).trigger('change');
		},
		
		/**
		 Returns value of input.
		 
		 @method input2value()
		 **/
		input2value: function () {
			var values = this.$selectize.getValue();
			var result = [];
			for (var v = 0; v < values.length; v++) {
				result.push(values[v]);
			}
			return (result.length) ? result : '';
		},
		
		/**
		 Activates input: sets focus on the first field.
		 
		 @method activate()
		 **/
		activate: function () {
			this.$input.focus();
			var val = this.$input.val();
			this.$input.val('').val(val);
		},
		
		/**
		 Attaches handler to submit form in case of 'showbuttons=false' mode
		 
		 @method autosubmit()
		 **/
		autosubmit: function () {
			this.$input.keydown(function (e) {
				if (e.which === 13) {
					$(this).closest('form').submit();
				}
			});
		},
		
		destroy: function() {
			if(this.$input.data('selectize')) {
				this.$selectize.destroy();
			}
		}
	});
	
	Constructor.defaults = $.extend({}, $.fn.editabletypes.abstractinput.defaults, {
		tpl: '' +
		'<div class="editable-selectize">' +
		'<select type="text" name="selectize" class="form-control input-sm" multiple="multiple"></select>' +
		'</div>',
		inputclass: 'form-control',
		
		/**
		 Configuration of selectizeinput. [Full list of options](https://github.com/jackocnr/intl-tel-input).
		 
		 @property selectize
		 @type object
		 **/
		selectize: {
			valueField: 'id',
			labelField: 'title',
			searchField: 'title',
			delimiter: ', ',
			persist: false,
			multiple: true,
			plugins: ['remove_button'],
		}
	});
	
	$.fn.editabletypes.selectize = Constructor;
	
}(window.jQuery));