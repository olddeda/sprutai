/**
 * Comment plugin
 */
(function ($) {

    $.fn.comment = function (method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' + method + ' does not exist on jQuery.comment');
            return false;
        }
    };

    // Default settings
    var defaults = {
        // Comment actions buttons selector
        toolsSelector: '.comment-action-buttons',
		
        // Form selector
        formSelector: '#comment-form',
		
        // Form container selector
        formContainerSelector: '.comment-form-container',
		
        // Comment content selector
        contentSelector: '.comment-body',
		
        // Cancel reply button selector
        cancelReplyBtnSelector: '#cancel-reply',
		
        // Pjax container id
        pjaxContainerId: '#comment-pjax-container',
		
        // Pjax default settings
        pjaxSettings: {
            timeout: 10000,
            scrollTo: false,
            url: window.location.href
        }
    };

    // Methods
    var methods = {
        init: function (options) {
            return this.each(function () {
                var $commentForm = $(this);
                if ($commentForm.data('comment')) {
                    return;
                }
                var settings = $.extend({}, defaults, options || {});
                $commentForm.data('comment', settings);
                
                // Add events
                $commentForm.on('beforeSubmit.comment', beforeSubmitForm);
                
                var eventParams = {commentForm: $commentForm};
                $(settings.pjaxContainerId).on('click.comment', '[data-action="reply"]', eventParams, reply);
                $(settings.pjaxContainerId).on('click.comment', '[data-action="cancel-reply"]', eventParams, cancelReply);
				$(settings.pjaxContainerId).on('click.comment', '[data-action="update"]', eventParams, updateComment);
                $(settings.pjaxContainerId).on('click.comment', '[data-action="delete"]', eventParams, deleteComment);
            });
        },
        data: function () {
            return this.data('comment');
        },
        reset: function (settings) {
            $(settings.pjaxContainerId).each(function () {
                $(this).unbind('.comment');
                $(this).removeData('comment');
            });
            $(settings.formSelector).comment(settings);
        }
    };


    /**
     * This function used for `beforeSubmit` comment form event
     */
    function beforeSubmitForm() {
        var $commentForm = $(this);
        var settings = $commentForm.data('comment');
		var pjaxSettings = $.extend({container: settings.pjaxContainerId}, settings.pjaxSettings);
		
        // Add loading to comment button
		var textSending = $commentForm.data('sending') ? $commentForm.data('sending') : 'Loading...';
        $commentForm.find(':submit').prop('disabled', true).text(textSending);
        
        // Send post request
        $.post($commentForm.attr("action"), $commentForm.serialize(), function (data) {
         
        	// If success is status, then pjax container has been reloaded and comment form has been reset
            if (data.status == 'success') {
                $.pjax(pjaxSettings).done(function () {
                    $commentForm.find(':submit').prop('disabled', false).text('Comment');
                    $commentForm.trigger("reset");
                    
                    // Restart plugin
                    methods.reset.call($commentForm, settings);
                });
            }
           
            // If status is error, then only show form errors.
            else {
                if (data.hasOwnProperty('errors')) {
                    $commentForm.yiiActiveForm('updateMessages', data.errors, true);
                }
                else {
                    $commentForm.yiiActiveForm('updateAttribute', 'commentmodel-content', [data.message]);
                }
                $commentForm.find(':submit').prop('disabled', false).text('Comment');
            }
        });
        return false;
    }

    /**
     * Reply to comment
     * @param event
     */
    function reply(event) {
        event.preventDefault();
        
        var $commentForm = event.data.commentForm;
        var settings = $commentForm.data('comment');
        var $this = $(this);
        var parentCommentSelector = $this.parents('[data-comment-content-id="' + $this.data('comment-id') + '"]');
        
        // Move form to comment container
        $commentForm.appendTo(parentCommentSelector);
        
        // Update parentId field
        $commentForm.find('[data-comment="parent-id"]').val($this.data('comment-id'));
	
        // Reset id field
		$commentForm.find('[data-comment="id"]').val(null);
	
		// Focus comment field
		//var r = $R('#' + parentCommentSelector.find('textarea').attr('id'));
		//r.editor.focus();
        
        // Show cancel reply link
        $commentForm.find(settings.cancelReplyBtnSelector).addClass('show');
    }

    /**
     * Cancel reply
     * @param event
     */
    function cancelReply(event) {
        event.preventDefault();
        
        var $commentForm = event.data.commentForm;
        var settings = $commentForm.data('comment');
        
        $commentForm.find(settings.cancelReplyBtnSelector).removeClass('show');
        
        // Move form back to form container
        var formContainer = $(settings.pjaxContainerId).find(settings.formContainerSelector);
        $commentForm.prependTo(formContainer);
        
        // Reset parentId field
        $commentForm.find('[data-comment="parent-id"]').val(null);
	
		// Reset id field
		$commentForm.find('[data-comment="id"]').val(null);
	
		// Update button title
		var buttonSubmit = $commentForm.find('[type="submit"]');
		buttonSubmit.text(buttonSubmit.data('title-send'));
    }
	
	/**
	 * Update comment
	 * @param event
	 */
	function updateComment(event) {
		event.preventDefault();
		
		var $commentForm = event.data.commentForm;
		var settings = $commentForm.data('comment');
		var $this = $(this);
		var parentCommentSelector = $this.parents('[data-comment-content-id="' + $this.data('comment-id') + '"]');
		
		// Move form to comment container
		$commentForm.appendTo(parentCommentSelector);
		
		// Update parentId field
		$commentForm.find('[data-comment="parent-id"]').val($this.data('parent-id'));
		
		// Update id field
		$commentForm.find('[data-comment="id"]').val($this.data('comment-id'));
		
		// Set text
		var comment = parentCommentSelector.find('.comment-body').html();
		var r = $R('#' + parentCommentSelector.find('textarea').attr('id'));
		r.insertion.set(comment);
		
		// Show cancel reply link
		$commentForm.find(settings.cancelReplyBtnSelector).addClass('show');
		
		// Update button title
		var buttonSubmit = $commentForm.find('[type="submit"]');
		buttonSubmit.text(buttonSubmit.data('title-save'));
	}
    
    /**
     * Delete comment
     * @param event
     */
    function deleteComment(event) {
		event.preventDefault();

        var $commentForm = event.data.commentForm;
        var settings = $commentForm.data('comment');
        var $this = $(this);

		var ok = function() {
			$.ajax({
				url: $this.data('url'),
				type: 'DELETE',
				error: function (xhr, status, error) {
					alert(error);
				},
				success: function (result, status, xhr) {
					$this.parents('[data-comment-content-id="' + $this.data('comment-id') + '"]').find(settings.contentSelector).text(result);
					$this.parents(settings.toolsSelector).remove();
				}
			});
		}
		
		yii.confirm($this.data('comment-confirm'), function() {
			$.ajax({
				url: $this.data('url'),
				type: 'DELETE',
				error: function (xhr, status, error) {
					alert(error);
				},
				success: function (result, status, xhr) {
					$this.parents('[data-comment-content-id="' + $this.data('comment-id') + '"]').find(settings.contentSelector).text(result);
					$this.parents(settings.toolsSelector).remove();
				}
			});
		});
    }

})(window.jQuery);
