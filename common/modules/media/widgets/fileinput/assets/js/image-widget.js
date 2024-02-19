if (typeof appmake == "undefined" || !appmake) {
	var appmake = {};
}

appmake.fileinput_image_widget = {
	init: function (event) {
		$('.fileinput-image-widget-item a.update').off('click').on('click', this.update);
		$('.fileinput-image-widget-item a.delete').off('click').on('click', this.remove);
	},
	
	update: function(event) {
		event.preventDefault();
		
		var modalContainer = $('#fileinput-widget-modal');
		modalContainer.modal({show: true});
		
		// Collect data
		var url = $(this).data('action');
		var data = $(this).parents('.fileinput-image-widget-item').data();
		data[yii.getCsrfParam()] = yii.getCsrfToken();
		
		$.ajax({
			url: $(this).data('action'),
			type: 'POST',
			data: data,
			success: function (data) {
				if (!data.error) {
					$('.fileinput-widget-form').html(data);
					$('#fileinput-widget-form').off('submit').on('submit', appmake.fileinput_image_widget.updateSave);
				}
				else {
					modalContainer.modal('hide');
					notifyError(data.error);
				}
			}
		});
	},
	
	updateSave: function (event) {
		event.preventDefault();
		
		var modalContainer = $('#fileinput-widget-modal');
		var form = modalContainer.find('form');
		
		var data = form.serialize();
		data[yii.getCsrfParam()] = yii.getCsrfToken();
		
		$.ajax({
			url: form.attr('action'),
			type: "POST",
			data: data,
			success: function (data) {
				if (data.success) {
					
					if (data.main) {
						var photoObj = $('[data-media_hash="' + data.main.new.hash + '"');
						
						if (data.main.old.hash) {
							var photoMainObj = $('input[value="' + data.main.old.hash + '"]');
							
							if (photoMainObj) {
								
								// Swap hash
								photoMainObj.attr('value', data.main.new.hash);
								photoObj.attr('data-media_hash', data.main.old.hash);
								
								// Swap photo
								if (data.main.old.src) {
									photoObj.find('img').attr('src', data.main.old.src);
								}
								else
									photoObj.hide();
								
								var uploader = photoMainObj.closest('.uploader.uploader-single');
								var uploaderThumbnail = uploader.find('.uploader-preview-placeholder.thumbnail');
								var img = uploaderThumbnail.find('img');
								if (!img.length) {
									img = $('<img>').appendTo(uploaderThumbnail);
								}
								img.attr('src', data.main.new.src);
								
								uploader.find('.uploader-preview-wrapper').addClass('hidden');
								uploader.find('.uploader-preview-placeholder-wrapper').addClass('hidden');
								uploader.find('.uploader-preview-delete').removeClass('hidden');
							}
						}
						else {
							photoObj.hide();
						}
						
					}
					
					modalContainer.modal('hide');
				}
				else {
					notifyError(data.error);
				}
			}
		});
		
		return false;
	},
	
	remove: function() {
		var self = this;
		var $this = $(this);
		yii.confirm($(this).data('message'), function() {
			appmake.fileinput_image_widget.sendData($this.data('action'), $this.parents('.fileinput-image-widget-item').data(), function () {
				$this.parents('.fileinput-image-widget-item').hide('slow').remove();
			});
		}, function() {});
		return false;
	},
	
	sendData: function (action, data, complete) {
		return $.ajax({
			url: action,
			type: 'POST',
			data: data,
			success: function (result) {
				if (result.success) {
					notifySuccess(result.success);
					complete();
				}
				else {
					notifyError(result.error);
				}
			}
		});
	},
	
	uploadSelect: function(event, numFiles, label) {
		var fileinput = $(event.target);
		var form = fileinput.closest('form');
		
		if (numFiles) {
			form.attr('fileinput-status', 'queue');
			form.off('submit').on('submit', function (event) {
				if (form.attr('fileinput-status') && (form.attr('fileinput-status') == 'queue')) {
					event.preventDefault();
					form.attr('fileinput-status', 'submit');
					$.scrollTo(fileinput.closest('fieldset'), 700, {
						onAfter: function() {
							fileinput.fileinput('upload');
						}
					});
				}
			});
		}
		else {
			form.removeAttr('fileinput-status', 'clear');
			form.unbind('submit');
		}
	},
	
	uploadSuccess: function (event, data, previewId, index) {
		var form = $(event.target).closest('form');
		
		var response = data.response;
		if (response.success) {
			notifySuccess(response.success);
			
			var itemPreview = $('#' + previewId);
			var itemPreviewParent = itemPreview.closest('.file-input');
			var ul = itemPreviewParent.parent().find('.fileinput-image-widget');
			
			$('#' + previewId).hide();
			
			$(response.html).appendTo(ul);
			
			$(event.target).fileinput('refresh').fileinput('enable');
			
			appmake.fileinput_image_widget.init();
			
			var formAttr = form.attr('fileinput-status');
			if (formAttr) {
				if (formAttr == 'queue')
					form.attr('fileinput-status', 'clear');
				else if (formAttr == 'submit') {
					form.attr('fileinpup-status', 'clear');
					form.submit();
				}
			}
		}
		else {
			notifyError(response.error);
		}
	},
}

appmake.fileinput_image_widget.init();