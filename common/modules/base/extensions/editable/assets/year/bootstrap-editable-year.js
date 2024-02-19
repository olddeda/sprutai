/**
 Constructor editable input.
 Internally value stored as {from: "1900", to: "2017"}

 @class year
 @extends abstractinput
 @final
 @example
 <a href="#" id="year" data-type="year" data-pk="1">awesome</a>
 <script>
 $(function(){
    $('#year').editable({
        url: '/post',
        title: 'Editable year',
        value: {
            from: "1900",
            to: "2017",
            fields: {
            	from: 'year_from',
            	to: 'year_to'
            }
        }
    });
});
 </script>
 **/
(function ($) {
    "use strict";

    var Constructor = function (options) {
        this.init('year', options, Constructor.defaults);
	
		// overriding objects in config (as by default jQuery extend() is not recursive)
		this.options.year = $.extend({}, Constructor.defaults.year, options.year);
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
			
			$('.editable-year-from input', this.$tpl).attr('name', this.options.year.fields.from).attr('placeholder', this.options.year.placeholders.from);
			$('.editable-year-to input', this.$tpl).attr('name', this.options.year.fields.to).attr('placeholder', this.options.year.placeholders.to);
	
			return Constructor.superclass.render.call(this);
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
            var html = '';
            if (value.from && value.to)
            	html = value.from + ' - ' + value.to;
            else if (value.from)
            	html = this.options.year.placeholders.from + ' ' + value.from;
            else if (value.to)
            	html = this.options.year.placeholders.to + ' ' + value.to;
            $(element).html(html);
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
			this.$input.filter('[name="' +  this.options.year.fields.from + '"]').val(value.from);
			this.$input.filter('[name="' +  this.options.year.fields.to + '"]').val(value.to);
        },

        /**
         Returns value of input.

         @method input2value()
         **/
        input2value: function () {
            return {
                from: this.$input.filter('[name="' +  this.options.year.fields.from + '"]').val(),
                to: this.$input.filter('[name="' +  this.options.year.fields.to + '"]').val()
            };
        },

        /**
         Activates input: sets focus on the first field.

         @method activate()
         **/
        activate: function () {
			this.$input.filter('[name="' +  this.options.year.fields.from + '"]').focus();
			var val = this.$input.filter('[name="' +  this.options.year.fields.from + '"]').val();
			this.$input.filter('[name="' +  this.options.year.fields.from + '"]').val('').val(val);
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

    Constructor.defaults = $.extend({}, $.fn.editabletypes.abstractinput.defaults, {
        tpl: '<div class="editable-year row">' +
            '<div class="editable-year-from col-sm-6"><label><input type="text" name="year_from" placeholder="From" class="form-control input-small" maxlength="4"></label></div>' +
			'<div class="editable-year-to col-sm-6"><label><input type="text" name="year_to" placeholder="To" class="form-control input-small" maxlength="4"></label></div>' +
            '</div>',

        inputclass: '',
	
		year: {
        	placeholders: {
        		from: 'From',
				to: 'To'
			},
			fields: {
				from: 'year_from',
				to: 'year_to',
			}
		}
    });

    $.fn.editabletypes.year = Constructor;

}(window.jQuery));