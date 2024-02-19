$(document).ready(function() {
	$('.price-widget-input-request input[type="checkbox"]').change(function() {
		var parent = $(this).closest('.price-widget').find('.price-widget-input');
		if ($(this).prop('checked')) {
			parent.slideUp();
		}
		else
			parent.slideDown();
	});
});