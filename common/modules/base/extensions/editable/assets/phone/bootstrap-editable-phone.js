/**
 Phone editable input.
 Internally value stored as {country: "RU", locality: "Moscow", address: "Isakovskogo str. 4, Moscow, Russia, 123181", lat: 55.70897496906675, lng: 37.668968869369564}
 
 @class phone
 @extends abstractinput
 @final
 @example
 <a href="#" id="phone" data-type="phone" data-pk="1">awesome</a>
 <script>
 $(function(){
    $('#phone').editable({
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
		this.init('phone', options, Constructor.defaults);
		
		options.phone = options.phone || {};
	};
	
	//inherit from Abstract input
	$.fn.editableutils.inherit(Constructor, $.fn.editabletypes.abstractinput);
	
	$.extend(Constructor.prototype, {
		
		/**
		 Renders input from tpl
		 
		 @method render()
		 **/
		render: function () {
			this.$input = this.$tpl.find('input');
			
			if (!this.$input.data('phone')) {
				var input = this.$input;
				this.$input.intlTelInput(this.options.phone);
				if (this.options.phone.autoFormat) {
					this.$input.bind('propertychange change click keyup input paste', function(event) {
						$(this).val(intlTelInputUtils.formatNumber($(this).intlTelInput("getNumber"), null, intlTelInputUtils.numberFormat.INTERNATIONAL));
					});
				}
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
			Constructor.superclass.value2html.apply(this, arguments);
		},
		
		/**
		 Converts value to string.
		 It is used in internal comparing (not for sending to server).
		 
		 @method value2str(value)
		 **/
		value2str: function (value) {
			var str = '';
			if (value) {
				for (var k in value) {
					str = str + k + ':' + value[k] + ';';
				}
			}
			return str;
		},
		
		/**
		 Sets value of input.
		 
		 @method value2input(value)
		 @param {mixed} value
		 **/
		value2input: function (value) {
			if (!value) {
				return;
			}
			
			// After setting the value we must trigger the change event for Select2
			this.$input.val(value).trigger('change');
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
			if(this.$input.data('phone')) {
			}
		}
	});
	
	Constructor.defaults = $.extend({}, $.fn.editabletypes.abstractinput.defaults, {
		tpl: '' +
			'<div class="editable-phone">' +
			'<input type="text" name="phone" class="form-control input-sm">' +
			'</div>',
		inputclass: 'form-control',
		
		/**
		 Configuration of phoneinput. [Full list of options](https://github.com/jackocnr/intl-tel-input).
		 
		 @property select2
		 @type object
		 @default null
		 **/
		phone: {
			autoFormat: true,
			nationalMode: false,
			autoHideDialCode: false,
			preferredCountries: ['ru'],
			defaultCountry: 'auto',
			geoIpLookup: function(callback) {
				$.get("http://ipinfo.io", function() {}, "jsonp").always(function(resp) {
					var countryCode = (resp && resp.country) ? resp.country : "";
					callback(countryCode);
				});
			}
		}
	});
	
	$.fn.editabletypes.phone = Constructor;
	
}(window.jQuery));