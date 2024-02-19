initS2Custom = function(id, options) {
	id.on('select2:unselecting', function() {
		$(this).data('unselecting', true);
	}).on('select2:opening', function(e) {
		if ($(this).data('unselecting')) {
			$(this).removeData('unselecting');
			e.preventDefault();
		}
	});
	if (options.allowClear) {
		id.addClass('select2-allow-clear');
	}
	
	$(".select2-selection__rendered").on('keydown', function (e) {
		if ($(".select2-search__field valid").prevObject.context.activeElement.value == "" && e.which === 8) {
			event.preventDefault();
		}
	});
	
	$.fn.select2.defaults.set('escapeMarkup', function (text) { return text; });
	
	$.fn.select2.defaults.set('templateSelection', function (data, container) {
		return '<span>' + data.text + '</span>';
	});
}