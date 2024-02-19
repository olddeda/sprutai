/**
 Constructor editable input.
 Internally value stored as {price: "100", currency: "Lenina", request: false}
 
 @class address
 @extends abstractinput
 @final
 @example
 <a href="#" id="price" data-type="price" data-pk="1">awesome</a>
 <script>
 $(function(){
 $('#price').editable({
 url: '/post',
 title: 'Enter price',
 value: {
 price: "100",
 currency: "usd",
 request: false,
 }
 });
 });
 </script>
 **/
(function($) {
	"use strict";
	
	var Constructor = function(options) {
		this.init('price', options, Constructor.defaults);
		
		// overriding objects in config (as by default jQuery extend() is not recursive)
		this.options.price = $.extend({}, Constructor.defaults.price, options.price);
	};
	
	//inherit from Abstract input
	$.fn.editableutils.inherit(Constructor, $.fn.editabletypes.abstractinput);
	
	$.extend(Constructor.prototype, {
		
		/**
		 Renders input from tpl
		 @method render()
		 **/
		render: function() {
			this.$input = this.$tpl.find('input');
			this.$select = this.$tpl.find('select');
			
			// Translate
			$('.editable-price-price input', this.$tpl).attr('name', this.options.price.fields.price);
			$('.editable-price-currency select', this.$tpl).attr('name', this.options.price.fields.currency);
			$('.editable-price-request input', this.$tpl).attr('name', this.options.price.fields.request);
			$('label', this.$tpl).html($('label', this.$tpl).html().replace('{label-request}', this.options.price.labelRequest));
			
			// Fill currencies
			var el = this.$select.filter('[name="' +  this.options.price.fields.currency + '"]');
			$.each(this.options.price.currencies, function(idx, obj) {
				el.append($("<option />").val(obj.value).text(obj.text));
			});
			
			if(!this.$select.data('price')) {
				this.$select.filter('[name="' +  this.options.price.fields.currency + '"]').select2();
			}
			
			// Add event
			this.$input.filter('[name="' + this.options.price.fields.request + '"]').change(function() {
				if ($(this).prop('checked'))
					$('.editable-price', this.$tpl).slideUp();
				else
					$('.editable-price', this.$tpl).slideDown();
			});
			
			this.$input.closest('.popover').addClass('editable-container-price');
			
			return Constructor.superclass.render.call(this);
		},
		
		/**
		 Default method to show value in element. Can be overwritten by display option.
		 @method value2html(value, element)
		 **/
		value2html: function(value, element) {
			if (!value) {
				$(element).empty();
				return;
			}
			
			var currencyName = '';
			$.each(this.options.price.currencies, function(idx, obj) {
				if (obj.value == value.currency) {
					currencyName = obj.text;
				}
			});
			
			var html = (value.request) ? this.options.price.labelRequest : (value.price).toLocaleString("ru-RU", {currency: "RUR", minimumFractionDigits: 0}) + ' ' + currencyName;
			$(element).html(html);
		},
		
		/**
		 Gets value from element's html
		 @method html2value(html)
		 **/
		html2value: function(html) {
			return null;
		},
		
		/**
		 Converts value to string.
		 It is used in internal comparing (not for sending to server).
		 @method value2str(value)
		 **/
		value2str: function(value) {
			var str = '';
			if (value) {
				for (var k in value) {
					str = str + k + ':' + value[k] + ';';
				}
			}
			return str;
		},
		
		/**
		 Converts string to value. Used for reading value from 'data-value' attribute.
		 @method str2value(str)
		 */
		str2value: function(str) {
			return str;
		},
		
		/**
		 Sets value of input.
		 @method value2input(value)
		 @param {mixed} value
		 **/
		value2input: function(value) {
			if (!value) {
				return;
			}
			this.$input.filter('[name="' +  this.options.price.fields.price + '"]').val(value.price);
			this.$select.filter('[name="' +  this.options.price.fields.currency + '"]').val(value.currency).trigger('change.select2');
			this.$input.filter('[name="' +  this.options.price.fields.request + '"]').prop('checked', parseInt(value.request));
			
			if (parseInt(value.request))
				$('.editable-price', this.$tpl).hide();
			else
				$('.editable-price', this.$tpl).show();
		},
		
		/**
		 Returns value of input.
		 @method input2value()
		 **/
		input2value: function() {
			var price = parseInt(this.$input.filter('[name="' +  this.options.price.fields.price + '"]').val());
			var currency = parseInt(this.$input.filter('[name="' +  this.options.price.fields.currency + '"]').val());
			var request = (this.$input.filter('[name="' +  this.options.price.fields.request + '"]').prop('checked') ? 1 : 0);
			return {
				price: (isNaN(price) ? 0 : price),
				currency: (isNaN(currency) ? 0 : currency),
				request: request,
			};
		},
		
		/**
		 Activates input: sets focus on the first field.
		 @method activate()
		 **/
		activate: function() {
			this.$input.filter('[name="' +  this.options.price.fields.price + '"]').focus();
			var val = this.$input.filter('[name="' +  this.options.price.fields.price + '"]').val();
			this.$input.filter('[name="' +  this.options.price.fields.price + '"]').val('').val(val);
		},
		
		/**
		 Attaches handler to submit form in case of 'showbuttons=false' mode
		 @method autosubmit()
		 **/
		autosubmit: function() {
			this.$input.keydown(function(e) {
				if (e.which === 13) {
					$(this).closest('form').submit();
				}
			});
		},
		
		validate: function(value) {
			return 'This field is required';
		},
		
		destroy: function() {
			if(this.$select.data('price')) {
				this.$select.select2('destroy');
			}
		}
	});
	
	Constructor.defaults = $.extend({}, $.fn.editabletypes.abstractinput.defaults, {
		tpl: '<div class="editable-price-container">' +
		'<div class="editable-price">' +
		'<div class="editable-price-price"><input type="text" name="price" class="form-control input-sm"></div>' +
		'<div class="editable-price-currency"><select name="currency" class="form-control input-sm"></select></div>' +
		'</div>' +
		'<div class="editable-price-request checkbox-default checkbox-primary"><input id="checkbox" type="checkbox" name="request" class="form-control input-mini"><label for="checkbox">{label-request}</label></div>'+
		'</div>',
		
		inputclass: '',
		
		price: {
			labelRequest: 'On request',
			currencies: {},
			fields: {
				price: 'price',
				currency: 'currency',
				request: 'request',
			}
		}
	});
	
	$.fn.editabletypes.price = Constructor;
	
}(window.jQuery));