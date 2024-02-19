initEditableButton = function(id, options) {
	var button = $(id + '-button');
	var $id = $(id);
	var $options = options;
	button.unbind('click').bind('click', function(e) {
		e.stopPropagation();
		if (button.is(':disabled'))
			return;
		$id.editable('setValue', null);
		$id.editable('toggle');
	});
	
	$id.on('save', function(e, params) {
		
		// Disable button
		$('.editable-button-toggle').attr('disabled', true);
		
		// Reload ajax
		$.pjax.reload({
			container: '#' + $options.pjaxId,
		});
	});
	
	$(document).on('pjax:complete', function() {
		initEditableButtonEvents(id);
	});
	initEditableButtonEvents(id);
};

initEditableButtonEvents = function(id) {
	var $id = $(id);
	$('input[name="selection_all"], input[name="selection[]"]').change(function () {
		var values = [];
		$.each($('input[name="selection[]"]:checked'), function() {
			values.push($(this).val());
		});
		
		$.each($('.editable-button-toggle'), function() {
			if (values.length)
				$(this).removeAttr('disabled');
			else
				$(this).attr('disabled', true);
		});
		$id.editable('option', 'pk', values);
	});
}