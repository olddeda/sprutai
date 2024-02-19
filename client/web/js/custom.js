(function($) {
    $.fn.attrBegins = function(s) {
        var matched = [];
        this.each(function(index) {
            var elem = this;
            $.each(this.attributes, function( index, attr ) {
                if(attr.name.indexOf(s)===0){
                    matched.push(elem);
                }
            });
        });
        return $( matched );
    };
})(jQuery);

(function (window, document, $, undefined) {
	
	if (typeof $ === 'undefined') {
        throw new Error('This application\'s JavaScript requires jQuery');
    }
	
	$(document).ready(function() {
		$('body').removeClass('preload');
		
		// Enable tooltip
		$("[data-toggle='tooltip']").tooltip();
		
		$('textarea[data-grow]').each(function () {
			this.addEventListener('keydown', function() {
				var el = this;
				setTimeout(function(){
					el.style.cssText = 'height:auto;padding:0';
					// for box-sizing other than "content-box" use:
					//el.style.cssText = '-moz-box-sizing:content-box';
					el.style.cssText = 'height:' + el.scrollHeight + 'px';
				},0);
			});
		});

       	$('.content-editable').on( 'keyup', 'textarea', function (){
        	$(this).height( 0 );
            $(this).height( this.scrollHeight );
       	});
        $('.content-editable').find('textarea').keyup();

        $('.parent-focused *')
			.focus(function() {
                $('.parent-focused').addClass('focused');
            })
            .blur(function() {
                $('.parent-focused').removeClass('focused');
            });
		
		$(window).scroll(function() {
			$('video').each(function(){
				if ($(this).is(":in-viewport")) {
					$(this)[0].play();
				} else {
					$(this)[0].pause();
				}
			})
		});
	});
	
})(window, document, window.jQuery);

function notifySuccess($message) {
	new PNotify({
		'text': $message,
		'type': 'success',
		'styling': 'bootstrap3',
		'icon': '',
		'opacity': 0.95,
		'shadow': false,
	});
}

function notifyError($message) {
	new PNotify({
		'text': $message,
		'type': 'error',
		'styling': 'bootstrap3',
		'icon': '',
		'opacity': 0.95,
		'shadow': false,
	});
}

function flotDateFormat(val, format, locale) {
	if (locale)
		moment.locale(locale);
	return moment.unix(val).format(format);
}