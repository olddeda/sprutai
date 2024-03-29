﻿(function (jQuery) {

    jQuery.saveimages = function (element, options) {

        var defaults = {
            handler: 'saveimage.php',
            onComplete: function (result) { },
			customval: ''
        };

        this.settings = {};

        var $element = jQuery(element),
                element = element;

        this.init = function () {

            this.settings = jQuery.extend({}, defaults, options);

        };

        this.save = function (s) {

            var handler = this.settings.handler;
			var customval = this.settings.customval;

			//Get quality info (from content builder plugin)
			var hiquality = false;
            try {
                hiquality = $element.data('contentbuilder').settings.hiquality;
            } catch (e) { };

            var count = 0;

            //Check all images
            $element.find('img').not('#divCb img').each(function () {
                var $this = jQuery(this);

                //Find base64 images
                if ($this.attr('src').indexOf('base64') != -1) {

                    count++;

                    //Read image (base64 string)
                    var image = $this.attr('src');
                    image = image.replace(/^data:image\/(png|jpeg);base64,/, "");

                    //Prepare form to submit image
                    if (jQuery('#form-' + count).length == 0) {
                        var s = '<form id="form-' + count + '" target="frame-' + count + '" method="post" enctype="multipart/form-data">' +
                        '<input id="hidimg-' + count + '" name="hidimg-' + count + '" type="hidden" />' +
                        '<input id="hidname-' + count + '" name="hidname-' + count + '" type="hidden" />' +
                        '<input id="hidtype-' + count + '" name="hidtype-' + count + '" type="hidden" />' +
						'<input id="hidcustomval-' + count + '" name="hidcustomval-' + count + '" type="hidden" />' +
                        '<iframe id="frame-' + count + '" name="frame-' + count + '" style="width:1px;height:1px;border:none;visibility:hidden;position:absolute"></iframe>' +
                    '</form>';
                        jQuery('body').append(s);
                    }

                    //Give ID to image
                    $this.attr('id', 'img-' + count);

                    //Set hidden field with image (base64 string) to be submitted
                    jQuery('#hidimg-' + count).val(image);
					
					//Set hidden field with custom value to be submitted
                    jQuery('#hidcustomval-' + count).val(customval);

                    //Set hidden field with file name to be submitted
                    var filename = '';
                    if ($this.data('filename') != undefined) {
                        filename = $this.data('filename'); //get filename data from the imagemebed plugin
                    }
                    var filename_without_ext = filename.substr(0, filename.lastIndexOf('.')) || filename;
                    filename_without_ext = filename_without_ext.toLowerCase().replace(/ /g, '-');
                    jQuery('#hidname-' + count).val(filename_without_ext);

                    //Set hidden field with file extension to be submitted
                    if (hiquality) {
                        //If high quality is set true, set image as png
                        jQuery('#hidtype-' + count).val('png'); //high quality
                    } else {
                        //If high quality is set false, depend on image extension
                        var extension = filename.substr((filename.lastIndexOf('.') + 1));
                        extension = extension.toLowerCase();
                        if (extension == 'jpg' || extension == 'jpeg') {
                            jQuery('#hidtype-' + count).val('jpg');
                        } else {
                            jQuery('#hidtype-' + count).val('png');
                        }
                    }

                    // Submit form
                    var url =  handler + '?count=' + count;
                    $.ajax({
                        url: url,
                        type: 'POST',
                        dataType: 'json',
                        data: new FormData(jQuery('#form-' + count)[0]),
                        processData: false,
                        contentType: false,
                        success: function(result) {
                            console.log(result);
                            if (result.url) {
                                $this.attr('src', result.url);
                            }
                            else {
                                alert(result.error);
                                $element.data('saveimages').settings.onComplete(false);
                            }
                        },
                        error: function(XMLHttpRequest, textStatus, errorThrown) {
                            alert("Status: " + textStatus);
                            $element.data('saveimages').settings.onComplete(false);
                        }
                    });
                }
            });

            //Check per 2 sec if all images have been changed with the new saved images.
            var int = setInterval(function () {

                var finished = true;
                $element.find('img').not('#divCb img').each(function () {
                    if (jQuery(this).attr('src').indexOf('base64') != -1) { //if there is still base64 image, means not yet finished.
                        finished = false;
                    }
                });

                if (finished) {
                    window.clearInterval(int);

                    // remove unused forms (previously used for submitting images)
                    for (var i = 1; i <= count; i++) {
                        jQuery('#form-' + i).remove();
                    }

                    $element.data('saveimages').settings.onComplete(true);
                }
            }, 2000);

        };

        this.init();

    };

    jQuery.fn.saveimages = function (options) {

        return this.each(function () {

            if (undefined == jQuery(this).data('saveimages')) {
                var plugin = new jQuery.saveimages(this, options);
                jQuery(this).data('saveimages', plugin);

            }

        });
    };
})(jQuery);