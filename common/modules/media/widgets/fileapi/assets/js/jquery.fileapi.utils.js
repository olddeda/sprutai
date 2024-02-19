

function fileapiCrop(selector) {
	jQuery(document).on('click', '#modal-crop .crop', function() {
		var el = jQuery('#' + selector);
		var modal = el.parent().find('.modal');

		// Crop
		var img = modal.find('canvas.cropper-hidden');
		var data = img.data('data');
		var file = img.data('file');

		el.fileapi('crop', file, data);

		el.fileapi('data', {
			'uid': FileAPI.uid(file),
		});

		// Remove image
		el.find('.uploader-preview-placeholder img').remove();

		// Start upload
		el.fileapi('upload');

		// Hide modal
		modal.modal('hide');
	});
}

function fileapiClose(selector) {
	jQuery(document).on('click', '#modal-crop .cancel, #modal-crop .close', function() {
		var el = jQuery('#' + selector);
		var modal = el.parent().find('.modal');

		// Clear instances
		fileapiClear();

		// Clear api
		var uploader = jQuery(this).closest('#' + selector);
		uploader.fileapi('clear');

		// Hide modal
		modal.modal("hide");
	});
}

function fileapiSelect(settings, ufile) {
	if (ufile) {
		settings.file = ufile;

		jQuery('#modal-crop').modal('show');
		jQuery('#modal-crop').off('shown.bs.modal').on('shown.bs.modal', function() {
			$('#modal-preview').mCropper(settings);
		});
	}
}

function fileapiComplete(obj, event, uiEvent) {

	// Clear instances
	fileapiClear();

	// Clear progress
	jQuery(obj).find('[data-fileapi="progress"]').css('width', '0%');

	if (uiEvent.result.error) {

		// Show error message
		notifyError(uiEvent.result.error);
	}
	else {
		
		// Show success message
		notifySuccess(uiEvent.result.success);

		jQuery(obj).find('input[name="uid"]').val(FileAPI.uid(uiEvent.file));
		jQuery(obj).find('[data-fileapi="delete"]').attr('data-fileapi-uid', FileAPI.uid(uiEvent.file)).removeClass('hidden');
		
		// Remove old image
		jQuery(obj).find('.uploader-preview-placeholder img').remove();
		
		// Create new image
		var format = $(obj).data('format');
		var src = uiEvent.result.url + format + '_' + uiEvent.result.file;
		var img = $('<img>').attr('src', src);
		jQuery(obj).find('.uploader-preview-placeholder.thumbnail').append(img);
		jQuery(obj).find('.uploader-preview-placeholder-wrapper').addClass('hidden');
	}
}

function fileapiDelete(selector) {
	jQuery(document).on('click', '#' + selector + ' [data-fileapi="delete"]', function(event) {
		event.preventDefault();

		// Clear uploader
		var uploader = jQuery(this).closest('#' + selector);

		// Hide delete button
		uploader.find('[data-fileapi="delete"]').addClass('hidden');

		// Show loading
		uploader.find('[data-fileapi="delete-loading"]').removeClass('hidden');

		// Collect data
		var data = {};
		data.media_hash = uploader.find('input[name="media_hash"]').val();

		$.ajax({
			url: uploader.find('input[name="delete_url"]').val(),
			type: 'POST',
			dataType: 'json',
			data: data,
			success: function(data) {

				// Hide loading
				uploader.find('[data-fileapi="delete-loading"]').addClass('hidden');

				if (data.error) {

					// Show error message
					notifyError(data.error);

					// Show delete button
					uploader.find('[data-fileapi="delete"]').removeClass('hidden');
				}
				else {

					// Show success message
					notifySuccess(data.success);

					// Clear uploader
					uploader.fileapi('clear');

					// Remove image
					uploader.find('.uploader-preview-placeholder img').remove();

					// Show placeholder
					uploader.find('.uploader-preview-placeholder-wrapper').removeClass('hidden');

					// Hide preview image
					uploader.find('[data-fileapi="preview"]').addClass('hidden');

					// Clear input
					uploader.find('input[type="hidden"]').val('');
				}
			}
		});
	});
}

function fileapiClear() {
	var modal = jQuery('#modal-crop');
	if (modal.length) {
		
		// Remove cropper instance
		var img = modal.find('.cropper-hidden');
		img.cropper('destroy');
		img.remove();
	}
}
