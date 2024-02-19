/**
 GoogleMap editable input.
 Internally value stored as {country: "RU", locality: "Moscow", address: "Isakovskogo str. 4, Moscow, Russia, 123181", lat: 55.70897496906675, lng: 37.668968869369564}
 
 @class googlemap
 @extends abstractinput
 @final
 @example
 <a href="#" id="googlemap" data-type="googlemap" data-pk="1">awesome</a>
 <script>
 $(function(){
    $('#address').editable({
        url: '/post',
        title: 'Select address on map',
        value: {
        	country: "RU",
        	locality: "Moscow",
            address: "Isakovskogo str. 4, Moscow, Russia, 123181",
            lat: 55.70897496906675,
            lng: 37.668968869369564
        }
    });
});
 </script>
 **/
(function ($) {
	"use strict";
	
	var GoogleMap = function (options) {
		this.init('googlemap', options, GoogleMap.defaults);
	};
	
	//inherit from Abstract input
	$.fn.editableutils.inherit(GoogleMap, $.fn.editabletypes.abstractinput);
	
	$.extend(GoogleMap.prototype, {
		picker: null,
		inputAddress: null,
		inputCountry: null,
		inputLocality: null,
		inputLatitude: null,
		inputLongitude: null,
		value: null,
		
		setupMap: function() {
			if (!this.picker) {
				this.inputAddress = this.$input.filter('[name="address"]');
				this.inputCountry = this.$input.filter('[name="country"]');
				this.inputLocality = this.$input.filter('[name="locality"]');
				this.inputLatitude = this.$input.filter('[name="latitude"]');
				this.inputLongitude = this.$input.filter('[name="longitude"]');
				
				this.picker = $('<div>').addClass('map-container').appendTo($('#googlemap'));
				this.picker.locationpicker({
					location: {
						latitude: this.inputLatitude.val(),
						longitude: this.inputLongitude.val()
					},
					zoom: 17,
					radius: 0,
					markerInCenter: true,
					enableAutocomplete: true,
					enableAutocompleteBlur: true,
					addressFormat: 'street_address',
					inputBinding: {
						locationNameInput: this.inputAddress,
						countryInput: this.inputCountry,
						localityInput: this.inputLocality,
						latitudeInput: this.inputLatitude,
						longitudeInput: this.inputLongitude
					}
				});
			}
			else {
				this.picker.appendTo($('#googlemap'));
				this.$input.filter('[name="address"]').replaceWith(this.inputAddress);
				this.$input.filter('[name="country"]').replaceWith(this.inputCountry);
				this.$input.filter('[name="locality"]').replaceWith(this.inputLocality);
				this.$input.filter('[name="latitude"]').replaceWith(this.inputLatitude);
				this.$input.filter('[name="longitude"]').replaceWith(this.inputLongitude);
				
				this.$input = this.$tpl.find('input');
			}
		},
		
		/**
		 Renders input from tpl
		 
		 @method render()
		 **/
		render: function () {
			this.$input = this.$tpl.find('input');
		},
		
		/**
		 Default method to show value in element. Can be overwritten by display option.
		 
		 @method value2html(value, element)
		 **/
		value2html: function (value, element) {
			if (!value) {
				$(element).empty();
				return;
			}
			$(element).html(value.address);
		},
		
		/**
		 Gets value from element's html
		 
		 @method html2value(html)
		 **/
		html2value: function (html) {
			return null;
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
		
		/*
		 Converts string to value. Used for reading value from 'data-value' attribute.
		 
		 @method str2value(str)
		 */
		str2value: function (str) {
			/*
			 this is mainly for parsing value defined in data-value attribute.
			 If you will always set value by javascript, no need to overwrite it
			 */
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
			this.$input.filter('[name="country"]').val(value.country);
			this.$input.filter('[name="locality"]').val(value.locality);
			this.$input.filter('[name="address"]').val(value.address);
			this.$input.filter('[name="latitude"]').val(value.latitude);
			this.$input.filter('[name="longitude"]').val(value.longitude);
		},
		
		/**
		 Returns value of input.
		 
		 @method input2value()
		 **/
		input2value: function () {
			return {
				country: this.$input.filter('[name="country"]').val(),
				locality: this.$input.filter('[name="locality"]').val(),
				address: this.$input.filter('[name="address"]').val(),
				latitude: this.$input.filter('[name="latitude"]').val(),
				longitude: this.$input.filter('[name="longitude"]').val()
			};
		},
		
		/**
		 Activates input: sets focus on the first field.
		 
		 @method activate()
		 **/
		activate: function () {
			this.setupMap();
			
			this.$input.filter('[name="address"]').focus();
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
		}
	});
	
	GoogleMap.defaults = $.extend({}, $.fn.editabletypes.abstractinput.defaults, {
		tpl: '' +
			'<div class="editable-googlemap">' +
			'<input type="text" name="address" class="form-control input-sm">' +
			'<div id="googlemap" class="map"></div>' +
			'<input type="hidden" name="country">' +
			'<input type="hidden" name="locality">' +
			'<input type="hidden" name="latitude">' +
			'<input type="hidden" name="longitude">' +
			'</div>',
		inputclass: ''
	});
	
	$.fn.editabletypes.googlemap = GoogleMap;
	
}(window.jQuery));